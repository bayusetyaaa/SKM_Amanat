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

// 3. Fallback APP_KEY if not configured in Vercel Environment Variables
if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
    $defaultKey = 'base64:1rkA81Dy9XJzbiEvt7Vrdl9FJQVFBOJv7NijxdA+5fc=';
    putenv('APP_KEY=' . $defaultKey);
    $_ENV['APP_KEY'] = $defaultKey;
    $_SERVER['APP_KEY'] = $defaultKey;
}

// 4. Fallback SQLite database in /tmp if external MySQL is not yet configured
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
$isNewDb = false;

if ($dbConn === 'sqlite') {
    $dbPath = '/tmp/database.sqlite';
    if (!file_exists($dbPath) || filesize($dbPath) === 0) {
        @touch($dbPath);
        $isNewDb = true;
    }
    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=' . $dbPath);
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $dbPath;
}

// 5. Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Auto-migrate & seed on first container boot if tables do not exist
try {
    if ($dbConn === 'sqlite' && $isNewDb) {
        Artisan::call('migrate --force --seed');
    } elseif ($dbConn !== 'sqlite' && !\Illuminate\Support\Facades\Schema::hasTable('users')) {
        Artisan::call('migrate --force --seed');
    }
} catch (\Throwable $e) {
    error_log('Database init notice: ' . $e->getMessage());
}

$app->handleRequest(Request::capture());
