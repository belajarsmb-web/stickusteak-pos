@echo off
setlocal EnableDelayedExpansion

echo ============================================
echo Project Backup Creator
echo ============================================
echo.

REM Get current date and time for prefix
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set YYYY=%datetime:~0,4%
set MM=%datetime:~4,2%
set DD=%datetime:~6,2%
set HH=%datetime:~8,2%
set Min=%datetime:~10,2%
set SS=%datetime:~12,2%

set TIMESTAMP=%YYYY%%MM%%DD-%HH%%Min%%SS%
set BACKUP_PREFIX=BACKUP_%TIMESTAMP%

echo Backup timestamp: %YYYY%-%MM%-%DD% %HH%:%Min%:%SS%
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
set BACKUP_FILE=%BACKUP_DIR%\%BACKUP_PREFIX%_restoopncode.zip

echo Creating backup...
echo Source: %PROJECT_DIR%
echo Destination: %BACKUP_FILE%
echo.

REM Exclude patterns (temporary files, logs, node_modules, etc.)
set EXCLUDE_FILES=*.log *.tmp *.bak node_modules\ .git\ .vscode\ *.zip backups\

echo Compressing files (excluding temporary files)...
powershell -Command "$exclude = @('*.log', '*.tmp', '*.bak', 'node_modules', '.git', '.vscode', '*.zip', 'backups'); $files = Get-ChildItem -Path '%PROJECT_DIR%' -Recurse -Exclude $exclude | Where-Object { $_.FullName -notmatch 'node_modules|.git|.vscode|backups' }; Compress-Archive -Path $files.FullName -DestinationPath '%BACKUP_FILE%' -Force"

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
    echo Recent backups:
    dir /b /o-d "%BACKUP_DIR%\*.zip" | findstr /n "^" | findstr "^[1-5]:"
    echo.
) else (
    echo.
    echo ============================================
    echo ERROR: Backup failed!
    echo ============================================
    echo Please check if PowerShell has permission to create ZIP files
    echo.
)

echo Press any key to exit...
pause >nul
