<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

class AcceptRejectSignatureCommand
{
    public function __construct(private string $xml)
    {
    }

    public function xml(): string
    {
        return $this->xml;
    }
}
