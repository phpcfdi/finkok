<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

class DatetimeCommand
{
    private string $postalCode;

    public function __construct(string $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function postalCode(): string
    {
        return $this->postalCode;
    }
}
