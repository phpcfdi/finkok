<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainCommand;
use PhpCfdi\Finkok\Services\Registration\ObtainService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class ObtainServiceTest extends TestCase
{
    public function testServiceUsingPreparedResultWithRfc(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode(TestCase::fileContentPath('registration-get-response-2-items.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new ObtainService($settings);

        $command = new ObtainCommand('LAN7008173R5');
        $result = $service->obtain($command);
        $this->assertSame('predefined-message', $result->message());
        $this->assertCount(2, $result->customers());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get', $caller->latestCallMethodName);

        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
    }

    public function testServiceUsingPreparedResultWithoutRfc(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode(TestCase::fileContentPath('registration-get-response-2-items.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new ObtainService($settings);

        $command = new ObtainCommand();
        $service->obtain($command);

        $caller = $soapFactory->latestSoapCaller;
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame('', $caller->latestCallParameters['taxpayer_id']);
    }
}
