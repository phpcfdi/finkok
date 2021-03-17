<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\DownloadXmlCommand;
use PhpCfdi\Finkok\Services\Utilities\DownloadXmlService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class DownloadXmlServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('utilities-getxml-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new DownloadXmlService($settings);

        $command = new DownloadXmlCommand('x-uuid', 'x-rfc', 'x-type');
        $result = $service->downloadXml($command);
        $this->assertSame('predefined-xml', $result->xml());
        $this->assertSame('predefined-error', $result->error());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get_xml', $caller->latestCallMethodName);
        $this->assertArrayHasKey('uuid', $caller->latestCallParameters);
        $this->assertSame($command->uuid(), $caller->latestCallParameters['uuid']);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('invoice_type', $caller->latestCallParameters);
        $this->assertSame($command->type(), $caller->latestCallParameters['invoice_type']);
    }
}
