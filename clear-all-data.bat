@echo off
echo ============================================
echo Delete All Transactions
echo ============================================
echo.
echo Deleting all transactions...
echo.
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < "C:\Project\restoopncode\database\delete-all-transactions.sql"
echo.
echo ============================================
echo Done! All transactions deleted.
echo ============================================
pause
