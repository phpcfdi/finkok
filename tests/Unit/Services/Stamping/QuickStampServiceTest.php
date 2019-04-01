<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class QuickStampServiceTest extends TestCase
{
    public function testQuickStampSendXmlAndProcessPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('quickstamp-response-with-alerts.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new QuickStampService($settings);
        $command = new StampingCommand('foo');

        $result = $service->quickstamp($command);
        $this->assertSame('FAKE1', $result->alerts()->first()->errorCode());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('quick_stamp', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame('foo', $caller->latestCallParameters['xml']);
    }
}
