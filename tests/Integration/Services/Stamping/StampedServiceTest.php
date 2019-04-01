<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampedService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class StampedServiceTest extends TestCase
{
    public function testStampedServiceWithNonStampedPrecfdi(): void
    {
        $precfdi = (new RandomPreCfdi())->createInvalidByDate();
        $command = new StampingCommand($precfdi);
        $settings = $this->createSettingsFromEnvironment();
        $stampedService = new StampedService($settings);

        $stampedResult = $stampedService->stamped($command);
        $this->assertCount(1, $stampedResult->alerts());

        $alert = $stampedResult->alerts()->first();
        $this->assertSame('603', $alert->errorCode());
        $this->assertSame('El CFDI no contiene un timbre previo', $alert->message());
    }
}
