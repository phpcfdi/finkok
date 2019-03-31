<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use RuntimeException;
use SoapClient;
use stdClass;
use Throwable;

class SoapCaller
{
    /** @var SoapClient */
    private $soapClient;

    /** @var array */
    private $extraParameters;

    public function __construct(SoapClient $soapClient, array $extraParameters = [])
    {
        $this->soapClient = $soapClient;
        $this->extraParameters = $extraParameters;
    }

    private function soapClient(): SoapClient
    {
        return $this->soapClient;
    }

    public function extraParameters(): array
    {
        return $this->extraParameters;
    }

    public function call(string $methodName, array $finalParameters): stdClass
    {
        $finalParameters = $this->finalParameters($finalParameters);
        $soap = $this->soapClient();
        try {
            return $soap->__soapCall($methodName, $finalParameters);
        } catch (Throwable $exception) {
            throw new RuntimeException(sprintf('Fail soap call to %s', $methodName), 0, $exception);
        }
    }

    public function finalParameters(array $parameters): array
    {
        return [array_merge($parameters, $this->extraParameters())];
    }
}
