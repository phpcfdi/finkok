<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QueryPendingCommand;
use PhpCfdi\Finkok\Services\Stamping\QueryPendingService;
use PhpCfdi\Finkok\SoapFactory;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class QueryPendingServiceTest extends IntegrationTestCase
{
    protected function createService(): QueryPendingService
    {
        $settings = $this->createSettingsFromEnvironment();
        return new QueryPendingService($settings);
    }

    public function testQueryPendingWithInvalidUuid(): void
    {
        $command = new QueryPendingCommand('foo');
        $service = $this->createService();

        $result = $service->queryPending($command);

        // FINKOK tiene faltas de ortografía
        $this->assertSame('UUID con formato invalido', $result->error());
    }

    public function testQueryPendingWithFakeUuid(): void
    {
        $this->markTestSkipped('Se sabe que finkok tiene un error en este caso, ticket #17626');

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
        // $service = $this->createService();

        $result = $service->queryPending($command);

        $this->assertSame('????', $result->error());
    }

    public function testQueryPendingWithCreatedCfdiUsingQuickStamp(): void
    {
        $quickStamp = $this->currentCfdi();

        $command = new QueryPendingCommand($quickStamp->uuid());
        $service = $this->createService();
        $result = $service->queryPending($command);

        $this->assertTrue(in_array($result->status(), ['S', 'F'], true), 'Finkok result is not S or F');
        $this->assertSame($result->uuid(), $command->uuid(), 'Finkok response does not include the requested uuid');
    }
}
