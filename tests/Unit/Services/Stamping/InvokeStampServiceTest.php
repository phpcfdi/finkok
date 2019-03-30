<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;

class InvokeStampServiceTest extends TestCase
{
    public function testInvokeExpectingFailure(): void
    {
        $settings = new FinkokSettings('foo', 'bar');
        $xml = (new RandomPreCfdi())->createInvalidByDate();

        $service = new StampService($settings);
        $command = new StampingCommand($xml);
        $result = $service->stamp($command);
        $this->assertTrue($result->hasAlerts());
    }
}
