@echo off
echo Running backup script...
powershell -ExecutionPolicy Bypass -File "%~dp0create-backup.ps1"
pause
