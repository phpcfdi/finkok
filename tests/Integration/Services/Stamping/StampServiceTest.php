<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class StampServiceTest extends IntegrationTestCase
{
    protected function createService(): StampService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new StampService($settings);
    }

    public function testStampValidPrecfdi(): void
    {
        $command = $this->newStampingCommand();
        $service = $this->createService();
        $result = $service->stamp($command);

        $this->assertSame('Comprobante timbrado satisfactoriamente', $result->statusCode());
        $this->assertNotEmpty($result->xml());
        $this->assertNotEmpty($result->uuid());
        $this->assertStringContainsString($result->uuid(), $result->xml());
    }

    public function testStampPreviouslyCreatedCfdi(): void
    {
        $firstResult = $this->currentCfdi();

        $secondResult = $this->createService()->stamp($this->currentStampingCommand());
        $this->assertNotNull(
            $secondResult->alerts()->findByErrorCode('307'),
            'Finkok must alert that it was previously stamped'
        );

        $this->assertSame(
            $firstResult->uuid(),
            $secondResult->uuid(),
            'Finkok does not return the same UUID for duplicated stamp call'
        );
    }

    public function testStampPrecfdiWithErrorInDate(): void
    {
        $command = $this->newStampingCommandInvalidDate();

        $service = $this->createService();
        $result = $service->stamp($command);

        $this->assertGreaterThan(0, $result->alerts()->count());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }
}
