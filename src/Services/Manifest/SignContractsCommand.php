<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

class SignContractsCommand
{
    public function __construct(private string $snid, private string $privacy, private string $contract)
    {
    }

    public function snid(): string
    {
        return $this->snid;
    }

    public function privacy(): string
    {
        return $this->privacy;
    }

    public function contract(): string
    {
        return $this->contract;
    }
}
