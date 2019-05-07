<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\GetContractsCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class GetContractsCommandTest extends TestCase
{
    public function testCreateCommand(): void
    {
        $command = new GetContractsCommand('x-rfc', 'x-name', 'x-address', 'x-email');
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame('x-name', $command->name());
        $this->assertSame('x-address', $command->address());
        $this->assertSame('x-email', $command->email());
    }
}
