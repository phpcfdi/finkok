<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Definitions\ReceiptType;
use PhpCfdi\Finkok\Services\Cancel\GetReceiptCommand;
use PhpCfdi\Finkok\Services\Cancel\GetReceiptService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetReceiptServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('cancel-get-receipt-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new GetReceiptService($settings);

        $command = new GetReceiptCommand('x-rfc', 'x-uuid', ReceiptType::cancellation());
        $result = $service->download($command);
        $this->assertEquals($preparedResult, $result->rawData()); // this is fake, no need to test

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get_receipt', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('uuid', $caller->latestCallParameters);
        $this->assertSame($command->uuid(), $caller->latestCallParameters['uuid']);
        $this->assertArrayHasKey('type', $caller->latestCallParameters);
        $this->assertSame($command->type()->value(), $caller->latestCallParameters['type']);
    }
}
