<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use DateTimeImmutable;
use PhpCfdi\Finkok\Helpers\CancelSigner;

use PhpCfdi\Finkok\Tests\TestCase;

final class CancelSignerTest extends TestCase
{
    public function testCreateAndSign(): void
    {
        $uuids = [
            '4CE93193-9E57-4BB0-9E03-09BAB53D392E',
            '4CE93193-9E57-4BB0-9E03-F896B148A146',
        ];
        $date = new DateTimeImmutable('2019-01-13 14:15:16');

        $signer = new CancelSigner($uuids, $date);
        $this->assertSame($uuids, $signer->uuids());
        $this->assertSame($date, $signer->dateTime());

        $signed = $signer->sign($this->createCsdCredential());

        $this->assertXmlStringEqualsXmlFile($this->filePath('cancel-cancelsignature-format.xml'), $signed);
    }
}
