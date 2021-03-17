<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Finkok\Services\Registration\SwitchCommand;
use PhpCfdi\Finkok\Services\Registration\SwitchService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class SwitchServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('registration-switch-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new SwitchService($settings);

        $command = new SwitchCommand('x-rfc', CustomerType::prepaid());
        $result = $service->switch($command);
        $this->assertSame('predefined-message', $result->message());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('switch', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('type_user', $caller->latestCallParameters);
        $this->assertSame($command->customerType()->value(), $caller->latestCallParameters['type_user']);
    }
}
