<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use SoapClient;
use stdClass;
use Throwable;

class SoapCaller implements LoggerAwareInterface
{
    /** @var SoapClient */
    private $soapClient;

    /** @var array */
    private $extraParameters;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(SoapClient $soapClient, array $extraParameters = [])
    {
        $this->soapClient = $soapClient;
        $this->extraParameters = $extraParameters;
        $this->logger = new NullLogger();
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
        $finalParameters = $this->finalParameters($parameters);
        $soap = $this->soapClient();
        try {
            $result = $soap->__soapCall($methodName, [$finalParameters]);
            $this->logger->debug(strval(json_encode([
                $methodName => $this->extractSoapClientTrace($soap),
            ], JSON_PRETTY_PRINT)));
            return $result;
        } catch (Throwable $exception) {
            $this->logger->error(strval(json_encode(
                ['method' => $methodName, 'parameters' => $finalParameters] + $this->extractSoapClientTrace($soap),
                JSON_PRETTY_PRINT
            )));
            throw new RuntimeException(sprintf('Fail soap call to %s', $methodName), 0, $exception);
        }
    }

    protected function extractSoapClientTrace(SoapClient $soapClient): array
    {
        return [
            'request.headers' => @$soapClient->__getLastRequestHeaders(),
            'request.body' => @$soapClient->__getLastRequest(),
            'response.headers' => @$soapClient->__getLastResponseHeaders(),
            'response.body' => @$soapClient->__getLastResponse(),
        ];
    }

    public function finalParameters(array $parameters): array
    {
        return array_merge($parameters, $this->extraParameters());
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
