<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use CfdiUtils\Cfdi;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusService;
use PhpCfdi\Finkok\SoapFactory;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class GetSatStatusServiceTest extends IntegrationTestCase
{
    protected function createService(): GetSatStatusService
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
        return new GetSatStatusService($settings);
    }

    public function testQueryOnOnNonExistentUuid(): void
    {
        $service = $this->createService();

        $command = new GetSatStatusCommand(
            'TCM970625MB1',
            'LAN7008173R5',
            '12345678-1234-1234-1234-123456789012',
            '12345.67'
        );

        $result = $service->query($command);
        $this->assertSame('No Encontrado', $result->cfdi());
    }

    public function testQueryOnNewStampedCfdi(): void
    {
        $cfdi = $this->currentCfdi();
        $this->assertNotEmpty($cfdi->uuid(), 'Cannot create a CFDI to GetSatStatus');

        $cfdiReader = Cfdi::newFromString($cfdi->xml())->getQuickReader();

        $command = new GetSatStatusCommand(
            $cfdiReader->emisor['Rfc'],
            $cfdiReader->receptor['Rfc'],
            $cfdi->uuid(),
            $cfdiReader['total']
        );
        $service = $this->createService();
        $result = $service->query($command);

        $this->assertSame('Vigente', $result->cfdi());
        $this->assertSame('Cancelable sin aceptación', $result->cancellable());
    }
}
