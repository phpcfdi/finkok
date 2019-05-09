<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class EditCommand
{
    /** @var string */
    private $rfc;

    /** @var CustomerStatus */
    private $status;

    /** @var string */
    private $certificate;

    /** @var string */
    private $privateKey;

    /** @var string */
    private $passPhrase;

    public function __construct(
        string $rfc,
        CustomerStatus $status,
        string $certificate = '',
        string $privateKey = '',
        string $passPhrase = ''
    ) {
        $this->rfc = $rfc;
        $this->status = $status;
        $this->certificate = $certificate;
        $this->privateKey = $privateKey;
        $this->passPhrase = $passPhrase;
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
