@echo off
echo ============================================
echo Running Sample Inventory & Recipe Data
echo ============================================
echo.

"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < C:\Project\restoopncode\database\sample-inventory-recipes.sql

echo.
echo ============================================
echo Done! Check messages above.
echo ============================================
echo.
pause
