<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

class StampingCommand
{
    private string $xml;

    public function __construct(string $xml)
    {
        $this->xml = $xml;
    }

    public function xml(): string
    {
        return $this->xml;
    }
}
