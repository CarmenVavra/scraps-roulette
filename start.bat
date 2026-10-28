@echo off
REM Startet ScrapsRoulette: Laravel-Backend (Port 8650) und Vite-Frontend (Port 8651).
cd /d "%~dp0"

if not exist vendor (
    echo Installiere PHP-Abhaengigkeiten ...
    call composer install
)
if not exist node_modules (
    echo Installiere Node-Abhaengigkeiten ...
    call npm install
)
if not exist .env (
    copy .env.example .env >nul
    php artisan key:generate
)

start "ScrapsRoulette Backend" cmd /k php artisan serve --host=127.0.0.1 --port=8650
start "ScrapsRoulette Frontend" cmd /k npm run dev

echo.
echo ScrapsRoulette laeuft auf http://localhost:8650
timeout /t 3 /nobreak >nul
start "" http://localhost:8650
