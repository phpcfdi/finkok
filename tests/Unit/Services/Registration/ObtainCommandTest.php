<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Exceptions\InvalidArgumentException;
use PhpCfdi\Finkok\Services\Registration\ObtainCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class ObtainCommandTest extends TestCase
{
    public function testObtainCommandCreation(): void
    {
        $command = new ObtainCommand('x-rfc');
        $this->assertSame('x-rfc', $command->rfc());
    }

    public function testObtainCommandCreationWithEmptyRfc(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid RFC, cannot be empty');
        new ObtainCommand('');
    }
}
