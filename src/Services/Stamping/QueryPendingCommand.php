<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

class QueryPendingCommand
{
    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }
}
