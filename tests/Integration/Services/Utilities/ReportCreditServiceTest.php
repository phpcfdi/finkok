<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\ReportCreditCommand;
use PhpCfdi\Finkok\Services\Utilities\ReportCreditResult;
use PhpCfdi\Finkok\Services\Utilities\ReportCreditService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class ReportCreditServiceTest extends IntegrationTestCase
{
    public function testReportCreditService(): void
    {
        $command = new ReportCreditCommand('TCM970625MB1');
        $settings = $this->createSettingsFromEnvironment();
        $service = new ReportCreditService($settings);
        $result = $service->reportCredit($command);
        // cannot assert anything since credit is unknown
        // this may change if implement credit services
        $this->assertInstanceOf(ReportCreditResult::class, $result);
    }
}
