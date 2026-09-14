<?php

// Ensure necessary writable directories exist in /tmp for Serverless environment
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward Vercel Serverless Function requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
