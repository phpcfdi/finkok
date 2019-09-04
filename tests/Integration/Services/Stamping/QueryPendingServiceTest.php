<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QueryPendingCommand;
use PhpCfdi\Finkok\Services\Stamping\QueryPendingService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

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
        $command = new QueryPendingCommand('01234567-0123-0123-0123-012345678901');
        $service = $this->createService();

        $result = $service->queryPending($command);

        $this->assertSame('UUID 01234567-0123-0123-0123-012345678901 No Encontrado', $result->error());
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
