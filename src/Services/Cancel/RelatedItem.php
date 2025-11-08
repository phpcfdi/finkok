<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

class RelatedItem
{
    public function __construct(private string $uuid, private string $rfcEmitter, private string $rfcReceiver)
    {
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function rfcEmitter(): string
    {
        return $this->rfcEmitter;
    }

    public function rfcReceiver(): string
    {
        return $this->rfcReceiver;
    }
}
