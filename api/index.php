<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$tmpPaths = [
    getenv('VIEW_COMPILED_PATH') ?: '/tmp/views',
    dirname(getenv('APP_CONFIG_CACHE') ?: '/tmp/config.php'),
    dirname(getenv('APP_EVENTS_CACHE') ?: '/tmp/events.php'),
    dirname(getenv('APP_PACKAGES_CACHE') ?: '/tmp/packages.php'),
    dirname(getenv('APP_ROUTES_CACHE') ?: '/tmp/routes.php'),
    dirname(getenv('APP_SERVICES_CACHE') ?: '/tmp/services.php'),
];

foreach (array_unique($tmpPaths) as $path) {
    if ($path && ! is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (getenv('VERCEL') && getenv('DB_CONNECTION') === 'sqlite' && str_starts_with((string) getenv('DB_DATABASE'), '/tmp/')) {
    $database = getenv('DB_DATABASE');
    $migrationFiles = glob(__DIR__.'/../database/migrations/*.php') ?: [];
    $migrationSignature = md5(implode('|', array_map(
        fn (string $file): string => basename($file).':'.filemtime($file),
        $migrationFiles
    )));
    $marker = '/tmp/warehouseflow_database_ready_'.$migrationSignature;

    if (! file_exists($database)) {
        touch($database);
    }

    if (! file_exists($marker)) {
        $kernel = $app->make('Illuminate\\Contracts\\Console\\Kernel');
        $kernel->call('migrate', ['--force' => true]);
        $kernel->call('db:seed', ['--force' => true]);
        file_put_contents($marker, '1');
    }
}

$app->handleRequest(Request::capture());
