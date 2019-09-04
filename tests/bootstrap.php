<?php

declare(strict_types=1);

// report all errors
error_reporting(-1);

date_default_timezone_set('America/Mexico_City');

// require composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

call_user_func(function (): void {
    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile) && ! is_dir($envFile)) {
        $dotenv = new \Symfony\Component\Dotenv\Dotenv();
        $dotenv->load($envFile);
    }
});
