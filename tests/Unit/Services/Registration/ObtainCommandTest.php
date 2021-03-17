<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class ObtainCommandTest extends TestCase
{
    public function testObtainCommandCreation(): void
    {
        $command = new ObtainCommand('x-rfc');
        $this->assertSame('x-rfc', $command->rfc());
    }

    public function testObtainCommandCreationWithoutRfc(): void
    {
        $command = new ObtainCommand();
        $this->assertSame('', $command->rfc());
    }
}
