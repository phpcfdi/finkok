<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class GetSatStatusServiceTest extends IntegrationTestCase
{
    protected function createService(): GetSatStatusService
    {
        $settings = $this->createSettingsFromEnvironment();
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

    public function testQueryOnCurrentStampedCfdi(): void
    {
        $cfdi = $this->stamp($this->newStampingCommand());
        $this->assertNotEmpty($cfdi->uuid(), 'Cannot create a CFDI to GetSatStatus');

        $command = $this->createGetSatStatusCommandFromCfdiContents($cfdi->xml());
        $service = $this->createService();

        // try until 30 seconds or status is not 'No Encontrado'
        $this->waitUntil(function () use ($command, $service): bool {
            return ('No Encontrado' !== $service->query($command)->cfdi());
        }, 30, 1, 'Cannot assert cfdi status before get_sat_status is not: No Encontrado');

        $result = $service->query($command);

        $this->assertSame('Vigente', $result->cfdi());
        $this->assertSame('Cancelable sin aceptación', $result->cancellable());
    }
}
