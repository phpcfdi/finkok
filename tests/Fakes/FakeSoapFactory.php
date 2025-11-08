<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Fakes;

use PhpCfdi\Finkok\SoapCaller;
use PhpCfdi\Finkok\SoapFactory;
use Psr\Log\LoggerInterface;
use SoapClient;
use stdClass;

final class FakeSoapFactory extends SoapFactory
{
    public FakeSoapCaller $latestSoapCaller;

    public string $latestWsdlLocation;

    public stdClass $preparedResult;

    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->preparedResult = (object) [];
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function createSoapClient(string $wsdlLocation): SoapClient
    {
        return new SoapClient(null, [
            'location' => '',
            'uri' => 'http://tempuri.org',
        ]);
    }

    /**
     * @inheritdoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function createSoapCaller(string $wsdlLocation, array $defaultOptions): SoapCaller
    {
        $soapCaller = new FakeSoapCaller($this->createSoapClient($wsdlLocation), $defaultOptions);
        $soapCaller->preparedResult = $this->preparedResult;
        $this->latestSoapCaller = $soapCaller;
        $this->latestWsdlLocation = $wsdlLocation;
        return $soapCaller;
    }
}
