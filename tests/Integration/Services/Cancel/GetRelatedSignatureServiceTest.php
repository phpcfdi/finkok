<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\Finkok\Helpers\GetRelatedSigner;
use PhpCfdi\Finkok\Services\Cancel\GetRelatedSignatureCommand;
use PhpCfdi\Finkok\Services\Cancel\GetRelatedSignatureService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class GetRelatedSignatureServiceTest extends IntegrationTestCase
{
    protected function createService(): GetRelatedSignatureService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new GetRelatedSignatureService($settings);
    }

    protected function createGetRelatedSignatureCommand(string $uuid): GetRelatedSignatureCommand
    {
        $signer = new GetRelatedSigner($uuid, RfcRole::issuer());
        $xml = $signer->sign($this->createCsdCredential());
        return new GetRelatedSignatureCommand($xml);
    }

    public function testConsumeServiceWithNonExistentUuid(): void
    {
        $uuid = '11111111-2222-3333-4444-000000000001';
        $command = $this->createGetRelatedSignatureCommand($uuid);
        $service = $this->createService();
        $result = $service->getRelatedSignature($command);
        $this->assertSame("UUID: $uuid No Encontrado", $result->error());
    }

    public function newStampingCommandRelated(string $relation, string $uuid): StampingCommand
    {
        $random = new RandomPreCfdi();
        $helper = $random->createHelper();
        $helper->setRelatedCfdis($relation, $uuid);
        $precfdi = $helper->create();
        return new StampingCommand($precfdi);
    }

    public function testConsumeServiceWithRelated(): void
    {
        // first is child of second, second is parent of first
        // second is child of third, third is parent of second
        // for second: parent is third, child is first
        $first = $this->stamp($this->newStampingCommand());
        $second = $this->stamp($this->newStampingCommandRelated('04', $first->uuid()));
        $third = $this->stamp($this->newStampingCommandRelated('04', $second->uuid()));

        $this->checkCanGetSatStatusOrFail($first->xml(), 'Unable to create first CFDI for testing get related');
        $this->checkCanGetSatStatusOrFail($second->xml(), 'Unable to create second CFDI for testing get related');
        $this->checkCanGetSatStatusOrFail($third->xml(), 'Unable to create third CFDI for testing get related');

        $command = $this->createGetRelatedSignatureCommand($second->uuid());
        $service = $this->createService();
        $maxtime = strtotime('+5 minutes');
        do {
            $result = $service->getRelatedSignature($command);
            // SAT is not resolving correctly because I ask for related immediately
            if ('' !== $result->error()) {
                break;
            }
            if (1 === $result->parents()->count() && 1 === $result->children()->count()) {
                break;
            }
            if (time() > $maxtime) {
                $this->fail('After 5 minutes consuming GetRelatedSignatureService didnt get related from SAT');
                break;
            }
            sleep(5);
        } while (true);

        $this->assertSame($third->uuid(), $result->parents()->first()->uuid());
        $this->assertSame($first->uuid(), $result->children()->first()->uuid());
        $this->assertEmpty($result->error());
    }
}
