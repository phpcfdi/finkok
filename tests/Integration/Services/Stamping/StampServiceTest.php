<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class StampServiceTest extends IntegrationTestCase
{
    protected function createService(): StampService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new StampService($settings);
    }

    public function testStampValidPrecfdi(): void
    {
        $command = $this->newStampingCommand();
        $service = $this->createService();
        $result = $service->stamp($command);

        $this->assertSame('Comprobante timbrado satisfactoriamente', $result->statusCode());
        $this->assertNotEmpty($result->xml());
        $this->assertNotEmpty($result->uuid());
        $this->assertStringContainsString($result->uuid(), $result->xml());
    }

    public function testStampPreviouslyCreatedCfdiReturnsErrorCode307(): void
    {
        $firstResult = $this->currentCfdi();

        $secondResult = $this->createService()->stamp($this->currentStampingCommand());
        $this->assertNotNull(
            $secondResult->alerts()->findByErrorCode('307'),
            'Finkok must alert that it was previously stamped'
        );

        $this->assertSame(
            $firstResult->uuid(),
            $secondResult->uuid(),
            'Finkok does not return the same UUID for duplicated stamp call'
        );
    }

    public function testStampPreviouslyCreatedCfdiReturnsErrorCode707(): void
    {
        // call first to cachedStamped to use previous stamp or create a new one
        $this->currentCfdi();

        $service = $this->createService();
        $currentCfdiStampCommand = new StampingCommand($this->currentCfdi()->xml());
        $secondResult = $service->stamp($currentCfdiStampCommand);
        $this->assertNotNull(
            $secondResult->alerts()->findByErrorCode('707'),
            'Finkok must alert that it contains and existing TFD'
        );
    }

    public function testStampPrecfdiWithErrorInDate(): void
    {
        $command = $this->newStampingCommandInvalidDate();

        $service = $this->createService();
        $result = $service->stamp($command);

        $this->assertGreaterThan(0, $result->alerts()->count());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }

    /**
     * @testWith ["&"]
     *           ["&amp;"]
     *           ["&Aacute;"]
     *           ["&copy;"]
     *           ["P&G"]
     */
    public function testStampPrecfdiWithConceptoDescriptionCaracteresCodificados(string $descriptionPart): void
    {
        $description = sprintf(
            'Mousepad con leyenda «%s es un texto XML válido»',
            htmlentities($descriptionPart, ENT_XML1)
        );
        $expectedXmlEncodedText = htmlentities($description, ENT_XML1);

        $randomPreCfdi = new RandomPreCfdi();
        $helper = $randomPreCfdi->createHelper();
        $helper->setConceptoDescription($description);
        $preCfdi = $helper->create();
        $command = new StampingCommand($preCfdi);

        $service = $this->createService();
        $result = $service->stamp($command);

        $this->assertCount(0, $result->alerts());
        $this->assertNotEmpty($result->uuid());

        $this->assertStringContainsString($expectedXmlEncodedText, $result->xml());
    }
}
