<#
.SYNOPSIS
    Spúšťací skript pre Laravel E-Shop Tenisiek
.DESCRIPTION
    Tento skript spustí celú aplikáciu vrátane Docker kontajnerov,
    databázových migrácií a seedovania testovacích dát.
#>

param(
    [switch]$ForceSeed,
    [switch]$OpenBrowser,
    [switch]$RebuildContainers
)

$ErrorActionPreference = "Continue"
$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  E-Shop Tenisiek - Laravel MVC" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# 1. Kontrola Docker
Write-Host "[1/5] Kontrolujem Docker..." -ForegroundColor Yellow
$dockerRunning = docker info 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "CHYBA: Docker nie je spusteny. Spustite Docker Desktop." -ForegroundColor Red
    exit 1
}
Write-Host "Docker je OK" -ForegroundColor Green

# 2. Spustenie kontajnerov
Write-Host ""
Write-Host "[2/5] Spustam kontajnery..." -ForegroundColor Yellow

if ($RebuildContainers) {
    docker-compose -f "$projectPath\docker-compose.yml" down -v 2>&1
    docker-compose -f "$projectPath\docker-compose.yml" build --no-cache 2>&1
}

docker-compose -f "$projectPath\docker-compose.yml" up -d --build 2>&1

if ($LASTEXITCODE -ne 0) {
    Write-Host "CHYBA: Nepodarilo sa spustit kontajnery" -ForegroundColor Red
    exit 1
}
Write-Host "Kontajnery spustene" -ForegroundColor Green

# 3. Cakanie na databazu
Write-Host ""
Write-Host "[3/5] Cakam na databazu..." -ForegroundColor Yellow
$maxAttempts = 30
$attempt = 0
$dbReady = $false

while ($attempt -lt $maxAttempts -and -not $dbReady) {
    $attempt++
    Write-Host "  Pokus $attempt / $maxAttempts..." -NoNewline

    $result = docker-compose -f "$projectPath\docker-compose-laravel.yml" exec -T db mysqladmin ping -uroot -pexample 2>&1

    if ($result -match "mysqld is alive") {
        $dbReady = $true
        Write-Host " OK" -ForegroundColor Green
    } else {
        Write-Host " cakam..." -ForegroundColor Yellow
        Start-Sleep -Seconds 2
    }
}

if (-not $dbReady) {
    Write-Host "CHYBA: Databaza sa nespustila vcas" -ForegroundColor Red
    exit 1
}
Write-Host "Databaza je pripravena" -ForegroundColor Green

# 4. Spustenie migracii
Write-Host ""
Write-Host "[4/5] Spustam databazove migracie..." -ForegroundColor Yellow

docker-compose -f "$projectPath\docker-compose-laravel.yml" exec -T web php artisan migrate --force 2>&1

if ($LASTEXITCODE -ne 0) {
    Write-Host "Varovanie: Migracie mohli zlyhat (mozno uz existuju tabulky)" -ForegroundColor Yellow
}

Write-Host "Migracie dokoncene" -ForegroundColor Green

# 5. Seedovanie dat
Write-Host ""
Write-Host "[5/5] Kontrolujem testovacie data..." -ForegroundColor Yellow

$productCount = docker-compose -f "$projectPath\docker-compose-laravel.yml" exec -T db mysql -uroot -pexample -N -e "SELECT COUNT(*) FROM eshop.products" 2>&1

if ($ForceSeed -or $productCount -match "^0$" -or $productCount -match "ERROR") {
    Write-Host "Seedujem testovacie data..." -ForegroundColor Yellow
    docker-compose -f "$projectPath\docker-compose-laravel.yml" exec -T web php artisan db:seed --force 2>&1
    Write-Host "Data naplnene" -ForegroundColor Green
} else {
    Write-Host "Data uz existuju (produktov: $productCount)" -ForegroundColor Green
}

# Hotovo
Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  E-Shop je pripraveny!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "  Webova stranka:  http://localhost:8000" -ForegroundColor Cyan
Write-Host "  Admin panel:     http://localhost:8000/admin/login" -ForegroundColor Cyan
Write-Host "  phpMyAdmin:      http://localhost:8081" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Admin prihlasenie:" -ForegroundColor White
Write-Host "    Heslo: admin123" -ForegroundColor White
Write-Host ""

# Otvorenie prehliadaca
if ($OpenBrowser) {
    Start-Process "http://localhost:8000"
}

Write-Host "Pre zastavenie: docker-compose -f docker-compose-laravel.yml down" -ForegroundColor Gray

