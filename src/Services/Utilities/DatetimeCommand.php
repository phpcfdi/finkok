<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

class DatetimeCommand
{
    public function __construct(private string $postalCode)
    {
    }

    public function postalCode(): string
    {
        return $this->postalCode;
    }
}
