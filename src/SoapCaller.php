<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use RuntimeException;
use SoapClient;
use stdClass;
use Throwable;

class SoapCaller
{
    /** @var array */
    private $traces = [];

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

    public function call(string $methodName, array $parameters): stdClass
    {
        $parameters = array_merge($parameters, $this->extraParameters());
        $soap = $this->soapClient();
        try {
            return $soap->__soapCall($methodName, [$parameters], []);
        } catch (Throwable $exception) {
            throw new RuntimeException(
                sprintf('Soap call to %s fail: %s', $methodName, $exception->getMessage()),
                0,
                $exception
            );
        } finally {
            $lastTrace = [
                '$methodName' => $methodName,
                '$parameters' => $parameters,
                'Request.Headers' => @$soap->__getLastRequestHeaders(),
                'Request.Body' => @$soap->__getLastRequest(),
                'Response.Headers' => @$soap->__getLastResponseHeaders(),
                'Response.Body' => @$soap->__getLastResponse(),
            ];
            $this->traces[] = $lastTrace;
        }
    }
}
