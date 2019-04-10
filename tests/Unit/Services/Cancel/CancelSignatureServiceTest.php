<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Definitions\CancelStorePending;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureCommand;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class CancelSignatureServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('cancel-cancelsignature-response-2-items.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new CancelSignatureService($settings);

        $command = new CancelSignatureCommand('x-xml', CancelStorePending::yes());
        $result = $service->CancelSignature($command);
        $this->assertCount(2, $result->documents());
        $this->assertSame('voucher', $result->voucher());
        $this->assertSame('LAN7008173R5', $result->rfc());
        $this->assertSame('2019-04-05 16:29:47.138032', $result->date());
        $this->assertSame('304', $result->statusCode());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('cancel_signature', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame($command->xml(), $caller->latestCallParameters['xml']);
        $this->assertArrayHasKey('store_pending', $caller->latestCallParameters);
        $this->assertSame($command->storePending()->asBool(), $caller->latestCallParameters['store_pending']);
    }
}
