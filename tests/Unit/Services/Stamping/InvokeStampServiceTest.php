<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class InvokeStampServiceTest extends TestCase
{
    public function testInvokeExpectingFailure(): void
    {
        $settings = $this->createSettingsFromEnvironment();
        $xml = (new RandomPreCfdi())->createInvalidByDate();

        $service = new StampService($settings);
        $command = new StampingCommand($xml);
        $result = $service->stamp($command);
        $this->assertGreaterThan(0, $result->alerts()->count());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }
}
