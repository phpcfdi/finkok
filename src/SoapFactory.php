<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use SoapClient;

class SoapFactory
{
    public function createSoapClient(string $wsdlLocation): SoapClient
    {
        return new SoapClient($wsdlLocation, [
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'cache_wsdl' => WSDL_CACHE_MEMORY,
            'exceptions' => true,
            // 'trace' => true,
        ]);
    }

    public function createSoapCaller(string $wsdlLocation, array $defaultOptions): SoapCaller
    {
        return new SoapCaller($this->createSoapClient($wsdlLocation), $defaultOptions);
    }
}
