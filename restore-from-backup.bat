@echo off
setlocal EnableDelayedExpansion

echo ============================================
echo Project Restore from Backup
echo ============================================
echo.
echo WARNING: This will restore the project from a backup!
echo All current changes will be overwritten.
echo.
pause

REM Set directories
set PROJECT_DIR=C:\Project\restoopncode
set BACKUP_DIR=C:\Project\restoopncode-backups

echo Available backups:
echo ============================================
dir /b /o-d "%BACKUP_DIR%\*.zip" | findstr /n "^" 
echo.

set /p BACKUP_FILE="Enter backup file name (or number): "

REM Check if user entered a number
echo %BACKUP_FILE% | findstr "^[0-9]$" >nul
if %ERRORLEVEL% EQU 0 (
    REM Get the nth backup file
    for /f "tokens=%BACKUP_FILE% delims=" %%A in ('dir /b /o-d "%BACKUP_DIR%\*.zip"') do set BACKUP_FILE=%%A
)

set BACKUP_PATH=%BACKUP_DIR%\%BACKUP_FILE%

if not exist "%BACKUP_PATH%" (
    echo.
    echo ERROR: Backup file not found: %BACKUP_PATH%
    pause
    exit /b 1
)

echo.
echo Restoring from: %BACKUP_PATH%
echo To: %PROJECT_DIR%
echo.
set /p CONFIRM="Are you sure? Type YES to confirm: "
if "%CONFIRM%" NEQ "YES" (
    echo Restore cancelled.
    pause
    exit /b
)

echo.
echo Extracting backup...
powershell -Command "Expand-Archive -Path '%BACKUP_PATH%' -DestinationPath '%PROJECT_DIR%' -Force"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo Restore completed successfully!
    echo ============================================
    echo Please restart your web server if running.
    echo.
) else (
    echo.
    echo ============================================
    echo ERROR: Restore failed!
    echo ============================================
    echo Please check if files are not in use.
    echo.
)

pause
