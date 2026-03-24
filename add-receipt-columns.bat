@echo off
echo ============================================
echo Add Receipt Template Columns
echo ============================================
echo.
cd /d C:\Project\restoopncode
echo Adding columns to database...
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < database\add-receipt-columns.sql
echo.
echo ============================================
echo Done! Columns added successfully.
echo ============================================
echo.
pause
