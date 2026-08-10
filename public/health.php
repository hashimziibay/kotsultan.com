<?php

/**
 * Temporary server diagnostic page.
 * Visit: https://kotsultan.com/health.php
 * DELETE this file after the site is working.
 */

header('Content-Type: text/plain; charset=utf-8');
header('X-Robots-Tag: noindex');

$root = dirname(__DIR__);

echo "KotSultan.com server check\n";
echo "==========================\n";
echo 'Time: ' . gmdate('c') . " UTC\n";
echo 'PHP: ' . PHP_VERSION . "\n";
echo 'SAPI: ' . PHP_SAPI . "\n";
echo 'Doc root guess: ' . (__DIR__) . "\n";
echo 'Project root guess: ' . $root . "\n\n";

$checks = [
    'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'public/index.php' => is_file(__DIR__ . '/index.php'),
    '../.env' => is_file($root . '/.env'),
    '../vendor/autoload.php' => is_file($root . '/vendor/autoload.php'),
    '../vendor/codeigniter4/framework/system/Boot.php' => is_file($root . '/vendor/codeigniter4/framework/system/Boot.php'),
    '../app/Config/Paths.php' => is_file($root . '/app/Config/Paths.php'),
    '../writable writable' => is_dir($root . '/writable') && is_writable($root . '/writable'),
    '../writable/cache writable' => is_dir($root . '/writable/cache') && is_writable($root . '/writable/cache'),
    '../writable/logs writable' => is_dir($root . '/writable/logs') && is_writable($root . '/writable/logs'),
    '../writable/session writable' => is_dir($root . '/writable/session') && is_writable($root . '/writable/session'),
    'uploads folder' => is_dir(__DIR__ . '/uploads') || is_dir(__DIR__ . '/uploads/businesses'),
];

foreach ($checks as $label => $ok) {
    echo ($ok ? '[OK]  ' : '[FAIL] ') . $label . "\n";
}

echo "\nLoaded extensions: mysqli=" . (extension_loaded('mysqli') ? 'yes' : 'NO');
echo ' intl=' . (extension_loaded('intl') ? 'yes' : 'NO');
echo ' mbstring=' . (extension_loaded('mbstring') ? 'yes' : 'NO');
echo ' json=' . (extension_loaded('json') ? 'yes' : 'NO');
echo "\n";

if (! version_compare(PHP_VERSION, '8.2.0', '>=')) {
    echo "\nACTION: In StackCP set PHP version to 8.2 or 8.3 for this domain.\n";
}
if (! is_file($root . '/.env')) {
    echo "\nACTION: Upload .env.production to the project root and rename it to .env\n";
}
if (! is_file($root . '/vendor/autoload.php')) {
    echo "\nACTION: Run composer install on the server, OR upload the local vendor/ folder.\n";
}

echo "\nDelete public/health.php after fixing.\n";
