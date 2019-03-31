<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use SoapClient;

class SoapFactory
{
    public function createSoapClient(string $endpoint): SoapClient
    {
        return new SoapClient($endpoint . '?singleWsdl', [
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'cache_wsdl' => WSDL_CACHE_MEMORY,
            'exceptions' => true,
            'trace' => true,
        ]);
    }

    public function createSoapCaller(SoapClient $soapClient, array $defaultOptions): SoapCaller
    {
        return new SoapCaller($soapClient, $defaultOptions);
    }
}
