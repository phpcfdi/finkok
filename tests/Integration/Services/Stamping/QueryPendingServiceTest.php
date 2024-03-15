<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QueryPendingCommand;
use PhpCfdi\Finkok\Services\Stamping\QueryPendingService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

final class QueryPendingServiceTest extends IntegrationTestCase
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
        $maxAttempts = 10;
        $attempts = 0;
        $quickStamp = $this->currentCfdi();
        $uuidToQueryPending = $quickStamp->uuid();

        $command = new QueryPendingCommand($uuidToQueryPending);
        $service = $this->createService();
        do {
            $attempts = $attempts + 1;
            $result = $service->queryPending($command);
            // exit when get a non-empty status
            if ('' !== $result->status()) {
                break;
            }
            // fail when didn't get a status and reached max attempts
            if ($attempts === $maxAttempts) {
                $this->fail(sprintf('Finkok did not respond with a status after %d attempts', $attempts));
            }
            // wait before next execution
            sleep(1);
        } while (true);

        $this->assertTrue(in_array($result->status(), ['S', 'F'], true), 'Finkok result is not S or F');
        $this->assertSame($uuidToQueryPending, $result->uuid(), 'Finkok response does not include the requested uuid');
        $this->assertSame(1, $attempts, 'El resultado no se obtuvo al primer intento');
    }
}
