<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests;

use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createSettingsFromEnvironment(): FinkokSettings
    {
        return new FinkokSettings(
            strval(getenv('FINKOK_USERNAME')),
            strval(getenv('FINKOK_PASSWORD')),
            FinkokEnvironment::makeDevelopment()
        );
    }

    public static function filePath(string $append = ''): string
    {
        return __DIR__ . '/_files/' . $append;
    }

    public static function fileContentPath(string $append): string
    {
        return static::fileContent(static::filePath($append));
    }

    public static function fileContent(string $path): string
    {
        if (! file_exists($path)) {
            return '';
        }
        return strval(file_get_contents($path));
    }
}
