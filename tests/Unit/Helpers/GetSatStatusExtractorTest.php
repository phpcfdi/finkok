<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use PhpCfdi\Finkok\Helpers\GetSatStatusExtractor;
use PhpCfdi\Finkok\Tests\TestCase;
use RuntimeException;

final class GetSatStatusExtractorTest extends TestCase
{
    public function testConstructWithEmptyData(): void
    {
        $extractor = new GetSatStatusExtractor([]);
        $command = $extractor->buildCommand();
        $this->assertEmpty($command->rfcIssuer());
        $this->assertEmpty($command->rfcRecipient());
        $this->assertEmpty($command->uuid());
        $this->assertEmpty($command->total());
    }

    public function testConstructWithFakeCfdi33(): void
    {
        $fakeCfdi = <<<EOT
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                              xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
                              xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital"
                              Version="3.3" Total="123.45" Sello="">
                <cfdi:Emisor Rfc="EKU9003173C9"/>
                <cfdi:Receptor Rfc="COSC8001137NA"/>
                <cfdi:Complemento>
                    <tfd:TimbreFiscalDigital UUID="12345678-1234-1234-1234-000000000001"/>
                </cfdi:Complemento>
            </cfdi:Comprobante>
            EOT;
        $extractor = GetSatStatusExtractor::fromXmlString($fakeCfdi);
        $command = $extractor->buildCommand();
        $this->assertSame('EKU9003173C9', $command->rfcIssuer());
        $this->assertSame('COSC8001137NA', $command->rfcRecipient());
        $this->assertSame('12345678-1234-1234-1234-000000000001', $command->uuid());
        $this->assertSame('123.45', $command->total());
    }

    public function testConstructWithOtherXml(): void
    {
        $fakeCfdi = '<xml/>';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to obtain the expression values');
        GetSatStatusExtractor::fromXmlString($fakeCfdi);
    }
}
