@echo off
echo Running backup cleanup...
powershell -ExecutionPolicy Bypass -File "%~dp0cleanup-old-backups.ps1"
pause
