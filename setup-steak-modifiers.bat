@echo off
echo ============================================
echo Setup Steak Modifiers
echo ============================================
echo.
echo Running SQL to create modifier groups...
echo.
cd /d C:\Project\restoopncode
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < database\setup-steak-modifiers.sql
echo.
echo ============================================
echo Setup Complete!
echo ============================================
pause
