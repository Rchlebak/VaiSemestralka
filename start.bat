@echo off
echo ========================================
echo    E-Shop Tenisiek - Laravel MVC
echo ========================================
echo.

cd /d "%~dp0"

echo [1/5] Kontrolujem Docker...
docker version >nul 2>&1
if errorlevel 1 (
    echo CHYBA: Docker nie je spusteny. Spustite Docker Desktop a skuste znova.
    pause
    exit /b 1
)
echo Docker OK

echo.
echo [2/5] Spustam kontajnery...
docker-compose up --build -d
if errorlevel 1 (
    echo CHYBA: Nepodarilo sa spustit kontajnery.
    pause
    exit /b 1
)

echo.
echo [3/5] Cakam na databazu (20 sekund)...
timeout /t 20 /nobreak >nul

echo.
echo [4/5] Spustam migracie...
docker exec vaiexperiment-web-1 php artisan config:clear >nul 2>&1
docker exec vaiexperiment-web-1 php artisan cache:clear >nul 2>&1
docker exec vaiexperiment-web-1 php artisan migrate --force >nul 2>&1

echo.
echo [5/5] Seedujem data...
docker exec vaiexperiment-web-1 php artisan db:seed --force >nul 2>&1

echo.
echo ========================================
echo    HOTOVO!
echo ========================================
echo.
echo E-Shop:        http://localhost:8000
echo Admin Login:   http://localhost:8000/admin/login
echo phpMyAdmin:    http://localhost:8081
echo.
echo Admin heslo: admin123
echo.
echo Stlacte lubovolnu klavesu pre otvorenie v prehliadaci...
pause >nul

start http://localhost:8000

