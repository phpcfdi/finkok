<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\ReceiptType;

class GetReceiptCommand
{
    /** @var string */
    private $rfc;

    /** @var string */
    private $uuid;

    /** @var ReceiptType */
    private $type;

    public function __construct(string $rfc, string $uuid, ReceiptType $type)
    {
        $this->rfc = $rfc;
        $this->uuid = $uuid;
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

    public function type(): ReceiptType
    {
        return $this->type;
    }
}
