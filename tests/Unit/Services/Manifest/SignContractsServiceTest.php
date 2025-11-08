<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\SignContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\SignContractsService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class SignContractsServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('manifest-signcontracts-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new SignContractsService($settings);

        $command = new SignContractsCommand('x-snid', 'x-privacy', 'x-contract');
        $result = $service->sendSignedContracts($command);
        $this->assertSame('predefined-message', $result->message());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('sign_contract', $caller->latestCallMethodName);
        $this->assertArrayHasKey('snid', $caller->latestCallParameters);
        $this->assertSame($command->snid(), $caller->latestCallParameters['snid']);
        $this->assertArrayHasKey('privacy_xml', $caller->latestCallParameters);
        $this->assertSame($command->privacy(), $caller->latestCallParameters['privacy_xml']);
        $this->assertArrayHasKey('contract_xml', $caller->latestCallParameters);
        $this->assertSame($command->contract(), $caller->latestCallParameters['contract_xml']);
    }
}
