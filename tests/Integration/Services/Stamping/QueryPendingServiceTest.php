<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QueryPendingCommand;
use PhpCfdi\Finkok\Services\Stamping\QueryPendingService;
use PhpCfdi\Finkok\Services\Stamping\QuickStampService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\SoapFactory;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class QueryPendingServiceTest extends TestCase
{
    public function testQueryPendingWithInvalidUuid(): void
    {
        $command = new QueryPendingCommand('foo');
        $settings = $this->createSettingsFromEnvironment();
        $service = new QueryPendingService($settings);

        $result = $service->queryPending($command);

        // FINKOK tiene faltas de ortografía
        $this->assertSame('UUID con formato invalido', $result->error());
    }

    public function testQueryPendingWithFakeUuid(): void
    {
        $command = new QueryPendingCommand('01234567-0123-0123-0123-012345678901');

        $logger = new class() extends AbstractLogger implements LoggerInterface {
            public function log($level, $message, array $context = []): void
            {
                echo PHP_EOL, $level, PHP_EOL, print_r(json_decode($message), true);
            }
        };
        $settings = $this->createSettingsFromEnvironment();
        $settings->changeSoapFactory(new SoapFactory($logger));

        $service = new QueryPendingService($settings);

        $result = $service->queryPending($command);

        $this->assertSame('????', $result->error());
    }

    public function testQueryPendingWithCreatedCfdiUsingQuickStamp(): void
    {
        $settings = $this->createSettingsFromEnvironment();
        $quickStamp = (new QuickStampService($settings))
            ->quickstamp(
                new StampingCommand((new RandomPreCfdi())->createValid())
            );
        $this->assertNotEmpty($quickStamp->uuid());

        $command = new QueryPendingCommand($quickStamp->uuid());
        $service = new QueryPendingService($settings);
        $result = $service->queryPending($command);

        $this->assertTrue(in_array($result->status(), ['N', 'F'], true), 'Finkok result is not N or F');
        $this->assertSame($result->uuid(), $command->uuid(), 'Finkok response was not for the same uuid requested');
    }
}
