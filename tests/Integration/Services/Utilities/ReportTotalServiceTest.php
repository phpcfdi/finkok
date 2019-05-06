<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Utilities;

use DateTime;
use PhpCfdi\Finkok\Services\Utilities\ReportTotalCommand;
use PhpCfdi\Finkok\Services\Utilities\ReportTotalService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class ReportTotalServiceTest extends IntegrationTestCase
{
    public function testReportTotalServiceInPast(): void
    {
        $command = new ReportTotalCommand('TCM970625MB1', 'I', 2019, 1, 2019, 4);
        $settings = $this->createSettingsFromEnvironment();
        $service = new ReportTotalService($settings);
        $result = $service->reportTotal($command);
        $this->assertNotSame('', $result->total());
        $this->assertSame('TCM970625MB1', $result->rfc());
    }

    public function testReportTotalServiceCurrentMonth(): void
    {
        $today = new DateTime('today');
        $year = intval($today->format('Y'));
        $month = intval($today->format('m'));
        $command = new ReportTotalCommand('TCM970625MB1', 'I', $year, $month);
        $settings = $this->createSettingsFromEnvironment();
        $service = new ReportTotalService($settings);
        $result = $service->reportTotal($command);
        $this->assertNotSame('', $result->total());
        $this->assertSame('TCM970625MB1', $result->rfc());
    }
}
