<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

class StampingCommand
{
    public function __construct(private string $xml)
    {
    }

    public function xml(): string
    {
        return $this->xml;
    }
}
