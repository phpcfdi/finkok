<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use BadMethodCallException;
use InvalidArgumentException;
use PhpCfdi\Finkok\Services\Cancel;
use PhpCfdi\Finkok\Services\Manifest;
use PhpCfdi\Finkok\Services\Registration;
use PhpCfdi\Finkok\Services\Stamping;
use PhpCfdi\Finkok\Services\Utilities;

/**
 * Helper class to invoke execute Finkok commands and get the result
 *
 * @method Stamping\StampingResult stamp(Stamping\StampingCommand $command)
 * @method Stamping\StampingResult quickstamp(Stamping\StampingCommand $command)
 * @method Stamping\StampingResult stamped(Stamping\StampingCommand $command)
 * @method Stamping\QueryPendingResult stampQueryPending(Stamping\QueryPendingCommand $command)
 * @method Cancel\CancelSignatureResult cancelSignature(Cancel\CancelSignatureCommand $command)
 * @method Cancel\GetPendingResult getPendingToCancel(Cancel\GetPendingCommand $command)
 * @method Cancel\GetReceiptResult getCancelReceipt(Cancel\GetReceiptResult $command)
 * @method Cancel\GetSatStatusResult getSatStatus(Cancel\GetSatStatusCommand $command)
 * @method Cancel\GetRelatedSignatureResult getRelatedSignature(Cancel\GetRelatedSignatureCommand $command)
 * @method Cancel\AcceptRejectSignatureResult acceptRejectSignature(Cancel\AcceptRejectSignatureCommand $command)
 * @method Utilities\DatetimeResult datetime()
 * @method Utilities\DatetimeResult datetimePostalCode(Utilities\DatetimeCommand $command)
 * @method Utilities\DownloadXmlResult downloadXml(Utilities\DownloadXmlCommand $command)
 * @method Utilities\ReportCreditResult reportCredit(Utilities\ReportCreditCommand $command)
 * @method Utilities\ReportTotalResult reportTotal(Utilities\ReportTotalCommand $command)
 * @method Utilities\ReportUuidResult reportUuid(Utilities\ReportUuidCommand $command)
 * @method Manifest\GetContractsResult getContracts(Manifest\GetContractsCommand $command)
 * @method Manifest\SignContractsResult signContracts(Manifest\SignContractsCommand $command)
 * @method Manifest\GetSignedContractsResult getSignedContracts(Manifest\GetSignedContractsCommand $command)
 * @method Registration\AddResult registrationAdd(Registration\AddCommand $command)
 * @method Registration\AssignResult registrationAssign(Registration\AssignCommand $command)
 * @method Registration\SwitchResult registrationSwitch(Registration\SwitchCommand $command)
 * @method Registration\EditResult registrationEdit(Registration\EditCommand $command)
 * @method Registration\ObtainResult registrationObtain(Registration\ObtainCommand $command)
 */
class Finkok
{
    /** @var array<array> */
    protected const SERVICES_MAP = [
        'stamp' => [Stamping\StampService::class, Stamping\StampingCommand::class],
        'quickstamp' => [Stamping\QuickStampService::class, Stamping\StampingCommand::class],
        'stamped' => [Stamping\StampedService::class, Stamping\StampingCommand::class],
        'stampQueryPending' => [
            Stamping\QueryPendingService::class,
            Stamping\QueryPendingCommand::class,
            'queryPending', // override method name on service
        ],
        'cancelSignature' => [Cancel\CancelSignatureService::class, Cancel\CancelSignatureCommand::class],
        'getPendingToCancel' => [Cancel\GetPendingService::class, Cancel\GetPendingCommand::class, 'obtainPending'],
        'getCancelReceipt' => [Cancel\GetReceiptService::class, Cancel\GetReceiptResult::class, 'download'],
        'getSatStatus' => [Cancel\GetSatStatusService::class, Cancel\GetSatStatusCommand::class, 'query'],
        'getRelatedSignature' => [
            Cancel\GetRelatedSignatureService::class,
            Cancel\GetRelatedSignatureCommand::class,
        ],
        'acceptRejectSignature' => [
            Cancel\AcceptRejectSignatureService::class,
            Cancel\AcceptRejectSignatureCommand::class,
        ],
        'datetime' => [Utilities\DatetimeService::class, ''],
        'datetimePostalCode' => [Utilities\DatetimeService::class, Utilities\DatetimeCommand::class, 'datetime'],
        'downloadXml' => [Utilities\DownloadXmlService::class, Utilities\DownloadXmlCommand::class],
        'reportCredit' => [Utilities\ReportCreditService::class, Utilities\ReportCreditCommand::class],
        'reportTotal' => [Utilities\ReportTotalService::class, Utilities\ReportTotalCommand::class],
        'reportUuid' => [Utilities\ReportUuidService::class, Utilities\ReportUuidCommand::class],
        'getContracts' => [Manifest\GetContractsService::class, Manifest\GetContractsCommand::class, 'obtainContracts'],
        'signContracts' => [
            Manifest\SignContractsService::class,
            Manifest\SignContractsCommand::class,
            'sendSignedContracts',
        ],
        'getSignedContracts' => [
            Manifest\GetSignedContractsService::class,
            Manifest\GetSignedContractsCommand::class,
        ],
        'registrationAdd' => [Registration\AddService::class, Registration\AddCommand::class, 'add'],
        'registrationAssign' => [Registration\AssignService::class, Registration\AssignCommand::class, 'assign'],
        'registrationSwitch' => [Registration\SwitchService::class, Registration\SwitchCommand::class, 'switch'],
        'registrationEdit' => [Registration\EditService::class, Registration\EditCommand::class, 'edit'],
        'registrationObtain' => [Registration\ObtainService::class, Registration\ObtainCommand::class, 'obtain'],
    ];

    /** @var FinkokSettings */
    private $settings;

    public function __construct(FinkokSettings $factory)
    {
        $this->settings = $factory;
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    /**
     * @param string $name
     * @param array<mixed> $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (array_key_exists($name, static::SERVICES_MAP)) {
            $command = $this->checkCommand($name, $arguments[0] ?? null);
            $service = $this->createService($name);
            $result = $this->executeService($name, $service, $command);
            return $result;
        }
        throw new BadMethodCallException(sprintf('Helper %s is not registered', $name));
    }

    /**
     * @param string $method
     * @param mixed $command
     * @return object|null
     */
    protected function checkCommand(string $method, $command)
    {
        $expected = static::SERVICES_MAP[$method][1];
        if ('' === $expected) {
            return null;
        }
        if (! is_a($command, $expected)) {
            $type = (is_object($command)) ? get_class($command) : gettype($command);
            throw new InvalidArgumentException(
                sprintf('Call %s::%s expect %s but received %s', static::class, $method, $expected, $type)
            );
        }
        return $command;
    }

    /**
     * @param string $method
     * @return object
     */
    protected function createService(string $method)
    {
        $serviceClass = static::SERVICES_MAP[$method][0];
        $service = new $serviceClass($this->settings);
        return $service;
    }

    /**
     * @param string $method
     * @param object $service
     * @param object|null $command
     * @return mixed
     */
    protected function executeService(string $method, object $service, ?object $command)
    {
        $method = static::SERVICES_MAP[$method][2] ?? $method;
        if (! is_callable([$service, $method])) {
            throw new BadMethodCallException(
                sprintf('The service %s does not have a method %s', get_class($service), $method)
            );
        }
        return $service->{$method}($command);
    }
}
