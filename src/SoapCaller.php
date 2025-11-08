<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use JsonSerializable;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use SoapClient;
use stdClass;
use Throwable;

class SoapCaller implements LoggerAwareInterface
{
    private SoapClient $soapClient;

    /** @var array<mixed> */
    private array $extraParameters;

    private LoggerInterface $logger;

    /**
     * @param SoapClient $soapClient
     * @param array<mixed> $extraParameters
     */
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

    /** @return array<mixed> */
    public function extraParameters(): array
    {
        return $this->extraParameters;
    }

    /**
     * @param string $methodName
     * @param array<mixed> $parameters
     * @return stdClass
     */
    public function call(string $methodName, array $parameters): stdClass
    {
        $finalParameters = $this->finalParameters($parameters);
        $soap = $this->soapClient();
        try {
            $result = $soap->__soapCall($methodName, [$finalParameters]);
            $this->logger->debug(strval(json_encode([
                $methodName => $this->extractSoapClientTrace($soap),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));
            /** @var stdClass $result */
            return $result;
        } catch (Throwable $exception) {
            $this->logger->error(strval(json_encode(
                array_merge(
                    ['method' => $methodName, 'parameters' => $finalParameters],
                    $this->extractSoapClientTrace($soap),
                    ['exception' => ($exception instanceof JsonSerializable) ? $exception : print_r($exception, true)]
                ),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )));
            throw new RuntimeException(sprintf('Fail soap call to %s', $methodName), 0, $exception);
        }
    }

    /**
     * @param SoapClient $soapClient
     * @return array<string, array<string, string>>
     * @noinspection PhpUsageOfSilenceOperatorInspection
     */
    protected function extractSoapClientTrace(SoapClient $soapClient): array
    {
        return [
            'request' => [
                'headers' => (string) @$soapClient->__getLastRequestHeaders(),
                'body' => (string) @$soapClient->__getLastRequest(),
            ],
            'response' => [
                'headers' => (string) @$soapClient->__getLastResponseHeaders(),
                'body' => (string) @$soapClient->__getLastResponse(),
            ],
        ];
    }

    /**
     * @param array<mixed> $parameters
     * @return array<mixed>
     */
    public function finalParameters(array $parameters): array
    {
        return array_merge($parameters, $this->extraParameters());
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
