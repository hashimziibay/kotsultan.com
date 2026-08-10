@echo off
title KTS Web Project Server
echo Starting PHP CodeIgniter Server and Tailwind CSS Watcher...
start "Tailwind Watcher" cmd /k "cd /d D:\wamp\www\kts && npm run watch"
"D:\wamp\bin\php\php8.3.28\php.exe" spark serve
pause
