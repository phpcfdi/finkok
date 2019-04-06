<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class QuickStampServiceTest extends TestCase
{
    protected function cachedCommand(): StampingCommand
    {
        static $command = null;
        if (null === $command) {
            $command = new StampingCommand((new RandomPreCfdi())->createValid());
        }
        return $command;
    }

    protected function cachedStamped(): StampingResult
    {
        static $stampingResult = null;
        if (null === $stampingResult) {
            $service = $this->createService();
            $stampingResult = $service->quickstamp($this->cachedCommand());
        }

        return $stampingResult;
    }

    protected function createService(): QuickStampService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new QuickStampService($settings);
    }

    public function testQuickStampCreatesStampUsingValidPrecfdi(): void
    {
        $result = $this->cachedStamped();
        $this->assertSame('Comprobante timbrado satisfactoriamente', $result->statusCode());
        $this->assertNotEmpty($result->uuid());
    }

    public function testStampValidPrecfdiTwoConsecutiveTimesReturnsErrorCode307(): void
    {
        // call first to cachedStamped to use previous stamp or create a new one
        $this->cachedStamped();

        $service = $this->createService();
        $secondResult = $service->quickstamp($this->cachedCommand());
        $this->assertNotNull(
            $secondResult->alerts()->findByErrorCode('307'),
            'Finkok must alert that it was previously stamped'
        );
    }

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
}
