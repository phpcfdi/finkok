<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QueryPendingCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class QueryPendingCommandTest extends TestCase
{
    public function testCreateCommand(): void
    {
        $uuid = '11111111-2222-3333-4444-000000000001';
        $command = new QueryPendingCommand($uuid);
        $this->assertSame($uuid, $command->uuid());
    }
}
