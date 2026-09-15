<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// 1. Ensure all writable storage directories exist in /tmp for Serverless environment
$dirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/bootstrap-cache',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Set default serverless environment variables
putenv('APP_MAINTENANCE_DRIVER=file');
$_ENV['APP_MAINTENANCE_DRIVER'] = 'file';
$_SERVER['APP_MAINTENANCE_DRIVER'] = 'file';

putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = 'stderr';
$_SERVER['LOG_CHANNEL'] = 'stderr';

putenv('LARAVEL_STORAGE_PATH=/tmp/storage');
$_ENV['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

putenv('APP_SERVICES_CACHE=/tmp/storage/bootstrap-cache/services.php');
$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap-cache/services.php';
$_SERVER['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap-cache/services.php';

putenv('APP_PACKAGES_CACHE=/tmp/storage/bootstrap-cache/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap-cache/packages.php';
$_SERVER['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap-cache/packages.php';

putenv('APP_CONFIG_CACHE=/tmp/storage/bootstrap-cache/config.php');
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap-cache/config.php';
$_SERVER['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap-cache/config.php';

putenv('APP_ROUTES_CACHE=/tmp/storage/bootstrap-cache/routes.php');
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap-cache/routes.php';
$_SERVER['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap-cache/routes.php';

putenv('APP_EVENTS_CACHE=/tmp/storage/bootstrap-cache/events.php');
$_ENV['APP_EVENTS_CACHE'] = '/tmp/storage/bootstrap-cache/events.php';
$_SERVER['APP_EVENTS_CACHE'] = '/tmp/storage/bootstrap-cache/events.php';

// 3. Fallback APP_KEY if not configured in Vercel Environment Variables
if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
    $defaultKey = 'base64:1rkA81Dy9XJzbiEvt7Vrdl9FJQVFBOJv7NijxdA+5fc=';
    putenv('APP_KEY=' . $defaultKey);
    $_ENV['APP_KEY'] = $defaultKey;
    $_SERVER['APP_KEY'] = $defaultKey;
}

// 4. Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

try {
    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<div style='font-family:sans-serif;padding:30px;max-width:800px;margin:auto;'>";
    echo "<h2 style='color:#e11d48;'>Application Server Error (500)</h2>";
    echo "<p style='font-size:16px;'><strong>Pesan:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color:#64748b;'><strong>Lokasi:</strong> " . htmlspecialchars($e->getFile()) . " baris " . $e->getLine() . "</p>";
    echo "<details><summary style='cursor:pointer;'>Detail Stack Trace</summary><pre style='background:#f1f5f9;padding:15px;border-radius:6px;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre></details>";
    echo "</div>";
}
