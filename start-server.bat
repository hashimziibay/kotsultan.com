@echo off
title KTS Web Project Server
echo Starting PHP CodeIgniter Server and Tailwind CSS Watcher...
start "Tailwind Watcher" cmd /k "cd /d d:\Wamp\www\kts web project && npm run watch"
"d:\Wamp\bin\php\php8.3.28\php.exe" spark serve
pause
