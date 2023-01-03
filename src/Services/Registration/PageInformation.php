<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class PageInformation
{
    /** @var int */
    private $firstRecord;

    /** @var int */
    private $lastRecord;

    /** @var int */
    private $totalRecords;

    /** @var int */
    private $currentPage;

    /** @var int */
    private $totalPages;

    /** @var int */
    private $pageLength;

    public function __construct(
        int $firstRecord,
        int $lastRecord,
        int $totalRecords,
        int $currentPage,
        int $totalPages,
        int $pageLength
    ) {
        $this->firstRecord = $firstRecord;
        $this->lastRecord = $lastRecord;
        $this->totalRecords = $totalRecords;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
        $this->pageLength = $pageLength;
    }

    public static function empty(): self
    {
        return new self(1, 50, 0, 1, 1, 50);
    }

    public static function fromMessage(string $message): self
    {
        $found = preg_match('/^Showing (?<first>\d+) to (?<last>\d+) of (?<records>\d+) entries$/', $message, $matches);
        if (1 !== $found) {
            return self::empty();
        }
        $first = intval($matches['first']);
        $last = intval($matches['last']);
        $records = intval($matches['records']);
        return self::fromValues($first, $last, $records);
    }

    public static function fromValues(int $first, int $last, int $records): self
    {
        $pageLength = $last - $first + 1;
        if (0 === $pageLength) {
            return new self($first, $last, $records, 1, 1, $pageLength);
        }
        $pages = intval(ceil($records / $pageLength));
        $page = intval(round($last / $pageLength, 0));

        return new self($first, $last, $records, $page, $pages, $pageLength);
    }

    public function firstRecord(): int
    {
        return $this->firstRecord;
    }

    public function lastRecord(): int
    {
        return $this->lastRecord;
    }

    public function totalRecords(): int
    {
        return $this->totalRecords;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function totalPages(): int
    {
        return $this->totalPages;
    }

    public function pageLength(): int
    {
        return $this->pageLength;
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->totalPages;
    }
}
