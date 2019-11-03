<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

class DatetimeCommand
{
    /** @var string */
    private $postalCode;

    public function __construct(string $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function postalCode(): string
    {
        return $this->postalCode;
    }
}
