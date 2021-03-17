<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\DatetimeCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class DatetimeCommandTest extends TestCase
{
    public function testDatetimeCommandWithValidPostalCode(): void
    {
        $stamping = new DatetimeCommand('86000');
        $this->assertSame('86000', $stamping->postalCode());
    }

    public function testDatetimeCommandWithEmptyPostalCode(): void
    {
        $stamping = new DatetimeCommand('');
        $this->assertSame('', $stamping->postalCode());
    }
}
