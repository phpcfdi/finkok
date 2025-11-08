<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use DateTime;
use DateTimeImmutable;
use LogicException;
use PhpCfdi\Finkok\Services\Utilities\ReportTotalCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class ReportTotalCommandTest extends TestCase
{
    public function testReportTotalCommandPastPeriods(): void
    {
        $command = new ReportTotalCommand('x-rfc', 'I', 2019, 2, 2019, 4); // feb, mar, abr
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame('I', $command->type());
        $this->assertSame('2019-02', $command->startPeriod());
        $this->assertSame('2019-04', $command->endPeriod());
        $this->assertSame('2019-02-01T00:00:00', $command->startString());
        $this->assertSame('2019-05-01T00:00:00', $command->endString());
    }

    public function testReportTotalCommandCurrentMonth(): void
    {
        $today = new DateTime('today');
        $year = intval($today->format('Y'));
        $month = intval($today->format('m'));
        $period = $today->format('Y-m');
        $startString = sprintf('%s-01T00:00:00', $today->format('Y-m'));
        $endString = sprintf('%sT00:00:00', $today->format('Y-m-d'));

        $command = new ReportTotalCommand('x-rfc', 'I', $year, $month, $year, $month);
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame($period, $command->startPeriod());
        $this->assertSame($period, $command->endPeriod());
        $this->assertSame($startString, $command->startString());
        $this->assertSame($endString, $command->endString());
    }

    public function testReportTotalCommandWithoutEndMonth(): void
    {
        $command = new ReportTotalCommand('x-rfc', 'I', 2018, 3, 2018);
        $this->assertSame('2018-03', $command->endPeriod());
    }

    public function testReportTotalCommandWithoutEndYear(): void
    {
        $command = new ReportTotalCommand('x-rfc', 'I', 2018, 3, 0, 4);
        $this->assertSame('2018-04', $command->endPeriod());
    }

    public function testReportTotalCommandWithoutEndYearMonth(): void
    {
        $command = new ReportTotalCommand('x-rfc', 'I', 2018, 3);
        $this->assertSame('2018-03', $command->endPeriod());
    }

    public function testCreateWithStartPeriodGreaterThanEndPeriod(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('greater than');
        new ReportTotalCommand('x-rfc', 'I', 2018, 1, 2017, 12);
    }

    public function testCreateWithStartPeriodInPastAndEndPeriodInCurrent(): void
    {
        $today = new DateTime('today');
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot combine multiple past months with current/future months');
        new ReportTotalCommand('x-rfc', 'I', 2019, 1, intval($today->format('Y')), intval($today->format('m')));
    }

    public function testCreateWithInvalidStartYearLowerBound(): void
    {
        $currentYear = intval(date('Y'));
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Start year 1999 is not between 2000 and $currentYear");
        new ReportTotalCommand('x-rfc', 'I', 1999, 1);
    }

    public function testCreateWithInvalidStartYearUpperBound(): void
    {
        $currentYear = intval(date('Y'));
        $nextYear = $currentYear + 1;
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Start year $nextYear is not between 2000 and $currentYear");
        new ReportTotalCommand('x-rfc', 'I', $nextYear, 1);
    }

    public function testCreateWithInvalidEndYearLowerBound(): void
    {
        $currentYear = intval(date('Y'));
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("End year 1999 is not between 2000 and $currentYear");
        new ReportTotalCommand('x-rfc', 'I', 2000, 1, 1999, 1);
    }

    public function testCreateWithInvalidEndYearUpperBound(): void
    {
        $currentYear = intval(date('Y'));
        $nextYear = $currentYear + 1;
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("End year $nextYear is not between 2000 and $currentYear");
        new ReportTotalCommand('x-rfc', 'I', 2000, 1, $nextYear, 1);
    }

    public function testCreateWithInvalidStartMonthLowerBound(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Start month -1 is not between 1 and 12');
        new ReportTotalCommand('x-rfc', 'I', 2018, -1);
    }

    public function testCreateWithInvalidStartMonthUpperBound(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Start month 13 is not between 1 and 12');
        new ReportTotalCommand('x-rfc', 'I', 2018, 13);
    }

    public function testCreateWithInvalidEndMonthLowerBound(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('End month -1 is not between 1 and 12');
        new ReportTotalCommand('x-rfc', 'I', 2018, 1, 2018, -1);
    }

    public function testCreateWithInvalidEndMonthUpperBound(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('End month 13 is not between 1 and 12');
        new ReportTotalCommand('x-rfc', 'I', 2018, 1, 2018, 13);
    }

    public function testCreateWithStartPeriodInPastAndEndPeriodInFuture(): void
    {
        $today = new DateTimeImmutable('2019-12-05T12:13:14');
        $year = intval($today->format('Y'));
        $month = intval($today->format('m'));
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot combine multiple past months with current/future months');
        new class ($today, 'x-rfc', 'I', 2019, 1, $year, $month) extends ReportTotalCommand {
            private DateTimeImmutable $today;

            public function __construct(
                DateTimeImmutable $today,
                string $rfc,
                string $type,
                int $startYear,
                int $startMonth,
                int $endYear = 0,
                int $endMonth = 0
            ) {
                $this->today = $today;
                parent::__construct($rfc, $type, $startYear, $startMonth, $endYear, $endMonth);
            }

            /** @noinspection PhpMissingParentCallCommonInspection */
            protected function today(): DateTimeImmutable
            {
                return $this->today;
            }
        };
    }
}
