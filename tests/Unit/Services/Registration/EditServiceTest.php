<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\EditCommand;
use PhpCfdi\Finkok\Services\Registration\EditService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class EditServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('registration-edit-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new EditService($settings);

        $command = new EditCommand('x-rfc', 'x-status', 'x-cer', 'x-key', 'x-pass');
        $result = $service->edit($command);
        $this->assertSame('predefined-message', $result->message());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('edit', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('status', $caller->latestCallParameters);
        $this->assertSame($command->status(), $caller->latestCallParameters['status']);
        $this->assertArrayHasKey('cer', $caller->latestCallParameters);
        $this->assertSame($command->certificate(), $caller->latestCallParameters['cer']);
        $this->assertArrayHasKey('key', $caller->latestCallParameters);
        $this->assertSame($command->privateKey(), $caller->latestCallParameters['key']);
        $this->assertArrayHasKey('passphrase', $caller->latestCallParameters);
        $this->assertSame($command->passPhrase(), $caller->latestCallParameters['passphrase']);
    }
}
