<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

class StampingCommand
{
    /** @var string */
    private $xml;

    public function __construct(string $xml)
    {
        $this->xml = $xml;
    }

    public function xml(): string
    {
        return $this->xml;
    }
}
