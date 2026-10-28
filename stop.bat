@echo off
REM Stoppt ScrapsRoulette: Laravel-Backend (Port 8650) und Vite-Frontend (Port 8651).
cd /d "%~dp0"

for %%P in (8650 8651) do (
    for /f "tokens=5" %%I in ('netstat -ano ^| findstr /R /C:":%%P  *0\.0\.0\.0:0 " /C:":%%P  *\[::\]:0 "') do (
        echo Beende Prozess %%I auf Port %%P
        taskkill /PID %%I /T /F >nul 2>&1
    )
)

taskkill /FI "WINDOWTITLE eq ScrapsRoulette Backend*" /T /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq ScrapsRoulette Frontend*" /T /F >nul 2>&1

if exist public\hot del public\hot

echo ScrapsRoulette wurde gestoppt.
