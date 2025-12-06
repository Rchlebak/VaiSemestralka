@echo off
cd /d "%~dp0"
echo Starting E-Shop...

echo [1] Starting Docker containers...
docker-compose up -d

echo [2] Waiting for database...
timeout /t 5 /nobreak > nul

echo [3] Running migrations...
docker-compose exec -T db mysql -uroot -pexample eshop -e "ALTER TABLE products ADD COLUMN IF NOT EXISTS image_url VARCHAR(500) DEFAULT NULL;"

echo [4] Done! Opening browser...
start http://localhost:8080/admin_login.html

echo.
echo ================================
echo E-Shop is running!
echo Admin: http://localhost:8080/admin_login.html
echo Login: admin / admin123
echo ================================
pause

