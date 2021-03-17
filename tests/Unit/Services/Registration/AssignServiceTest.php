<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\AssignCommand;
use PhpCfdi\Finkok\Services\Registration\AssignService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class AssignServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('registration-assign-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new AssignService($settings);

        $command = new AssignCommand('x-rfc', 100);
        $result = $service->assign($command);
        $this->assertSame('predefined-message', $result->message());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('assign', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('credit', $caller->latestCallParameters);
        $this->assertSame($command->credit(), $caller->latestCallParameters['credit']);
    }
}
