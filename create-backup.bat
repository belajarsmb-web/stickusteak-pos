@echo off
setlocal EnableDelayedExpansion

echo ============================================
echo Project Backup Creator
echo ============================================
echo.

REM Get current date and time using PowerShell
for /f "delims=" %%I in ('powershell -Command "Get-Date -Format 'yyyyMMdd-HHmmss'"') do set TIMESTAMP=%%I

echo Backup timestamp: !TIMESTAMP!
echo.

REM Set project directory
set PROJECT_DIR=C:\Project\restoopncode
set BACKUP_DIR=C:\Project\restoopncode-backups

REM Create backup directory if not exists
if not exist "%BACKUP_DIR%" (
    echo Creating backup directory: %BACKUP_DIR%
    mkdir "%BACKUP_DIR%"
)

REM Set backup file name
set BACKUP_FILE=%BACKUP_DIR%\BACKUP_%TIMESTAMP%_restoopncode.zip

echo Creating backup...
echo Source: %PROJECT_DIR%
echo Destination: %BACKUP_FILE%
echo.

echo Compressing files (excluding temporary files)...
powershell -Command "$exclude = @('*.log', '*.tmp', '*.bak', 'node_modules', '.git', '.vscode', '*.zip', 'backups'); $files = Get-ChildItem -Path '%PROJECT_DIR%' -Recurse -File | Where-Object { $_.FullName -notmatch 'node_modules|.git|.vscode|backups' -and $exclude -notcontains $_.Extension }; Compress-Archive -Path $files.FullName -DestinationPath '%BACKUP_FILE%' -Force"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo Backup completed successfully!
    echo ============================================
    echo Backup file: %BACKUP_FILE%
    
    REM Get file size
    for %%A in ("%BACKUP_FILE%") do set SIZE=%%~zA
    set /a SIZE_MB=%SIZE%/1048576
    echo File size: approximately %SIZE_MB% MB
    echo.
    
    REM List recent backups
    echo Recent backups (last 5):
    dir /b /o-d "%BACKUP_DIR%\*.zip" 2>nul | findstr /n "^" | findstr "^[1-5]:"
    echo.
) else (
    echo.
    echo ============================================
    echo ERROR: Backup failed!
    echo ============================================
    echo Please check if PowerShell has permission to create ZIP files
    echo.
)

echo.
echo Press any key to exit...
pause >nul
