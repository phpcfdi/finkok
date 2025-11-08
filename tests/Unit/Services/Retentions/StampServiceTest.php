<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Services\Retentions\StampCommand;
use PhpCfdi\Finkok\Services\Retentions\StampService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class StampServiceTest extends TestCase
{
    public function testSignStampSendXmlAndProcessPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('retentions-stamp-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new StampService($settings);
        $command = new StampCommand('x-precfdi');

        $service->stamp($command);

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('stamp', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame('x-precfdi', $caller->latestCallParameters['xml']);
    }
}
