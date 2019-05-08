<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class AddCommand
{
    /** @var string */
    private $rfc;

    /** @var string */
    private $type;

    /** @var string */
    private $certificate;

    /** @var string */
    private $privateKey;

    /** @var string */
    private $passPhrase;

    public function __construct(
        string $rfc,
        string $type,
        string $certificate,
        string $privateKey,
        string $passPhrase
    ) {
        $this->rfc = $rfc;
        $this->type = $type;
        $this->certificate = $certificate;
        $this->privateKey = $privateKey;
        $this->passPhrase = $passPhrase;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function type(): string
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
