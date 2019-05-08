<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\AddCommand;
use PhpCfdi\Finkok\Services\Registration\AddService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class AddServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('registration-add-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new AddService($settings);

        $command = new AddCommand('x-rfc', 'x-type', 'x-cer', 'x-key', 'x-pass');
        $result = $service->add($command);
        $this->assertSame('predefined-message', $result->message());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('add', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('type_user', $caller->latestCallParameters);
        $this->assertSame($command->type(), $caller->latestCallParameters['type_user']);
        $this->assertArrayHasKey('cer', $caller->latestCallParameters);
        $this->assertSame($command->certificate(), $caller->latestCallParameters['cer']);
        $this->assertArrayHasKey('key', $caller->latestCallParameters);
        $this->assertSame($command->privateKey(), $caller->latestCallParameters['key']);
        $this->assertArrayHasKey('passphrase', $caller->latestCallParameters);
        $this->assertSame($command->passPhrase(), $caller->latestCallParameters['passphrase']);
    }
}
