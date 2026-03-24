@echo off
echo ============================================
echo Fixing Payment Tables - Adding Missing Columns
echo ============================================
echo.

"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < C:\Project\restoopncode\database\fix-payment-tables.sql

echo.
echo ============================================
echo Done! Check output above for any errors.
echo ============================================
pause
