@echo off
echo ============================================
echo Delete All Transactions
echo ============================================
echo.
echo WARNING: This will delete ALL orders, payments, and order items!
echo.
set /p CONFIRM="Are you sure? Type YES to confirm: "
if "%CONFIRM%" NEQ "YES" (
    echo Cancelled.
    pause
    exit /b
)
echo.
echo Deleting all transactions...
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root posreato < C:\Project\restoopncode\database\delete-all-transactions.sql
echo.
echo ============================================
echo Done! All transactions deleted.
echo ============================================
pause
