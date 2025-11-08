<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Definitions\SignedDocumentFormat;

class GetSignedContractsCommand
{
    private string $snid;

    private string $rfc;

    private SignedDocumentFormat $format;

    public function __construct(string $snid, string $rfc, SignedDocumentFormat $format)
    {
        $this->snid = $snid;
        $this->rfc = $rfc;
        $this->format = $format;
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
