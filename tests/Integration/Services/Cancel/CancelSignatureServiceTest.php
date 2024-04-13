<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\CancelSignatureService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;

final class CancelSignatureServiceTest extends IntegrationTestCase
{
    protected function createService(): CancelSignatureService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new CancelSignatureService($settings);
    }

    public function testCancelNonExistentUuid(): void
    {
        $uuid = '12345678-1234-1234-1234-123456789012';
        $expectedStatusCode = sprintf('UUID: %s No Encontrado', $uuid);

        $command = $this->createCancelSignatureCommandFromDocument(
            CancelDocument::newWithErrorsUnrelated($uuid)
        );
        $service = $this->createService();
        $result = $service->cancelSignature($command);

        $this->assertSame($expectedStatusCode, $result->statusCode());
    }
}
