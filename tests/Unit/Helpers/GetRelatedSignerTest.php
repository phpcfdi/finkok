<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\Finkok\Helpers\GetRelatedSigner;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetRelatedSignerTest extends TestCase
{
    public function testCreateAndSign(): void
    {
        $uuid = '4CE93193-9E57-4BB0-9E03-09BAB53D392E';
        $role = RfcRole::issuer();

        $signer = new GetRelatedSigner($uuid, $role);
        $this->assertSame($uuid, $signer->uuid());
        $this->assertSame($role, $signer->role());
        $this->assertSame($signer::DEFAULT_PACRFC, $signer->pacRfc());

        $signed = $signer->sign($this->createCsdCredential());

        /** @see tests/_files/cancel-get-related-signature-format.xml */
        $this->assertXmlStringEqualsXmlFile($this->filePath('cancel-get-related-signature-format.xml'), $signed);
    }
}
