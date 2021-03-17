<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\ReportTotalCommand;
use PhpCfdi\Finkok\Services\Utilities\ReportTotalService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class ReportTotalServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('utilities-report-total-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new ReportTotalService($settings);

        $command = new ReportTotalCommand('EKU9003173C9', 'R', 2019, 1, 2019, 3);
        $result = $service->reportTotal($command);
        $this->assertSame('123', $result->total());
        $this->assertSame('EKU9003173C9', $result->rfc());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('report_total', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('date_from', $caller->latestCallParameters);
        $this->assertSame($command->startString(), $caller->latestCallParameters['date_from']);
        $this->assertArrayHasKey('date_to', $caller->latestCallParameters);
        $this->assertSame($command->endString(), $caller->latestCallParameters['date_to']);
        $this->assertArrayHasKey('invoice_type', $caller->latestCallParameters);
        $this->assertSame($command->type(), $caller->latestCallParameters['invoice_type']);
    }
}
