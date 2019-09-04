<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

class GetPendingCommand
{
    /** @var string */
    private $rfc;

    public function __construct(string $rfc)
    {
        $this->rfc = $rfc;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }
}
