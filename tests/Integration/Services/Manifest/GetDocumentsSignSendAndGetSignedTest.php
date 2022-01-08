<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Manifest;

use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\QuickFinkok;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class GetDocumentsSignSendAndGetSignedTest extends IntegrationTestCase
{
    private function createFielCredential(): Credential
    {
        $certificateFile = $this->filePath('fiel/EKU9003173C9.cer.pem');
        $privateKeyFile = $this->filePath('fiel/EKU9003173C9.key.pem');
        $passPhrase = trim($this->fileContentPath('fiel/EKU9003173C9.password.bin'));
        return Credential::openFiles($certificateFile, $privateKeyFile, $passPhrase);
    }

    public function testFullProcess(): void
    {
        $finkok = new QuickFinkok($this->createSettingsFromEnvironment());

        $fiel = $this->createFielCredential();
        $address = 'Cuauhtémoc #123, Colonia Centro, Villahermosa, Tabasco. CP 86000';
        $email = 'legal@empresa-conocida.mx';
        $snid = strval(getenv('FINKOK_SNID') ?: '');

        $signedContracts = $finkok->customerSignAndSendContracts($fiel, $snid, $address, $email);
        $this->assertTrue($signedContracts->success());

        $currentContracts = $finkok->customerGetSignedContracts($snid, $fiel->rfc());
        $this->assertTrue($currentContracts->success());
    }
}
