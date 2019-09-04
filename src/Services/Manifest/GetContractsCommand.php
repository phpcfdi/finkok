<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

class GetContractsCommand
{
    /** @var string */
    private $rfc;

    /** @var string */
    private $name;

    /** @var string */
    private $address;

    /** @var string */
    private $email;

    public function __construct(string $rfc, string $name, string $address, string $email)
    {
        $this->rfc = $rfc;
        $this->name = $name;
        $this->address = $address;
        $this->email = $email;
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
}
