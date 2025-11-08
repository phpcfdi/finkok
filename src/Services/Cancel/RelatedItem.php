<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

class RelatedItem
{
    private string $uuid;

    private string $rfcEmitter;

    private string $rfcReceiver;

    public function __construct(string $uuid, string $rfcEmitter, string $rfcReceiver)
    {
        $this->uuid = $uuid;
        $this->rfcEmitter = $rfcEmitter;
        $this->rfcReceiver = $rfcReceiver;
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
