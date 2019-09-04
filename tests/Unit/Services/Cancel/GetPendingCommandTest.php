<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetPendingCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class GetPendingCommandTest extends TestCase
{
    public function testCommandValues(): void
    {
        $command = new GetPendingCommand('x-rfc');
        $this->assertSame('x-rfc', $command->rfc());
    }
}
