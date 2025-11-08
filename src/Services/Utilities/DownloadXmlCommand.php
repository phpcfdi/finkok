<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

class DownloadXmlCommand
{
    public function __construct(private string $uuid, private string $rfc, private string $type)
    {
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function type(): string
    {
        return $this->type;
    }
}
