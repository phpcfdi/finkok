<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Helpers\AcceptRejectSigner;
use PhpCfdi\Finkok\Services\Cancel\AcceptRejectSignatureCommand;
use PhpCfdi\Finkok\Services\Cancel\AcceptRejectSignatureService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class AcceptRejectSignatureServiceTest extends IntegrationTestCase
{
    protected function createService(): AcceptRejectSignatureService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new AcceptRejectSignatureService($settings);
    }

    protected function createAcceptRejectSignatureCommand(
        string $uuid,
        CancelAnswer $answer
    ): AcceptRejectSignatureCommand {
        $signer = new AcceptRejectSigner($uuid, $answer);
        $xml = $signer->sign($this->createCsdCredential());
        return new AcceptRejectSignatureCommand($xml);
    }

    public function testConsumeServiceWithNonExistentUuid(): void
    {
        $uuid = '11111111-2222-3333-4444-000000000001';
        $command = $this->createAcceptRejectSignatureCommand($uuid, CancelAnswer::accept());
        $service = $this->createService();
        $result = $service->acceptRejectSignature($command);
        $this->assertSame("UUID: $uuid No Encontrado", $result->error());
    }
}
