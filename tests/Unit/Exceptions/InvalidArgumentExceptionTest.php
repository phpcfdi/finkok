<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Exceptions;

use PhpCfdi\Finkok\Exceptions\FinkokException;
use PhpCfdi\Finkok\Exceptions\InvalidArgumentException;
use PhpCfdi\Finkok\Tests\TestCase;

final class InvalidArgumentExceptionTest extends TestCase
{
    public function testIsInstaceOfFinkokException(): void
    {
        $exception = new InvalidArgumentException();
        $this->assertInstanceOf(FinkokException::class, $exception);
    }

    public function testIsInstaceOfInvalidArgumentException(): void
    {
        $exception = new InvalidArgumentException();
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }
}
