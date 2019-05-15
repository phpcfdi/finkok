<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\GetContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\GetContractsService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class GetContractsServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('manifest-getcontracts-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new GetContractsService($settings);

        $command = new GetContractsCommand('x-rfc', 'x-name', 'x-address', 'x-email');
        $result = $service->obtainContracts($command);
        $this->assertSame('predefined-contract', $result->contract());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get_contracts', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('name', $caller->latestCallParameters);
        $this->assertSame($command->name(), $caller->latestCallParameters['name']);
        $this->assertArrayHasKey('address', $caller->latestCallParameters);
        $this->assertSame($command->address(), $caller->latestCallParameters['address']);
        $this->assertArrayHasKey('email', $caller->latestCallParameters);
        $this->assertSame($command->email(), $caller->latestCallParameters['email']);
    }
}
