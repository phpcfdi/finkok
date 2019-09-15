<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\Finkok\Helpers\GetRelatedSigner;

use PhpCfdi\Finkok\Tests\TestCase;

class GetRelatedSignerTest extends TestCase
{
    public function testCreateSignature(): void
    {
        $certificate = $this->filePath('certs/EKU9003173C9.cer.pem');
        $privateKey = $this->filePath('certs/EKU9003173C9.key.pem');
        $passPhrase = trim($this->fileContentPath('certs/EKU9003173C9.password.bin'));

        $signer = new GetRelatedSigner('4CE93193-9E57-4BB0-9E03-09BAB53D392E', 'EKU9003173C9', RfcRole::emitter());
        $signed = $signer->sign($certificate, $privateKey, $passPhrase);
        $this->assertXmlStringEqualsXmlFile($this->filePath('cancel-get-related-signature-raw.xml'), $signed);
    }
}
