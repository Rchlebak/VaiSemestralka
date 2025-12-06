@echo off
echo ========================================
echo    E-Shop Tenisiek - Spustenie
echo ========================================
echo.

cd /d "%~dp0"

echo [1/4] Kontrolujem Docker...
docker version >nul 2>&1
if errorlevel 1 (
    echo CHYBA: Docker nie je spusteny. Spustite Docker Desktop a skuste znova.
    pause
    exit /b 1
)
echo Docker OK

echo.
echo [2/4] Spustam kontajnery...
docker-compose up --build -d
if errorlevel 1 (
    echo CHYBA: Nepodarilo sa spustit kontajnery.
    pause
    exit /b 1
)

echo.
echo [3/4] Cakam na databazu (30 sekund)...
timeout /t 30 /nobreak >nul

echo.
echo [4/4] Inicializujem databazu...
docker-compose exec -T db mysql -uroot -pexample -e "SELECT 1" >nul 2>&1
if errorlevel 1 (
    echo Databaza este nie je pripravena, cakam...
    timeout /t 10 /nobreak >nul
)

echo.
echo ========================================
echo    HOTOVO!
echo ========================================
echo.
echo E-Shop:        http://localhost:8080
echo Admin Login:   http://localhost:8080/admin_login.html
echo Login stranka: http://localhost:8080/login.html
echo.
echo Stlacte lubovolnu klavesu pre otvorenie v prehliadaci...
pause >nul

start http://localhost:8080

