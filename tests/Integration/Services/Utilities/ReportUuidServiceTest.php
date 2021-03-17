<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Utilities;

use DateTimeImmutable;
use PhpCfdi\Finkok\Services\Utilities\ReportUuidCommand;
use PhpCfdi\Finkok\Services\Utilities\ReportUuidResult;
use PhpCfdi\Finkok\Services\Utilities\ReportUuidService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class ReportUuidServiceTest extends IntegrationTestCase
{
    public function testReportUuidService(): void
    {
        $until = new DateTimeImmutable('now');
        $since = $until->setDate(intval($until->format('Y')), 1, 1)->setTime(0, 0, 0);
        $command = new ReportUuidCommand('EKU9003173C9', 'I', $since, $until);
        $settings = $this->createSettingsFromEnvironment();
        $service = new ReportUuidService($settings);
        $result = $service->reportUuid($command);
        // cannot assert anything since is unknown if it has contents
        $this->assertSame('', $result->error());
        $this->assertInstanceOf(ReportUuidResult::class, $result);
    }
}
