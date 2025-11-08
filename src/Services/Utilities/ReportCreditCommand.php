<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

class ReportCreditCommand
{
    public function __construct(private string $rfc)
    {
    }

    public function rfc(): string
    {
        return $this->rfc;
    }
}
