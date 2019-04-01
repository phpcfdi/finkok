<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class QuickStampServiceTest extends TestCase
{
    public function testQuickStampValidatesAndFailOnPrecfdiWithErrors(): void
    {
        $precfdi = (new RandomPreCfdi())->createInvalidByDate();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new QuickStampService($settings);
        $result = $service->quickstamp($command);

        $this->assertTrue($result->hasAlerts());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }

    public function testQuickStampCreatesStampUsingValidPrecfdi(): void
    {
        $precfdi = (new RandomPreCfdi())->createValid();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new QuickStampService($settings);
        $result = $service->quickstamp($command);

        $this->assertNotEmpty($result->uuid());
    }

    public function testStampValidPrecfdiTwoConsecutiveTimesReturnsErrorCode307(): void
    {
        $precfdi = (new RandomPreCfdi())->createValid();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new QuickStampService($settings);

        $firstResult = $service->quickstamp($command);
        $this->assertSame('Comprobante timbrado satisfactoriamente', $firstResult->statusCode());

        $secondResult = $service->quickstamp($command);
        $this->assertSame(
            '307',
            $secondResult->alerts()->first()->errorCode(),
            'Finkok must alert that it was previously stamped'
        );
    }
}
