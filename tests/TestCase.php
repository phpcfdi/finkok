<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests;

use DateTimeImmutable;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Helpers\FileLogger;
use PhpCfdi\Finkok\Helpers\JsonDecoderLogger;
use PhpCfdi\Finkok\SoapFactory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createSettingsFromEnvironment(?SoapFactory $soapFactory = null): FinkokSettings
    {
        $settings = new FinkokSettings(
            $this->getenv('FINKOK_USERNAME') ?: 'username-non-set',
            $this->getenv('FINKOK_PASSWORD') ?: 'password-non-set',
            FinkokEnvironment::makeDevelopment(),
        );
        if (null !== $soapFactory) {
            $settings->changeSoapFactory($soapFactory);
        }

        if ($this->getenvBool('FINKOK_LOG_CALLS')) {
            $loggerOutputFile = sprintf(
                '%s/../build/tests/%s-%s-%s.txt',
                __DIR__,
                (new DateTimeImmutable())->format('YmdHis.u'),
                $this->name(),
                uniqid(),
            );
            $logger = new JsonDecoderLogger(new FileLogger($loggerOutputFile));
            $settings->soapFactory()->setLogger($logger);
        }
        return $settings;
    }

    /** @return array<string, string> */
    protected function obtainCsdCertificatePrivateKeyData(): array
    {
        return [
            'certificate' => $this->filePath('certs/EKU9003173C9.cer'),
            'privateKey' => $this->filePath('certs/EKU9003173C9.key.pem'),
            'passPhrase' => trim($this->fileContentPath('certs/EKU9003173C9.password.bin')),
        ];
    }

    protected function createCsdCredential(): Credential
    {
        $cerKey = $this->obtainCsdCertificatePrivateKeyData();
        return Credential::openFiles($cerKey['certificate'], $cerKey['privateKey'], $cerKey['passPhrase']);
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

    public static function getenv(string $key): string
    {
        $value = $_ENV[$key] ?? '';
        return (is_scalar($value)) ? strval($value) : '';
    }

    public static function getenvBool(string $key): bool
    {
        $value = static::getenv($key);
        return ! in_array($value, ['', '0', 'no', 'false']);
    }
}
