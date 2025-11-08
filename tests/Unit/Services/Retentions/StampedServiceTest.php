<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Services\Retentions\StampedCommand;
use PhpCfdi\Finkok\Services\Retentions\StampedService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class StampedServiceTest extends TestCase
{
    public function testSignStampSendXmlAndProcessPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('retentions-stamped-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new StampedService($settings);
        $command = new StampedCommand('x-precfdi');

        $service->stamped($command);

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('stamped', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame('x-precfdi', $caller->latestCallParameters['xml']);
    }
}
