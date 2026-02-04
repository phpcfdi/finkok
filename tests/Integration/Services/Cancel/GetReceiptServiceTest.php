<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Definitions\ReceiptType;
use PhpCfdi\Finkok\Services\Cancel\GetReceiptCommand;
use PhpCfdi\Finkok\Services\Cancel\GetReceiptService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class GetReceiptServiceTest extends IntegrationTestCase
{
    protected function createService(): GetReceiptService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new GetReceiptService($settings);
    }

    public function testDownloadOnNonExistentUuid(): void
    {
        $service = $this->createService();

        $command = new GetReceiptCommand(
            'EKU9003173C9',
            '12345678-1234-1234-1234-123456789012',
            ReceiptType::cancellation(),
        );

        $result = $service->download($command);
        $this->assertSame('There is not a Cancellation receipt with that info', $result->error());
    }
}
