<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\DownloadXmlCommand;
use PhpCfdi\Finkok\Services\Utilities\DownloadXmlService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class DownloadXmlServiceTest extends IntegrationTestCase
{
    protected function createService(): DownloadXmlService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new DownloadXmlService($settings);
    }

    public function testServiceWithNonExistentUuid(): void
    {
        $service = $this->createService();

        $command = new DownloadXmlCommand('01234567-0123-0123-0123-012345678901', 'EKU9003173C9', 'I');
        $result = $service->downloadXml($command);
        $this->assertSame('UUID Does not Exists', $result->error());
    }

    public function testStampAndConsumeStampedImmediately(): void
    {
        $previousStamp = $this->currentCfdi();
        $this->assertNotEmpty($previousStamp->uuid(), 'Finkok did not create CFDI');

        $service = $this->createService();
        $command = new DownloadXmlCommand($previousStamp->uuid(), 'EKU9003173C9', 'I');
        $result = $service->downloadXml($command);

        $this->assertXmlStringEqualsXmlString(
            $previousStamp->xml(),
            $result->xml(),
            'Finkok does not return equal XML for recently created stamp using get_xml'
        );
        $this->assertEmpty($result->error(), 'Finkok must not return an error');
        // see finkok ticket: https://support.finkok.com/support/tickets/41438
        // it was not returning the xml header
        $this->assertSame(
            $previousStamp->xml(),
            $result->xml(),
            'Finkok does not return exactly the same XML for recently created stamp using get_xml'
        );
    }
}
