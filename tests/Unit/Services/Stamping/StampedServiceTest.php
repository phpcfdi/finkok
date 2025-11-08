<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampedService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class StampedServiceTest extends TestCase
{
    public function testStampedSendXmlAndProcessPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('stamped-response-with-alerts.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new StampedService($settings);
        $command = new StampingCommand('foo');

        $result = $service->stamped($command);
        $this->assertSame('FAKE1', $result->alerts()->first()->errorCode());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('stamped', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame('foo', $caller->latestCallParameters['xml']);
    }
}
