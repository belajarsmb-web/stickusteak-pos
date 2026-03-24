@echo off
echo ============================================
echo Running Simple Sample Inventory Data
echo ============================================
echo.

"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < C:\Project\restoopncode\database\sample-inventory-simple.sql

echo.
echo ============================================
echo Done!
echo ============================================
pause
