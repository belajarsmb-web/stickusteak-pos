@echo off
REM ============================================
REM Restaurant POS - Laragon Quick Setup
REM ============================================

echo.
echo ========================================
echo  Restaurant POS - Laragon Setup
echo ========================================
echo.

REM Check if Laragon exists
if not exist "C:\laragon\www" (
    echo [ERROR] Laragon not found at C:\laragon
    echo.
    pause
    exit /b 1
)

echo [OK] Laragon detected
echo.

REM Create folder structure
echo [1/3] Creating folder structure...
if not exist "C:\laragon\www\pos\public" (
    mkdir C:\laragon\www\pos\public
    echo [OK] Created C:\laragon\www\pos\public
) else (
    echo [OK] Folder exists
)

REM Copy build files
echo [2/3] Copying frontend build files...
xcopy /E /I /Y /Q C:\Project\restoopncode\frontend\build C:\laragon\www\pos\public
echo [OK] Files copied to C:\laragon\www\pos\public

REM Copy .htaccess
echo [3/3] Copying .htaccess...
copy /Y C:\Project\restoopncode\frontend\build\.htaccess C:\laragon\www\pos\public\.htaccess
echo [OK] .htaccess copied

echo.
echo ========================================
echo  Setup Complete!
echo ========================================
echo.
echo Next Steps:
echo.
echo 1. Restart Laragon:
echo    - Open Laragon
echo    - Click "Stop All"
echo    - Click "Start All"
echo.
echo 2. Start Backend Server:
echo    - Backend already running at: http://localhost:3001
echo.
echo 3. Access Application:
echo    - Frontend:  http://pos.test
echo    - Backend:   http://localhost:3001/api
echo    - API Docs:  http://localhost:3001/api-docs
echo.
echo ========================================
echo.

REM Ask to open Laragon
set /p OPEN_LARAGON="Open Laragon now? (Y/N): "
if /i "%OPEN_LARAGON%"=="Y" (
    start "" "C:\laragon\laragon.exe"
)

pause
