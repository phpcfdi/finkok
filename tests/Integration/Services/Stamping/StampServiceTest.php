<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class StampServiceTest extends TestCase
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
            $stampingResult = $service->stamp($this->cachedCommand());
        }

        return $stampingResult;
    }

    protected function createService(): StampService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new StampService($settings);
    }

    public function testStampValidPrecfdi(): void
    {
        $firstResult = $this->cachedStamped();

        $this->assertSame('Comprobante timbrado satisfactoriamente', $firstResult->statusCode());
        $this->assertNotEmpty($firstResult->xml());
        $this->assertNotEmpty($firstResult->uuid());
        $this->assertStringContainsString($firstResult->uuid(), $firstResult->xml());
    }

    public function testStampTwiceSamePrecfdi(): void
    {
        $firstResult = $this->cachedStamped();

        $secondResult = $this->createService()->stamp($this->cachedCommand());
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
        $command = new StampingCommand(
            (new RandomPreCfdi())->createInvalidByDate()
        );

        $service = $this->createService();
        $result = $service->stamp($command);

        $this->assertGreaterThan(0, $result->alerts()->count());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }
}
