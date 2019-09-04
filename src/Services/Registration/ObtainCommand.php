<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

class ObtainCommand
{
    /** @var string */
    private $rfc;

    public function __construct(string $rfc = '')
    {
        $this->rfc = $rfc;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }
}
