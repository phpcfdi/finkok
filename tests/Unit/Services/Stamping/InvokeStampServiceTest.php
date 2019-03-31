<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class InvokeStampServiceTest extends TestCase
{
    public function testStampSendXmlAndProcessPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('stamp-response-with-alerts.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new StampService($settings);
        $command = new StampingCommand('foo');

        $result = $service->stamp($command);
        $this->assertSame('FAKE1', $result->alerts()->first()->errorCode());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('stamp', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame('foo', $caller->latestCallParameters['xml']);
    }
}
