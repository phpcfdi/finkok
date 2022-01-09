<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Manifest;

use PhpCfdi\Finkok\Definitions\SignedDocumentFormat;
use PhpCfdi\Finkok\Services\Manifest\GetSignedContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\GetSignedContractsService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class GetSignedContractsServiceTest extends IntegrationTestCase
{
    private function createService(): GetSignedContractsService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new GetSignedContractsService($settings);
    }

    /** @return array<string, mixed> */
    public function providerGetSignedContracts(): array
    {
        return [
            'xml' => [SignedDocumentFormat::xml(), 'text/xml'],
            'pdf' => [SignedDocumentFormat::pdf(), 'application/pdf'],
        ];
    }

    /**
     * @param SignedDocumentFormat $format
     * @param string $expectedMimeType
     * @dataProvider providerGetSignedContracts
     */
    public function testGetSignedContracts(SignedDocumentFormat $format, string $expectedMimeType): void
    {
        $command = new GetSignedContractsCommand(
            $this->getenv('FINKOK_SNID'),
            'EKU9003173C9',
            $format
        );

        $service = $this->createService();
        $result = $service->getSignedContracts($command);

        $this->assertTrue($result->success());
        $this->assertNotEmpty($result->privacy());
        $this->assertNotEmpty($result->contract());
        $this->assertEmpty($result->error());

        $this->assertSame($expectedMimeType, $this->obtainMimeType($result->privacy()), 'Privacy invalid type');
        $this->assertSame($expectedMimeType, $this->obtainMimeType($result->contract()), 'Contract invalid type');
    }

    private function obtainMimeType(string $contents): string
    {
        $temporary = tempnam('', '') ?: '';
        if ('' === $temporary) {
            return '';
        }
        try {
            file_put_contents($temporary, $contents);
            $type = mime_content_type($temporary) ?: '';
        } finally {
            unlink($temporary);
        }
        return $type;
    }
}
