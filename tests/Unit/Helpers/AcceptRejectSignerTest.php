<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use DateTimeImmutable;
use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Helpers\AcceptRejectSigner;

use PhpCfdi\Finkok\Tests\TestCase;

class AcceptRejectSignerTest extends TestCase
{
    public function testCreateAndSign(): void
    {
        $uuid = '4CE93193-9E57-4BB0-9E03-09BAB53D392E';
        $date = new DateTimeImmutable('2019-01-13 14:15:16');
        $answer = CancelAnswer::accept();

        $signer = new AcceptRejectSigner($uuid, $answer, $date);
        $this->assertSame($uuid, $signer->uuid());
        $this->assertSame($answer, $signer->answer());
        $this->assertSame($date, $signer->dateTime());
        $this->assertSame($signer::DEFAULT_PACRFC, $signer->pacRfc());

        $signed = $signer->sign($this->createCsdCredential());

        $this->assertXmlStringEqualsXmlFile($this->filePath('cancel-accept-reject-format.xml'), $signed);
    }
}
