<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests;

use DateTimeImmutable;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\SoapFactory;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function createSettingsFromEnvironment(SoapFactory $soapFactory = null): FinkokSettings
    {
        $settings = new FinkokSettings(
            strval(getenv('FINKOK_USERNAME')) ?: 'username-non-set',
            strval(getenv('FINKOK_PASSWORD')) ?: 'password-non-set',
            FinkokEnvironment::makeDevelopment()
        );
        if (null !== $soapFactory) {
            $settings->changeSoapFactory($soapFactory);
        }

        if (boolval(getenv('FINKOK_LOG_CALLS'))) {
            $settings->soapFactory()->setLogger(
                $this->createLoggerPrinter(
                    sprintf(
                        '%s/../build/tests/%s-%s-%s.txt',
                        __DIR__,
                        (new DateTimeImmutable())->format('YmdHis.u'),
                        $this->getName(),
                        uniqid()
                    )
                )
            );
        }
        return $settings;
    }

    protected function createLoggerPrinter($outputFile = 'php://stdout'): LoggerInterface
    {
        return new class($outputFile) extends AbstractLogger implements LoggerInterface {
            public $outputFile;

            public function __construct(string $outputFile)
            {
                $this->outputFile = $outputFile;
            }

            public function log($level, $message, array $context = []): void
            {
                file_put_contents(
                    $this->outputFile,
                    PHP_EOL . print_r(json_decode($message), true),
                    FILE_APPEND
                );
            }
        };
    }

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
}
