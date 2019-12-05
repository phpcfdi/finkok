<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use DateTimeImmutable;
use LogicException;

class ReportTotalCommand
{
    /** @var string */
    private $rfc;

    /** @var string */
    private $type;

    /** @var int */
    private $startYear;

    /** @var int */
    private $startMonth;

    /** @var int */
    private $endYear;

    /** @var int */
    private $endMonth;

    /** @var string */
    private $startPeriod;

    /** @var string */
    private $endPeriod;

    public function __construct(
        string $rfc,
        string $type,
        int $startYear,
        int $startMonth,
        int $endYear = 0,
        int $endMonth = 0
    ) {
        $endYear = $endYear ?: $startYear;
        $endMonth = $endMonth ?: $startMonth;
        $today = $this->today();
        $currentYear = intval($today->format('Y'));
        $currentPeriod = sprintf('%04d-%02d', $currentYear, intval($today->format('m')));

        $this->rfc = $rfc;
        $this->type = $type;
        $this->startYear = $startYear;
        $this->startMonth = $startMonth;
        $this->endYear = $endYear;
        $this->endMonth = $endMonth;
        $this->startPeriod = sprintf('%04d-%02d', $this->startYear, $this->startMonth);
        $this->endPeriod = sprintf('%04d-%02d', $this->endYear, $this->endMonth);

        if ($startYear < 2000 || $startYear > $currentYear) {
            throw new LogicException(sprintf('Start year %d is not between 2000 and %d', $startYear, $currentYear));
        }
        if ($endYear < 2000 || $endYear > $currentYear) {
            throw new LogicException(sprintf('End year %d is not between 2000 and %d', $endYear, $currentYear));
        }
        if ($startMonth < 1 || $startMonth > 12) {
            throw new LogicException(sprintf('Start month %d is not between 1 and 12', $startMonth));
        }
        if ($endMonth < 1 || $endMonth > 12) {
            throw new LogicException(sprintf('End month %d is not between 1 and 12', $endMonth));
        }

        if ($this->startPeriod > $this->endPeriod) {
            throw new LogicException(
                sprintf('Start period %s cannot be greater than end period %s', $this->startPeriod, $this->endPeriod)
            );
        }

        if ($this->startPeriod < $this->endPeriod && $this->endPeriod >= $currentPeriod) {
            throw new LogicException(sprintf('Cannot combine multiple past months with current/future months'));
        }
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function startPeriod(): string
    {
        return $this->startPeriod;
    }

    public function endPeriod(): string
    {
        return $this->endPeriod;
    }

    public function startString(): string
    {
        return sprintf('%04d-%02d-01T00:00:00', $this->startYear, $this->startMonth);
    }

    public function endString(): string
    {
        $date = new DateTimeImmutable(sprintf('%04d-%02d-01', $this->endYear, $this->endMonth));
        $today = $this->today();
        if ($this->endPeriod() === $today->format('Y-m')) {
            $date = $today;
        } else {
            $date = $date->modify('+ 1 month');
        }
        return sprintf('%sT00:00:00', $date->format('Y-m-d'));
    }

    protected function today(): DateTimeImmutable
    {
        return new DateTimeImmutable('today');
    }
}
