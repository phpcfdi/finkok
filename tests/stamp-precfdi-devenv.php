<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests;

// report all errors
use Exception;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\QuickFinkok;
use Throwable;

require_once __DIR__ . '/bootstrap.php';

exit(call_user_func(new class ($argv[1] ?? '') {
    /** @var string */
    private $command;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function __invoke(string $preCfdiPath): int
    {
        if (in_array($preCfdiPath, ['-h', '--help', 'help'])) {
            $this->showHelp();
            return 0;
        }
        $debug = boolval(getenv('FINKOK_LOG_CALLS'));
        try {
            if (! file_exists($preCfdiPath)) {
                throw new Exception("File $preCfdiPath does not exists");
            }
            $preCfdiContents = file_get_contents($preCfdiPath) ?: '';
            if ('' === $preCfdiContents) {
                throw new Exception("File $preCfdiPath is empty");
            }

            $settings = new FinkokSettings(
                strval(getenv('FINKOK_USERNAME')) ?: 'username-non-set',
                strval(getenv('FINKOK_PASSWORD')) ?: 'password-non-set',
                FinkokEnvironment::makeDevelopment()
            );
            if ($debug) {
                $settings->soapFactory()->setLogger(new LoggerPrinter());
            }
            $quickFinkok = new QuickFinkok($settings);

            $stamp = $quickFinkok->stamp($preCfdiContents);

            echo 'WS-Response: ',
                json_encode($stamp->rawData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), PHP_EOL;

            if ('' === $stamp->uuid()) {
                throw new Exception("Stamp on $preCfdiPath did not return an UUID");
            }
            echo 'CFDI: ', PHP_EOL, $stamp->xml(), PHP_EOL;
            echo 'UUID: ', $stamp->uuid(), PHP_EOL;

            return 0;
        } catch (Throwable $exception) {
            file_put_contents(
                'php://stderr',
                sprintf("%s: %s\n", get_class($exception), $exception->getMessage()),
                FILE_APPEND
            );
            return (int) $exception->getCode() ?: 1;
        }
    }

    public function showHelp(): void
    {
        $commandName = basename($this->command);
        echo "{$commandName}: Try to stamp a precfdi file in development environment", PHP_EOL,
            "Syntax: $commandName precfdi-path", PHP_EOL,
            '  precfdi-path: Precfdi Location', PHP_EOL,
            'Environment (See .env file):', PHP_EOL,
            '  FINKOK_USERNAME: Finkok username', PHP_EOL,
            '  FINKOK_PASSWORD: Finkok password', PHP_EOL,
            '  FINKOK_LOG_CALLS: Print on stdout the request/response dump and raw data response', PHP_EOL,
            'Author: Carlos C Soto <eclipxe13@gmail.com>', PHP_EOL,
            PHP_EOL;
    }
}, $argv[1] ?? ''));
