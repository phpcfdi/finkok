<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration;

use CfdiUtils\Cfdi;
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
use PhpCfdi\XmlCancelacion\Capsule;
use PhpCfdi\XmlCancelacion\CapsuleSigner;
use PhpCfdi\XmlCancelacion\Credentials;
use RuntimeException;

class IntegrationTestCase extends TestCase
{
    /** @var array */
    protected static $statics = [];

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
        if (! isset(static::$statics['stampingCommand'])) {
            static::$statics['stampingCommand'] = $this->newStampingCommand();
        }
        return static::$statics['stampingCommand'];
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
        if (! isset(static::$statics['cfdi'])) {
            static::$statics['cfdi'] = $this->quickStamp($this->currentStampingCommand());
        }
        return static::$statics['cfdi'];
    }

    public function createGetSatStatusCommandFromCfdiContents(string $xmlContents): GetSatStatusCommand
    {
        $cfdiReader = Cfdi::newFromString($xmlContents)->getQuickReader();
        return new GetSatStatusCommand(
            $cfdiReader->emisor['Rfc'],
            $cfdiReader->receptor['Rfc'],
            $cfdiReader->complemento->timbreFiscalDigital['uuid'],
            $cfdiReader['total']
        );
    }

    protected function createCancelSignatureCommandFromCapsule(Capsule $capsule): CancelSignatureCommand
    {
        $credentials = new Credentials(
            $this->filePath('certs/TCM970625MB1.cer'),
            $this->filePath('certs/TCM970625MB1.key.pem'),
            trim($this->fileContentPath('certs/TCM970625MB1.password.bin'))
        );
        $xmlCancelacion = (new CapsuleSigner())->sign($capsule, $credentials);
        return new CancelSignatureCommand($xmlCancelacion);
    }

    protected function checkCanGetSatStatusOrFail(
        string $cfdiContents,
        string $exceptionMessage = ''
    ): GetSatStatusResult {
        $service = new GetSatStatusService($this->createSettingsFromEnvironment());
        $command = $this->createGetSatStatusCommandFromCfdiContents($cfdiContents);
        $result = $service->queryUntilFoundOrTime($command);
        if ('No Encontrado' === $result->cfdi()) {
            if ('' === $exceptionMessage) {
                $exceptionMessage = sprintf('Cannot found UUID %s at SAT using GetSatStatusService', $command->uuid());
            }
            throw new RuntimeException($exceptionMessage);
        }
        return $result;
    }
}
