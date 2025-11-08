<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class AssignCommand
{
    public function __construct(private string $rfc, private int $credit)
    {
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function credit(): int
    {
        return $this->credit;
    }
}
