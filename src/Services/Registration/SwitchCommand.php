<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class SwitchCommand
{
    public function __construct(private string $rfc, private CustomerType $customerType)
    {
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function customerType(): CustomerType
    {
        return $this->customerType;
    }
}
