@echo off
echo ============================================
echo Update Receipt Header Information
echo ============================================
echo.
echo This will update receipt header (name, address, phone)
echo.

set /p NAME="Enter restaurant name (e.g., Stickusteak): "
set /p ADDRESS="Enter address (e.g., SouthCity, Jakarta): "
set /p PHONE="Enter phone number (e.g., 08123456789): "

echo.
echo Updating receipt header...
echo Name: %NAME%
echo Address: %ADDRESS%
echo Phone: %PHONE%
echo.

"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato -e "USE posreato; UPDATE receipt_templates SET header_text = '%NAME%', address = '%ADDRESS%', phone = '%PHONE%' WHERE is_default = 1; UPDATE outlets SET name = '%NAME%', address = '%ADDRESS%', phone = '%PHONE%' WHERE id = 1; SELECT 'Receipt header updated successfully!' AS status;"

echo.
echo ============================================
echo Done! Receipt header updated.
echo ============================================
pause
