<?php

/**
 * Shared-hosting front controller.
 * Use when the domain document root is the project root (not /public).
 * Prefer pointing the StackCP document root to /public instead.
 */

$publicIndex = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';

if (! is_file($publicIndex)) {
    header('HTTP/1.1 503 Service Unavailable', true, 503);
    echo 'Missing public/index.php. Upload the full project and set document root to /public.';
    exit(1);
}

require $publicIndex;
