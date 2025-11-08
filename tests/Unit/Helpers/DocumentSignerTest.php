<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use DateTimeImmutable;
use DOMDocument;
use LogicException;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\Helpers\DocumentSigner;
use PhpCfdi\Finkok\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class DocumentSignerTest extends TestCase
{
    public function testCreateDocumentSigner(): void
    {
        $rfc = 'COSC8001137NA';
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $content = 'Lorem Ipsum';

        $certificate = $this->filePath('fiel/EKU9003173C9.cer.pem');
        $privateKey = $this->filePath('fiel/EKU9003173C9.key.pem');
        $passPhrase = trim($this->fileContentPath('fiel/EKU9003173C9.password.bin'));

        $docSigner = new DocumentSigner($rfc, $date, $content);
        $this->assertSame($rfc, $docSigner->rfc());
        $this->assertSame($date, $docSigner->date());
        $this->assertSame($content, $docSigner->content());
        $signed = $docSigner->sign($certificate, $privateKey, $passPhrase);

        // the comparison does not check white spacing,
        // lorem-ipsum-signed.xml is formatted for better reading
        /** @see tests/_files/lorem-ipsum-signed.xml */
        $this->assertXmlStringEqualsXmlFile($this->filePath('lorem-ipsum-signed.xml'), $signed);
    }

    public function testSignDocumentUsingCredentialWithoutRootElement(): void
    {
        $rfc = 'COSC8001137NA';
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $content = 'Lorem Ipsum';
        $docSigner = new DocumentSigner($rfc, $date, $content);

        /** @var Credential&MockObject $credential */
        $credential = $this->createMock(Credential::class);
        $documentWithoutRootElement = new DOMDocument();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The DOM Document does not contains a root element');
        $docSigner->signDocumentUsingCredential($documentWithoutRootElement, $credential);
    }
}
