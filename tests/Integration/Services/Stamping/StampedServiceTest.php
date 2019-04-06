<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Services\Stamping\StampedService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class StampedServiceTest extends TestCase
{
    protected function cachedCommand(): StampingCommand
    {
        static $command = null;
        if (null === $command) {
            $command = new StampingCommand((new RandomPreCfdi())->createValid());
        }
        return $command;
    }

    public function testStampedServiceWithNonStampedPrecfdi(): void
    {
        $settings = $this->createSettingsFromEnvironment();
        $stampedService = new StampedService($settings);

        $stampedResult = $stampedService->stamped($this->cachedCommand());
        $this->assertCount(1, $stampedResult->alerts());

        $alert = $stampedResult->alerts()->first();
        $this->assertSame('603', $alert->errorCode());
        $this->assertSame('El CFDI no contiene un timbre previo', $alert->message());
    }

    public function testStampAndConsumeStampedImmediately(): void
    {
        $command = $this->cachedCommand();

        $settings = $this->createSettingsFromEnvironment();
        $stampService = new QuickStampService($settings);
        $quickstampService = $stampService->quickstamp($command);
        $this->assertNotEmpty($quickstampService->uuid(), 'Finkok did not create CFDI');

        $stampedService = new StampedService($settings);
        $stampedResult = $stampedService->stamped($command);

        $this->assertNotEmpty($stampedResult->uuid(), 'Finkok regression test fail, see ticket #17287');

        $this->assertSame(
            $quickstampService->uuid(),
            $stampedResult->uuid(),
            'Finkok does not return the same UUID for recently created stamp using stamped'
        );
    }
}
