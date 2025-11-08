<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

class QueryPendingCommand
{
    public function __construct(private string $uuid)
    {
    }

    public function uuid(): string
    {
        return $this->uuid;
    }
}
