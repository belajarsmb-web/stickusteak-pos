@echo off
echo Running restore script...
powershell -ExecutionPolicy Bypass -File "%~dp0restore-backup.ps1"
pause
