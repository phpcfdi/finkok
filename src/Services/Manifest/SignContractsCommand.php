<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

class SignContractsCommand
{
    /** @var string */
    private $snid;

    /** @var string */
    private $privacy;

    /** @var string */
    private $contract;

    public function __construct(string $snid, string $privacy, string $contract)
    {
        $this->snid = $snid;
        $this->privacy = $privacy;
        $this->contract = $contract;
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
