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

    public function testQueryOnNonExistentUuid(): void
    {
        $service = $this->createService();

        $command = new GetSatStatusCommand(
            'EKU9003173C9',
            'LAN7008173R5',
            '12345678-1234-1234-1234-123456789012',
            '12345.67'
        );

        $result = $service->query($command);
        $this->assertSame('No Encontrado', $result->cfdi());
    }

    public function testQueryUntilFoundOrTimeOnRecentlyStampedCfdi(): void
    {
        $cfdi = $this->stamp($this->newStampingCommand());
        $this->assertNotEmpty($cfdi->uuid(), 'Cannot create a CFDI to GetSatStatus');

        $command = $this->createGetSatStatusCommandFromCfdiContents($cfdi->xml());
        $service = $this->createService();

        $result = $service->queryUntilFoundOrTime($command);

        $this->assertSame('Vigente', $result->cfdi());
        $this->assertSame('Cancelable sin aceptación', $result->cancellable());
    }
}
