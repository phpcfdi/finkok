<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests;

use Closure;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\SoapFactory;
use RuntimeException;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createSettingsFromEnvironment(SoapFactory $soapFactory = null): FinkokSettings
    {
        $settings = new FinkokSettings(
            strval(getenv('FINKOK_USERNAME')),
            strval(getenv('FINKOK_PASSWORD')),
            FinkokEnvironment::makeDevelopment()
        );
        if (null !== $soapFactory) {
            $settings->changeSoapFactory($soapFactory);
        }
        return $settings;
    }

    protected function waitUntil(
        Closure $checkFunction,
        int $maxSeconds,
        int $waitSeconds,
        string $exceptionMessage = ''
    ): void {
        $repeatUntil = time() + $maxSeconds;
        do {
            if ($checkFunction()) {
                return;
            }
            if (time() > $repeatUntil) {
                break;
            }
            sleep($waitSeconds);
        } while (true);
        if ('' !== $exceptionMessage) {
            throw new RuntimeException($exceptionMessage);
        }
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
