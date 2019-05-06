<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use DateTimeImmutable;
use LogicException;
use PhpCfdi\Finkok\Services\Utilities\ReportUuidCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class ReportUuidCommandTest extends TestCase
{
    public function testReportUuidCommandCreation(): void
    {
        $since = new DateTimeImmutable('yesterday');
        $until = new DateTimeImmutable('now');
        $command = new ReportUuidCommand('x-rfc', 'I', $since, $until);
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame('I', $command->type());
        $this->assertSame($since, $command->since());
        $this->assertSame($until, $command->until());
        $this->assertSame($since->format('Y-m-d\TH:i:s'), $command->sinceString());
        $this->assertSame($until->format('Y-m-d\TH:i:s'), $command->untilString());
    }

    public function testCreateUsingSinceDateTimeGreaterThanUntilDateTime(): void
    {
        $since = new DateTimeImmutable('now');
        $until = $since->modify('-1 second');
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Since date is greater than until date');
        new ReportUuidCommand('x-rfc', 'I', $since, $until);
    }
}
