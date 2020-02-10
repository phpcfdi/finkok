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
            // Break the loop if SAT return an error, but the error is not:
            // 2001 - No Existen cfdi relacionados al folio fiscal.
            // Testing only: in the wild it is expected to ask for related several seconds after the CFDI were created
            if ('' !== $result->error() && '2001' !== substr($result->error(), 0, 4)) {
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

    /**
     * Use this method to test specifics UUID, useful for debugging
     * You have to fill the UUIDS, is expected that 2 relates to 1 and 3 relates to 2, as in previous test
     *
     * To enable this test you must add "@test" annotation
     */
    public function manualConsumeServiceWithRelated(): void
    {
        $first = '4F72BDE6-36FD-4D88-B740-78561BA9B6B3';
        $second = '527002DF-49FB-4380-886F-5667900722AD';
        $third = '2A403C20-1F8B-43E4-B15F-96E19CDB6760';

        $command = $this->createGetRelatedSignatureCommand($second);
        $service = $this->createService();
        $result = $service->getRelatedSignature($command);

        $this->assertNotEmpty($result->parents()->first()->uuid(), 'Did not receive the parent UUID');
        $this->assertSame($third, $result->parents()->first()->uuid());

        $this->assertNotEmpty($result->children()->first()->uuid(), 'Did not receive the child UUID');
        $this->assertSame($first, $result->children()->first()->uuid());

        $this->assertEmpty($result->error(), 'The result must not contain any error');
    }
}
