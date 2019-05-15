<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetPendingCommand;
use PhpCfdi\Finkok\Services\Cancel\GetPendingService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class GetPendingServiceTest extends IntegrationTestCase
{
    public function testConsumeService(): void
    {
        // Cannot check anything else than it did not return an error
        // It might be possible to create a test that ask for a cancellation that require authorization
        // but this is simply unpractical for *this* test suite because
        // it takes around 16 minutes to have a CFDI with status "cancelable con autorizacion"
        $settings = $this->createSettingsFromEnvironment();
        $command = new GetPendingCommand('TCM970625MB1');
        $service = new GetPendingService($settings);
        $result = $service->obtainPending($command);

        $this->assertTrue(is_array($result->uuids()));
        $this->assertSame('', $result->error());
    }
}
