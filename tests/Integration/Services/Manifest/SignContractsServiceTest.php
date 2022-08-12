<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Manifest;

use DateTimeImmutable;
use PhpCfdi\Finkok\Helpers\DocumentSigner;
use PhpCfdi\Finkok\Services\Manifest\GetContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\GetContractsService;
use PhpCfdi\Finkok\Services\Manifest\SignContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\SignContractsResult;
use PhpCfdi\Finkok\Services\Manifest\SignContractsService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class SignContractsServiceTest extends IntegrationTestCase
{
    private function consumeSignContracts(
        string $rfc,
        string $certificateFile,
        string $privateKeyFile,
        string $passPhrase
    ): SignContractsResult {
        $settings = $this->createSettingsFromEnvironment();

        $cmdGetContracts = new GetContractsCommand(
            $rfc,
            'Empresa Conocida SA de CV',
            'Cuauhtémoc #123, Colonia Centro, Villahermosa, Tabasco. CP 86000',
            'legal@empresa-conocida.mx',
            $this->getenv('FINKOK_SNID')
        );

        $srvGetContracts = new GetContractsService($settings);
        $resGetContracts = $srvGetContracts->obtainContracts($cmdGetContracts);

        $privacy = $resGetContracts->privacy();
        $contract = $resGetContracts->contract();

        $signDate = new DateTimeImmutable('now');
        $privacyDocument = new DocumentSigner($rfc, $signDate, $privacy);
        $contractDocument = new DocumentSigner($rfc, $signDate, $contract);

        $cmdSignContracts = new SignContractsCommand(
            $this->getenv('FINKOK_SNID'),
            $privacyDocument->sign($certificateFile, $privateKeyFile, $passPhrase),
            $contractDocument->sign($certificateFile, $privateKeyFile, $passPhrase)
        );

        $srvSignContracts = new SignContractsService($settings);
        return $srvSignContracts->sendSignedContracts($cmdSignContracts);
    }

    public function testSignContractsUsingCsd(): void
    {
        $certificateFile = $this->filePath('certs/EKU9003173C9.cer.pem');
        $privateKeyFile = $this->filePath('certs/EKU9003173C9.key.pem');
        $passPhrase = trim($this->fileContentPath('certs/EKU9003173C9.password.bin'));

        $result = $this->consumeSignContracts('EKU9003173C9', $certificateFile, $privateKeyFile, $passPhrase);
        $this->assertSame('La firma del Aviso de privacidad no es FIEL', trim($result->message()));
        $this->assertFalse($result->success());
    }

    public function testSignContractsUsingNotRegisteredRfc(): void
    {
        $certificateFile = $this->filePath('fiel/EKU9003173C9.cer.pem');
        $privateKeyFile = $this->filePath('fiel/EKU9003173C9.key.pem');
        $passPhrase = trim($this->fileContentPath('fiel/EKU9003173C9.password.bin'));

        $result = $this->consumeSignContracts('COSC8001137NA', $certificateFile, $privateKeyFile, $passPhrase);
        $this->assertSame('No se registro el RFC bajo la cuenta de Finkok', $result->message());
        $this->assertFalse($result->success());
    }

    public function testSignContractsUsingFiel(): void
    {
        $certificateFile = $this->filePath('fiel/EKU9003173C9.cer.pem');
        $privateKeyFile = $this->filePath('fiel/EKU9003173C9.key.pem');
        $passPhrase = trim($this->fileContentPath('fiel/EKU9003173C9.password.bin'));

        $result = $this->consumeSignContracts('EKU9003173C9', $certificateFile, $privateKeyFile, $passPhrase);
        $this->assertSame('Proceso exitoso', $result->message());
        $this->assertTrue($result->success());
    }
}
