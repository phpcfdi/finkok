<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\AssignCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class AssignCommandTest extends TestCase
{
    public function testAssignCommandCreation(): void
    {
        $command = new AssignCommand('x-rfc', 1);
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame(1, $command->credit());
    }
}
