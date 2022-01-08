<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration;

use DateTimeImmutable;
use PhpCfdi\Finkok\Helpers\CancelSigner;
use PhpCfdi\Finkok\Helpers\GetSatStatusExtractor;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureCommand;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusResult;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusService;
use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use RuntimeException;

abstract class IntegrationTestCase extends TestCase
{
    /** @var StampingResult|null */
    protected static $staticCurrentStampingResult;

    /** @var StampingCommand|null */
    protected static $staticCurrentStampingCommand;

    public function newStampingCommand(): StampingCommand
    {
        return new StampingCommand((new RandomPreCfdi())->createValid());
    }

    public function newStampingCommandInvalidDate(): StampingCommand
    {
        return new StampingCommand((new RandomPreCfdi())->createInvalidByDate());
    }

    public function currentStampingCommand(): StampingCommand
    {
        if (null === static::$staticCurrentStampingCommand) {
            static::$staticCurrentStampingCommand = $this->newStampingCommand();
        }
        return static::$staticCurrentStampingCommand;
    }

    public function quickStamp(StampingCommand $stampingCommand): StampingResult
    {
        $settings = $this->createSettingsFromEnvironment();
        $service = new QuickStampService($settings);
        return $service->quickstamp($stampingCommand);
    }

    public function stamp(StampingCommand $stampingCommand): StampingResult
    {
        $settings = $this->createSettingsFromEnvironment();
        $service = new StampService($settings);
        return $service->stamp($stampingCommand);
    }

    /**
     * El método utilizado para crear el estampado es QuickStamp
     *
     * @return StampingResult
     */
    public function currentCfdi(): StampingResult
    {
        if (null === static::$staticCurrentStampingResult) {
            static::$staticCurrentStampingResult = $this->quickStamp($this->currentStampingCommand());
        }
        return static::$staticCurrentStampingResult;
    }

    public function createGetSatStatusCommandFromCfdiContents(string $xmlContents): GetSatStatusCommand
    {
        return GetSatStatusExtractor::fromXmlString($xmlContents)->buildCommand();
    }

    protected function createCancelSignatureCommandFromDocument(
        CancelDocument $document,
        ?DateTimeImmutable $dateTime = null
    ): CancelSignatureCommand {
        $credential = $this->createCsdCredential();
        $signer = new CancelSigner(new CancelDocuments($document), $dateTime);
        return new CancelSignatureCommand($signer->sign($credential));
    }

    protected function checkCanGetSatStatusOrFail(
        string $cfdiContents,
        string $exceptionMessage = '',
        int $waitSeconds = 120
    ): GetSatStatusResult {
        $service = new GetSatStatusService($this->createSettingsFromEnvironment());
        $command = $this->createGetSatStatusCommandFromCfdiContents($cfdiContents);
        $result = $service->queryUntilFoundOrTime($command, $waitSeconds);
        if ('No Encontrado' === $result->cfdi()) {
            if ('' === $exceptionMessage) {
                $exceptionMessage = sprintf('Cannot found UUID %s at SAT using GetSatStatusService', $command->uuid());
            }
            throw new RuntimeException($exceptionMessage);
        }
        return $result;
    }
}
