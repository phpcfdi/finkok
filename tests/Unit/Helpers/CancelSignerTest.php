<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use DateTimeImmutable;
use PhpCfdi\Finkok\Helpers\CancelSigner;

use PhpCfdi\Finkok\Tests\TestCase;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;

final class CancelSignerTest extends TestCase
{
    public function testCreateAndSign(): void
    {
        $documents = new CancelDocuments(
            CancelDocument::newWithErrorsUnrelated('62B00C5E-4187-4336-B569-44E0030DC729'),
        );
        $date = new DateTimeImmutable('2022-01-06 17:49:12');

        $signer = new CancelSigner($documents, $date);
        $this->assertSame($documents, $signer->documents());
        $this->assertSame($date, $signer->dateTime());

        $signed = $signer->sign($this->createCsdCredential());

        $this->assertXmlStringEqualsXmlFile($this->filePath('cancel-cancelsignature-format.xml'), $signed);
    }
}
