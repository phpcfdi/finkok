<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use DateTimeImmutable;
use PhpCfdi\Finkok\Services\Utilities\ReportUuidCommand;
use PhpCfdi\Finkok\Services\Utilities\ReportUuidService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class ReportUuidServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('utilities-report-uuid-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new ReportUuidService($settings);

        $until = new DateTimeImmutable('now');
        $since = $until->modify('-1 seconds');
        $command = new ReportUuidCommand('x-rfc', 'I', $since, $until);
        $result = $service->reportUuid($command);
        $this->assertCount(2, $result->items());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('report_uuid', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('invoice_type', $caller->latestCallParameters);
        $this->assertSame($command->type(), $caller->latestCallParameters['invoice_type']);
        $this->assertArrayHasKey('date_from', $caller->latestCallParameters);
        $this->assertSame($command->sinceString(), $caller->latestCallParameters['date_from']);
        $this->assertArrayHasKey('date_to', $caller->latestCallParameters);
        $this->assertSame($command->untilString(), $caller->latestCallParameters['date_to']);
    }
}
