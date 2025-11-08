<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class AssignCommand
{
    private string $rfc;

    private int $credit;

    public function __construct(string $rfc, int $credit)
    {
        $this->rfc = $rfc;
        $this->credit = $credit;
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
