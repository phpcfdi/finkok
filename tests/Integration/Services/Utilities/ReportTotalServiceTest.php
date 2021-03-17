<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Utilities;

use DateTime;
use PhpCfdi\Finkok\Services\Utilities\ReportTotalCommand;
use PhpCfdi\Finkok\Services\Utilities\ReportTotalService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class ReportTotalServiceTest extends IntegrationTestCase
{
    public function testReportTotalServiceInPast(): void
    {
        $date = strtotime('previous month');
        $thisYear = intval(date('Y', $date));
        $thisMonth = intval(date('m', $date));
        $command = new ReportTotalCommand('EKU9003173C9', 'I', $thisYear, $thisMonth, $thisYear, $thisMonth);
        $settings = $this->createSettingsFromEnvironment();
        $service = new ReportTotalService($settings);
        $result = $service->reportTotal($command);
        $this->assertSame('', $result->error(), 'It was not expected to receive an error');
        if ('' !== $result->total()) {
            $this->assertTrue(is_numeric($result->total()), 'It was not expected to receive a non-numeric total');
        }
        $this->assertSame('EKU9003173C9', $result->rfc());
    }

    public function testReportTotalServiceCurrentMonth(): void
    {
        $today = new DateTime('today');
        $year = intval($today->format('Y'));
        $month = intval($today->format('m'));
        $command = new ReportTotalCommand('EKU9003173C9', 'I', $year, $month);
        $settings = $this->createSettingsFromEnvironment();
        $service = new ReportTotalService($settings);
        $result = $service->reportTotal($command);
        $this->assertNotSame('', $result->total());
        $this->assertSame('EKU9003173C9', $result->rfc());
    }
}
