<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\ReportCreditCommand;
use PhpCfdi\Finkok\Services\Utilities\ReportCreditService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class ReportCreditServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('utilities-report-credit-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new ReportCreditService($settings);

        $command = new ReportCreditCommand('x-rfc');
        $result = $service->reportCredit($command);
        $this->assertCount(2, $result->items());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('report_credit', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
    }
}
