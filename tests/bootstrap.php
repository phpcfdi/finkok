<?php

declare(strict_types=1);

// report all errors
error_reporting(-1);

// set time zone to mexico city instead of running on default UTC
date_default_timezone_set('America/Mexico_City');

// require composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

call_user_func(new class() {
    public function __invoke(): void
    {
        $environmentFile = __DIR__ . '/.env';
        if (! $this->environmentFileExists($environmentFile)) {
            trigger_error(sprintf('Cannot read testing environment file %s', $environmentFile), E_USER_NOTICE);
            return;
        }
        $dotDevUsePutenv = true;
        (new Symfony\Component\Dotenv\Dotenv($dotDevUsePutenv))->load($environmentFile);
    }

    public function environmentFileExists(string $environmentFile): bool
    {
        return file_exists($environmentFile) && ! is_dir($environmentFile) && is_readable($environmentFile);
    }
});
