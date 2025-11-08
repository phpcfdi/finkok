<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

class GetContractsCommand
{
    private string $rfc;

    private string $name;

    private string $address;

    private string $email;

    private string $snid;

    public function __construct(string $rfc, string $name, string $address, string $email, string $snid)
    {
        $this->rfc = $rfc;
        $this->name = $name;
        $this->address = $address;
        $this->email = $email;
        $this->snid = $snid;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function address(): string
    {
        return $this->address;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function snid(): string
    {
        return $this->snid;
    }
}
