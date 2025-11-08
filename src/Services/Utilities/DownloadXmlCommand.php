<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

class DownloadXmlCommand
{
    private string $uuid;

    private string $rfc;

    private string $type;

    public function __construct(string $uuid, string $rfc, string $type)
    {
        $this->uuid = $uuid;
        $this->rfc = $rfc;
        $this->type = $type;
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
