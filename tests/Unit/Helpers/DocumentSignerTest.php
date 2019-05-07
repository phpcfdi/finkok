<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use DateTimeImmutable;
use PhpCfdi\Finkok\Helpers\DocumentSigner;

use PhpCfdi\Finkok\Tests\TestCase;

class DocumentSignerTest extends TestCase
{
    public function testCreateDocumentSigner(): void
    {
        $rfc = 'COSC8001137NA';
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $content = 'Lorem Ipsum';

        $certificate = $this->filePath('fiel/TCM970625MB1.cer.pem');
        $privateKey = $this->filePath('fiel/TCM970625MB1.key.pem');
        $passPhrase = trim($this->fileContentPath('fiel/TCM970625MB1.password.bin'));

        $docSigner = new DocumentSigner($rfc, $date, $content);
        $this->assertSame($rfc, $docSigner->rfc());
        $this->assertSame($date, $docSigner->date());
        $this->assertSame($content, $docSigner->content());
        $signed = $docSigner->sign($certificate, $privateKey, $passPhrase);

        // the comparison does not check white spacing,
        // lorem-ipsum-signed.xml is formatted for better reading
        $this->assertXmlStringEqualsXmlFile($this->filePath('lorem-ipsum-signed.xml'), $signed);
    }
}
