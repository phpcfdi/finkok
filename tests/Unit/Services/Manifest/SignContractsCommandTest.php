<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\SignContractsCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class SignContractsCommandTest extends TestCase
{
    public function testCreateCommand(): void
    {
        $command = new SignContractsCommand('x-snid', 'x-privacy', 'x-contract');
        $this->assertSame('x-snid', $command->snid());
        $this->assertSame('x-privacy', $command->privacy());
        $this->assertSame('x-contract', $command->contract());
    }
}
