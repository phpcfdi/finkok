<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Definitions\CancelStorePending;
use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\Services\Retentions\CancelSignatureCommand;
use PhpCfdi\Finkok\Services\Retentions\CancelSignatureService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class CancelSignatureServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode(TestCase::fileContentPath('retentions-cancelsignature-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new CancelSignatureService($settings);

        $command = new CancelSignatureCommand('x-xml', CancelStorePending::yes());
        $result = $service->cancelSignature($command);
        $this->assertCount(2, $result->documents());
        $this->assertSame('voucher', $result->voucher());
        $this->assertSame('LAN7008173R5', $result->rfc());
        $this->assertSame('2019-04-05 16:29:47.138032', $result->date());
        $this->assertSame('304', $result->statusCode());
        $this->assertSame('x-seguimiento-cancelacion', $result->tracing());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertStringEndsWith(Services::retentions()->value(), $soapFactory->latestWsdlLocation);
        $this->assertSame('cancel_signature', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame($command->xml(), $caller->latestCallParameters['xml']);
        $this->assertArrayHasKey('store_pending', $caller->latestCallParameters);
        $this->assertSame($command->storePending()->asBool(), $caller->latestCallParameters['store_pending']);
    }
}
