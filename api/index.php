<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// 1. Ensure all writable storage directories exist in /tmp for Serverless environment (only on cold start)
if (!is_dir('/tmp/storage/framework/views')) {
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
}

// 2. Normalize HTTPS environment for reverse proxy (Vercel)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = '443';
}

// 3. Set default serverless environment variables
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

// 4. Force cookie-based session for serverless (no shared filesystem between instances)
putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

putenv('SESSION_SECURE_COOKIE=true');
$_ENV['SESSION_SECURE_COOKIE'] = 'true';
$_SERVER['SESSION_SECURE_COOKIE'] = 'true';

putenv('SESSION_SAME_SITE=none');
$_ENV['SESSION_SAME_SITE'] = 'none';
$_SERVER['SESSION_SAME_SITE'] = 'none';

// 5. Fallback APP_KEY if not configured in Vercel Environment Variables
if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
    $defaultKey = 'base64:1rkA81Dy9XJzbiEvt7Vrdl9FJQVFBOJv7NijxdA+5fc=';
    putenv('APP_KEY=' . $defaultKey);
    $_ENV['APP_KEY'] = $defaultKey;
    $_SERVER['APP_KEY'] = $defaultKey;
}

if (getenv('APP_DEBUG') === false && !isset($_ENV['APP_DEBUG'])) {
    putenv('APP_DEBUG=false');
    $_ENV['APP_DEBUG'] = 'false';
    $_SERVER['APP_DEBUG'] = 'false';
}

// 6. Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

try {
    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 7. Run migrations only if explicitly enabled via RUN_MIGRATIONS=true (avoids 3-4s cold-start delay)
    $dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
    $dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '');
    $runMigrations = (getenv('RUN_MIGRATIONS') === 'true' || ($_ENV['RUN_MIGRATIONS'] ?? '') === 'true');

    if ($runMigrations && $dbConnection === 'pgsql' && !empty($dbHost)) {
        $migrationLockFile = '/tmp/storage/migrations_ran.lock';
        if (!file_exists($migrationLockFile)) {
            try {
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                // Always run migrations (safe, idempotent)
                $kernel->call('migrate', ['--force' => true]);
                // Only seed if users table is empty (avoid duplicate data)
                $userCount = \Illuminate\Support\Facades\DB::table('users')->count();
                if ($userCount === 0) {
                    $kernel->call('db:seed', ['--force' => true, '--class' => 'DatabaseSeeder']);
                }
                file_put_contents($migrationLockFile, date('Y-m-d H:i:s') . ' - users:' . $userCount);
            } catch (\Throwable $migrationError) {
                error_log('Migration error: ' . $migrationError->getMessage());
                file_put_contents($migrationLockFile, 'error: ' . $migrationError->getMessage());
            }
        }
    }

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
