<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampedService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class StampedServiceTest extends IntegrationTestCase
{
    protected function createService(): StampedService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new StampedService($settings);
    }

    public function testStampedServiceWithNonStampedPrecfdi(): void
    {
        $service = $this->createService();

        $result = $service->stamped($this->newStampingCommand());
        $this->assertCount(1, $result->alerts());

        $alert = $result->alerts()->first();
        $this->assertSame('603', $alert->errorCode());
        $this->assertSame('El CFDI no contiene un timbre previo', $alert->message());
    }

    public function testStampAndConsumeStampedImmediately(): void
    {
        $previousStamp = $this->currentCfdi();
        $this->assertNotEmpty($previousStamp->uuid(), 'Finkok did not create CFDI');

        $service = $this->createService();
        $result = $service->stamped($this->currentStampingCommand());
        $this->assertNotEmpty($result->uuid(), 'Finkok regression fail, see ticket #17287');

        $this->assertSame(
            $previousStamp->uuid(),
            $result->uuid(),
            'Finkok does not return the same UUID for recently created stamp using stamped'
        );
    }
}
