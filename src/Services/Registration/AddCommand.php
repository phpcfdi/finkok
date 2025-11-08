<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class AddCommand
{
    private CustomerType $type;

    public function __construct(
        private string $rfc,
        ?CustomerType $type = null,
        private string $certificate = '',
        private string $privateKey = '',
        private string $passPhrase = ''
    ) {
        $this->type = $type ?? CustomerType::ondemand();
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function type(): CustomerType
    {
        return $this->type;
    }

    public function certificate(): string
    {
        return $this->certificate;
    }

    public function privateKey(): string
    {
        return $this->privateKey;
    }

    public function passPhrase(): string
    {
        return $this->passPhrase;
    }
}
