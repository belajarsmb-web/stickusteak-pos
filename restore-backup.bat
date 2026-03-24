@echo off
REM ============================================
REM Restoopncode - Restore from Backup
REM ============================================

echo.
echo ========================================
echo  Restore from Backup
echo ========================================
echo.
echo Available backups:
echo.

dir /b C:\Project\restoopncode\backups\*.zip | find /i "BACKUP"

echo.
set /p BACKUP_FILE="Enter backup filename to restore (without .zip extension): "

if not exist "C:\Project\restoopncode\backups\%BACKUP_FILE%.zip" (
    echo [ERROR] Backup file not found!
    pause
    exit /b 1
)

echo.
echo [WARNING] This will overwrite current php-native folder!
set /p CONFIRM="Are you sure? (Y/N): "

if /i "%CONFIRM%" NEQ "Y" (
    echo [CANCELLED] Restore cancelled.
    pause
    exit /b 0
)

echo.
echo [INFO] Restoring from %BACKUP_FILE%.zip...

REM Backup current version first
echo [INFO] Creating backup of current version...
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime ^| find "."') do set datetime=%%a
set "TIMESTAMP=%datetime:~0,4%%datetime:~4,2%%datetime:~6,2%_%datetime:~8,2%%datetime:~10,2%"

powershell -Command "if (Test-Path 'C:\Project\restoopncode\php-native') { Compress-Archive -Path 'C:\Project\restoopncode\php-native' -DestinationPath 'C:\Project\restoopncode\backups\PRE_RESTORE_%TIMESTAMP%.zip' -Force }"

REM Remove current php-native
if exist "C:\Project\restoopncode\php-native" (
    echo [INFO] Removing current php-native folder...
    rmdir /s /q C:\Project\restoopncode\php-native
)

REM Extract backup
echo [INFO] Extracting backup...
powershell -Command "Expand-Archive -Path 'C:\Project\restoopncode\backups\%BACKUP_FILE%.zip' -DestinationPath 'C:\Project\restoopncode' -Force"

echo.
echo ========================================
echo  Restore Completed Successfully!
echo ========================================
echo.
echo Restored from: %BACKUP_FILE%.zip
echo Previous version saved as: PRE_RESTORE_%TIMESTAMP%.zip
echo.
echo Please refresh your browser to see changes.
echo.

pause
