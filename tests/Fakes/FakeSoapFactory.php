<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Fakes;

use PhpCfdi\Finkok\SoapCaller;
use PhpCfdi\Finkok\SoapFactory;
use SoapClient;
use stdClass;

class FakeSoapFactory extends SoapFactory
{
    /** @var FakeSoapCaller */
    public $latestSoapCaller;

    /** @var string */
    public $latestWsdlLocation;

    /** @var stdClass */
    public $preparedResult;

    public function createSoapClient(string $wsdlLocation): SoapClient
    {
        return new SoapClient(null, [
            'location' => '',
            'uri' => 'http://tempuri.org',
        ]);
    }

    public function createSoapCaller(string $wsdlLocation, array $defaultOptions): SoapCaller
    {
        $soapCaller = new FakeSoapCaller($this->createSoapClient($wsdlLocation), $defaultOptions);
        $soapCaller->preparedResult = $this->preparedResult;
        $this->latestSoapCaller = $soapCaller;
        $this->latestWsdlLocation = $wsdlLocation;
        return $soapCaller;
    }
}
