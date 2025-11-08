<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

final class FileLogger extends AbstractLogger implements LoggerInterface
{
    public function __construct(public string $outputFile = 'php://stdout')
    {
    }

    /**
     * @inheritDoc
     * @param mixed[] $context
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        file_put_contents($this->outputFile, $message . PHP_EOL, FILE_APPEND);
    }
}
