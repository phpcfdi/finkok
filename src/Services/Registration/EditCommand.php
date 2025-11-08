<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class EditCommand
{
    public function __construct(
        private string $rfc,
        private CustomerStatus $status,
        private string $certificate = '',
        private string $privateKey = '',
        private string $passPhrase = '',
    ) {
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function status(): CustomerStatus
    {
        return $this->status;
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
