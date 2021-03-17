<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Definitions\SignedDocumentFormat;
use PhpCfdi\Finkok\Services\Manifest\GetSignedContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\GetSignedContractsService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetSignedContractsServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('manifest-getsignedcontracts-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new GetSignedContractsService($settings);

        $command = new GetSignedContractsCommand('x-snid', 'x-rfc', SignedDocumentFormat::xml());
        $result = $service->getSignedContracts($command);
        $this->assertSame('predefined-contract', $result->contract());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get_documents', $caller->latestCallMethodName);
        $this->assertArrayHasKey('snid', $caller->latestCallParameters);
        $this->assertSame($command->snid(), $caller->latestCallParameters['snid']);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('type', $caller->latestCallParameters);
        $this->assertSame($command->format()->value(), $caller->latestCallParameters['type']);
    }
}
