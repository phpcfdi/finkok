<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureCommand;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureService;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusResult;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusService;
use PhpCfdi\Finkok\SoapFactory;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;
use PhpCfdi\XmlCancelacion\Capsule;
use PhpCfdi\XmlCancelacion\CapsuleSigner;
use PhpCfdi\XmlCancelacion\Credentials;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class CancelSignatureServiceTest extends IntegrationTestCase
{
    protected function createService(): CancelSignatureService
    {
        $consoleLogger = new class() extends AbstractLogger implements LoggerInterface {
            public function log($level, $message, array $context = []): void
            {
                print_r(json_decode($message));
            }
        };
        $soapFactory = new SoapFactory();
        $soapFactory->setLogger($consoleLogger);
        $settings = $this->createSettingsFromEnvironment($soapFactory);
        return new CancelSignatureService($settings);
    }

    protected function createCommand(Capsule $capsule): CancelSignatureCommand
    {
        $credentials = new Credentials(
            $this->filePath('certs/TCM970625MB1.cer'),
            $this->filePath('certs/TCM970625MB1.key.pem'),
            trim($this->fileContentPath('certs/TCM970625MB1.password.bin'))
        );
        $xmlCancelacion = (new CapsuleSigner())->sign($capsule, $credentials);
        return new CancelSignatureCommand($xmlCancelacion);
    }

    protected function querySatStatus(FinkokSettings $settings, string $cfdi): GetSatStatusResult
    {
        $command = $this->createGetSatStatusCommandFromCfdiContents($cfdi);
        $service = new GetSatStatusService($settings);
        return $service->query($command);
    }

    public function testCancelNonExistentUuid(): void
    {
        $service = $this->createService();

        $cancelData = new Capsule('TCM970625MB1', ['12345678-1234-1234-1234-123456789012']);
        $command = $this->createCommand($cancelData);

        $result = $service->cancelSignature($command);
        $this->assertSame('UUID Not Found', $result->statusCode());
    }

    public function testCancelNewStampedCfdi(): void
    {
        $service = $this->createService();

        // given a cfdi
        $cfdi = $this->stamp($this->newStampingCommand());
        $this->assertNotEmpty($cfdi->uuid(), 'Cannot create a CFDI to cancel');

        // check that it has a correct status
        $beforeCancelStatus = $this->querySatStatus($service->settings(), $cfdi->xml());
        $this->assertSame('Vigente', $beforeCancelStatus->cfdi());
        $this->assertStringStartsWith('Cancelable ', $beforeCancelStatus->cancellable());

        // perform cancel
        $command = $this->createCommand(new Capsule('TCM970625MB1', [$cfdi->uuid()]));
        $result = $service->cancelSignature($command);

        print_r(['$result' => $result->rawData()]);

        // check cancel result
        $document = $result->documents()->first();
        $this->assertSame($cfdi->uuid(), $document->uuid());
        $this->assertSame(
            '201', // 201 - Petición de cancelación realizada exitosamente
            $document->documentStatus(),
            'Finkok did not return 201 EstatusUUID on CancelSignature'
        );
        $this->assertNotEmpty($result->voucher(), 'Finkok did not return voucher (Acuse) on CancelSignature');
        $this->assertNotEmpty($result->date());
        $this->assertSame('TCM970625MB1', $result->rfc());
    }
}
