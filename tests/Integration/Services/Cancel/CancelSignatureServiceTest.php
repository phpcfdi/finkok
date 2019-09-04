<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\CancelSignatureService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;
use PhpCfdi\XmlCancelacion\Capsule;

class CancelSignatureServiceTest extends IntegrationTestCase
{
    protected function createService(): CancelSignatureService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new CancelSignatureService($settings);
    }

    public function testCancelNonExistentUuid(): void
    {
        $cancelData = new Capsule('EKU9003173C9', ['12345678-1234-1234-1234-123456789012']);
        $command = $this->createCancelSignatureCommandFromCapsule($cancelData);

        $service = $this->createService();

        $result = $service->cancelSignature($command);
        $this->assertSame('UUID Not Found', $result->statusCode());
    }
}
