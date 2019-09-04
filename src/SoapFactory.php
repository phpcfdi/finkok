<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SoapClient;

class SoapFactory implements LoggerAwareInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function createSoapClient(string $wsdlLocation): SoapClient
    {
        return new SoapClient($wsdlLocation, [
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'cache_wsdl' => WSDL_CACHE_MEMORY,
            'exceptions' => true,
            'trace' => true,
        ]);
    }

    public function createSoapCaller(string $wsdlLocation, array $defaultOptions): SoapCaller
    {
        $caller = new SoapCaller($this->createSoapClient($wsdlLocation), $defaultOptions);
        $caller->setLogger($this->logger);
        return $caller;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
