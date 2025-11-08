<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class SwitchCommand
{
    private string $rfc;

    private CustomerType $customerType;

    public function __construct(string $rfc, CustomerType $customerType)
    {
        $this->rfc = $rfc;
        $this->customerType = $customerType;
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
