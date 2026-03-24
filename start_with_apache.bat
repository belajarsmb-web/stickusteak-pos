@echo off
REM ============================================
REM Restaurant POS - Apache Quick Start Script
REM ============================================

echo.
echo ========================================
echo  Restaurant POS - Apache Setup
echo ========================================
echo.

REM Check if XAMPP is installed
if exist "C:\xampp\apache\bin\httpd.exe" (
    set APACHE_PATH=C:\xampp
    set APACHE_TYPE=XAMPP
) else if exist "C:\Apache24\bin\httpd.exe" (
    set APACHE_PATH=C:\Apache24
    set APACHE_TYPE=Standalone
) else (
    echo [ERROR] Apache not found!
    echo.
    echo Please install one of the following:
    echo   1. XAMPP - Download from https://www.apachefriends.org/
    echo   2. Apache - Download from https://www.apachehaus.com/
    echo.
    pause
    exit /b 1
)

echo [OK] Apache detected: %APACHE_TYPE% at %APACHE_PATH%
echo.

REM Start Backend Server
echo [1/4] Starting Backend Server...
start "Restaurant POS - Backend" cmd /k "cd /d C:\Project\restoopncode\backend && npm run start:dev"
timeout /t 3 /nobreak >nul

REM Copy Build Files
echo [2/4] Copying Frontend Build Files...
if "%APACHE_TYPE%"=="XAMPP" (
    set DEST_PATH=C:\xampp\htdocs\pos
) else (
    set DEST_PATH=C:\Apache24\htdocs\pos
)

if not exist "%DEST_PATH%" mkdir "%DEST_PATH%"
xcopy /E /I /Y /Q C:\Project\restoopncode\frontend\build "%DEST_PATH%"
echo [OK] Files copied to %DEST_PATH%

REM Configure Apache
echo [3/4] Configuring Apache...
if "%APACHE_TYPE%"=="XAMPP" (
    echo Copy pos.conf to: %APACHE_PATH%\apache\conf\extra\
    copy /Y C:\Project\restoopncode\docker\pos.conf %APACHE_PATH%\apache\conf\extra\
    
    echo.
    echo [IMPORTANT] Manual Steps Required:
    echo.
    echo 1. Edit httpd.conf and uncomment these lines:
    echo    LoadModule rewrite_module modules/mod_rewrite.so
    echo    LoadModule proxy_module modules/mod_proxy.so
    echo    LoadModule proxy_http_module modules/mod_proxy_http.so
    echo.
    echo 2. Add this line to include the config:
    echo    Include conf/extra/pos.conf
    echo.
    echo 3. Start Apache from XAMPP Control Panel
    echo.
) else (
    echo Copying config to: %APACHE_PATH%\conf\extra\
    copy /Y C:\Project\restoopncode\docker\pos.conf %APACHE_PATH%\conf\extra\
    
    echo.
    echo [IMPORTANT] Manual Steps Required:
    echo.
    echo 1. Edit httpd.conf and uncomment these lines:
    echo    LoadModule rewrite_module modules/mod_rewrite.so
    echo    LoadModule proxy_module modules/mod_proxy.so
    echo    LoadModule proxy_http_module modules/mod_proxy_http.so
    echo.
    echo 2. Add this line to include the config:
    echo    Include conf/extra/pos.conf
    echo.
    echo 3. Start Apache:
    echo    %APACHE_PATH%\bin\httpd.exe -k start
    echo.
)

REM Open Documentation
echo [4/4] Opening Setup Guide...
start notepad C:\Project\restoopncode\docker\APACHE_SETUP.md

echo.
echo ========================================
echo  Setup Complete!
echo ========================================
echo.
echo Next Steps:
echo 1. Complete the manual Apache configuration above
echo 2. Start Apache
echo 3. Access the application:
echo    - Frontend:  http://localhost
echo    - Backend:   http://localhost:3001/api
echo    - API Docs:  http://localhost:3001/api-docs
echo.
pause
