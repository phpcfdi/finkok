<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetPendingCommand;
use PhpCfdi\Finkok\Services\Cancel\GetPendingService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetPendingServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('cancel-get-pending-response-2-items.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new GetPendingService($settings);

        $command = new GetPendingCommand('x-rfc');
        $result = $service->obtainPending($command);
        $this->assertEquals($preparedResult, $result->rawData()); // this is fake, no need to test

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get_pending', $caller->latestCallMethodName);
        $this->assertArrayHasKey('rtaxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['rtaxpayer_id']);
    }
}
