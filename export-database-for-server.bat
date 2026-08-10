@echo off
REM Export local WAMP database for upload to StackCP phpMyAdmin.
REM Output: database-export.sql in project root.

set MYSQL="D:\wamp\bin\mysql\mysql8.0.31\bin\mysqldump.exe"
if not exist %MYSQL% (
  echo Looking for mysqldump...
  for /d %%i in ("D:\wamp\bin\mysql\mysql*") do set MYSQL="%%i\bin\mysqldump.exe"
)

echo Exporting local database "kts"...
%MYSQL% -u root --password= --databases kts --single-transaction --routines --triggers --default-character-set=utf8mb4 > "%~dp0database-export.sql"

if %ERRORLEVEL% NEQ 0 (
  echo Export failed. Update MYSQL path/password in this script.
  pause
  exit /b 1
)

echo Done: %~dp0database-export.sql
echo.
echo Next: In StackCP phpMyAdmin, open database wordpress-35303337d41d
echo and Import this SQL file. Then rename DB usage is already set in .env.production
echo ^(tables import into the StackCP database name automatically if you edit the SQL^)
echo.
echo IMPORTANT: Open database-export.sql and replace:
echo   USE `kts`;
echo with:
echo   USE `wordpress-35303337d41d`;
echo or remove the USE line before importing.
pause
