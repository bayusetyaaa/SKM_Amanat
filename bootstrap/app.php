<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// On Vercel / Serverless environments, redirect storage to writable /tmp
$storagePath = getenv('LARAVEL_STORAGE_PATH') ?: ($_ENV['LARAVEL_STORAGE_PATH'] ?? null);
if (!$storagePath && (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || (PHP_OS_FAMILY !== 'Windows' && is_dir('/tmp')))) {
    $storagePath = '/tmp/storage';
}
if ($storagePath) {
    $app->useStoragePath($storagePath);
}

return $app;
