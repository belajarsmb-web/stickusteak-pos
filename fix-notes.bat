@echo off
echo Fixing notes encoding...
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < "C:\Project\restoopncode\database\fix-notes.sql"
pause
