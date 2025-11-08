<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

class GetPendingCommand
{
    public function __construct(private string $rfc)
    {
    }

    public function rfc(): string
    {
        return $this->rfc;
    }
}
