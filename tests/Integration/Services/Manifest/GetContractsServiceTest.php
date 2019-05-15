<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\GetContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\GetContractsService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class GetContractsServiceTest extends IntegrationTestCase
{
    private function createService(): GetContractsService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new GetContractsService($settings);
    }

    public function testObtainContracts(): void
    {
        $command = new GetContractsCommand(
            'TCM970625MB1',
            'Empresa Conocida SA de CV',
            'Cuauhtémoc #123, Colonia Centro, Villahermosa, Tabasco. CP 86000',
            'legal@empresa-conocida.mx'
        );

        $service = $this->createService();
        $result = $service->obtainContracts($command);

        $this->assertTrue($result->success());
        $this->assertNotEmpty($result->privacy());
        $this->assertNotEmpty($result->contract());
        $this->assertEmpty($result->error());

        $privacy = strval(base64_decode($result->privacy()));
        $contract = strval(base64_decode($result->contract()));

        $this->assertNotEmpty($privacy, 'Cannot decode privacy statement');
        $this->assertNotEmpty($contract, 'Cannot decode contract statement');
        $this->assertStringContainsString($command->rfc(), $contract);
        $this->assertStringContainsString($command->name(), $contract);
        $this->assertStringContainsString($command->email(), $contract);
        $this->assertStringContainsString($command->address(), $contract);
    }
}
