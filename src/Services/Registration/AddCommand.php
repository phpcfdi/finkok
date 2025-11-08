<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class AddCommand
{
    private string $rfc;

    private CustomerType $type;

    private string $certificate;

    private string $privateKey;

    private string $passPhrase;

    public function __construct(
        string $rfc,
        ?CustomerType $type = null,
        string $certificate = '',
        string $privateKey = '',
        string $passPhrase = ''
    ) {
        $this->rfc = $rfc;
        $this->type = $type ?? CustomerType::ondemand();
        $this->certificate = $certificate;
        $this->privateKey = $privateKey;
        $this->passPhrase = $passPhrase;
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
