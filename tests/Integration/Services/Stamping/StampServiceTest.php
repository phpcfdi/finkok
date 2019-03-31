<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class StampServiceTest extends TestCase
{
    public function testStampPrecfdiWithErrorInDate(): void
    {
        $precfdi = (new RandomPreCfdi())->createInvalidByDate();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new StampService($settings);
        $result = $service->stamp($command);

        $this->assertGreaterThan(0, $result->alerts()->count());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }

    public function testStampValidPrecfdi(): void
    {
        $precfdi = (new RandomPreCfdi())->createValid();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new StampService($settings);
        $result = $service->stamp($command);

        $this->assertSame('Comprobante timbrado satisfactoriamente', $result->statusCode());
        $this->assertCount(0, $result->alerts());
        $this->assertNotEmpty($result->xml());
        $this->assertNotEmpty($result->uuid());
        $this->assertStringContainsString($result->uuid(), $result->xml());
    }

    public function testStampValidPrecfdiTwoConsecutiveTimes(): void
    {
        $this->markTestSkipped('Finkok no está devolviendo la información esperada, finkok-bug?');

        $precfdi = (new RandomPreCfdi())->createValid();
        $command = new StampingCommand($precfdi);

        $settings = $this->createSettingsFromEnvironment();
        $service = new StampService($settings);

        $firstResult = $service->stamp($command);
        $this->assertSame('Comprobante timbrado satisfactoriamente', $firstResult->statusCode());

        $secondResult = $service->stamp($command);
        $this->assertSame(
            $firstResult->uuid(),
            $secondResult->uuid(),
            'Finkok does not return the same UUID for duplicated stamp'
        );
    }
}
