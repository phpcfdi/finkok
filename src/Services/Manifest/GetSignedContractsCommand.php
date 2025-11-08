<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Definitions\SignedDocumentFormat;

class GetSignedContractsCommand
{
    public function __construct(private string $snid, private string $rfc, private SignedDocumentFormat $format)
    {
    }

    public function snid(): string
    {
        return $this->snid;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function format(): SignedDocumentFormat
    {
        return $this->format;
    }
}
