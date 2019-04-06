<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class QuickStampServiceTest extends IntegrationTestCase
{
    protected function createService(): QuickStampService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new QuickStampService($settings);
    }

    public function testQuickStampCreatesStampUsingValidPrecfdi(): void
    {
        $command = $this->newStampingCommand();
        $service = $this->createService();
        $result = $service->quickstamp($command);

        $this->assertSame('Comprobante timbrado satisfactoriamente', $result->statusCode());
        $this->assertNotEmpty($result->xml());
        $this->assertNotEmpty($result->uuid());
        $this->assertStringContainsString($result->uuid(), $result->xml());
    }

    public function testQuickStampPreviouslyCreatedCfdiReturnsErrorCode307(): void
    {
        // call first to cachedStamped to use previous stamp or create a new one
        $this->currentCfdi();

        $service = $this->createService();
        $secondResult = $service->quickstamp($this->currentStampingCommand());
        $this->assertNotNull(
            $secondResult->alerts()->findByErrorCode('307'),
            'Finkok must alert that it was previously stamped'
        );
    }

    public function testQuickStampValidatesAndFailOnPrecfdiWithErrors(): void
    {
        $command = $this->newStampingCommandInvalidDate();

        $settings = $this->createSettingsFromEnvironment();
        $service = new QuickStampService($settings);
        $result = $service->quickstamp($command);

        $this->assertTrue($result->hasAlerts());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }
}
