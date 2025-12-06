<#
run_all.ps1 - E-Shop Tenisiek Startup Script
Usage: .\run_all.ps1 [-Reset] [-OpenBrowser]
#>
param(
    [switch]$Reset = $false,
    [switch]$OpenBrowser = $false
)

$ErrorActionPreference = "Continue"
Set-Location (Split-Path $MyInvocation.MyCommand.Path)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   E-Shop Tenisiek - Spustenie" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check Docker
Write-Host "[1/4] Kontrolujem Docker..." -ForegroundColor Yellow
$dockerCheck = docker version 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "CHYBA: Docker nie je spusteny!" -ForegroundColor Red
    Write-Host "Spustite Docker Desktop a skuste znova." -ForegroundColor Red
    exit 1
}
Write-Host "Docker OK" -ForegroundColor Green

# Reset if requested
if ($Reset) {
    Write-Host ""
    Write-Host "Reset: Mazem kontajnery a volumes..." -ForegroundColor Yellow
    docker-compose down -v 2>&1 | Out-Null
}

# Start containers
Write-Host ""
Write-Host "[2/4] Spustam kontajnery..." -ForegroundColor Yellow
docker-compose up --build -d 2>&1

# Wait for DB
Write-Host ""
Write-Host "[3/4] Cakam na databazu..." -ForegroundColor Yellow
$maxAttempts = 30
$attempt = 0
while ($attempt -lt $maxAttempts) {
    $attempt++
    Start-Sleep -Seconds 2
    $dbCheck = docker-compose exec -T db mysqladmin ping -uroot -pexample --silent 2>&1
    if ($dbCheck -match "alive" -or $LASTEXITCODE -eq 0) {
        Write-Host "Databaza je pripravena!" -ForegroundColor Green
        break
    }
    Write-Host "  Pokus $attempt/$maxAttempts..." -ForegroundColor Gray
}

# Verify API
Write-Host ""
Write-Host "[4/4] Overujem API..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080/api/products.php" -UseBasicParsing -TimeoutSec 10
    $json = $response.Content | ConvertFrom-Json
    if ($json.ok) {
        Write-Host "API funguje! Produktov v DB: $($json.data.Count)" -ForegroundColor Green
    }
} catch {
    Write-Host "API zatial nereaguje, ale kontajnery bezia." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "   HOTOVO!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "E-Shop:        http://localhost:8080"
Write-Host "Admin Login:   http://localhost:8080/admin_login.html"
Write-Host "Login stranka: http://localhost:8080/login.html"
Write-Host ""

if ($OpenBrowser) {
    Start-Process "http://localhost:8080"
}
