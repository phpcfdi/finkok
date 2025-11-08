<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

class GetSatStatusCommand
{
    public function __construct(
        private string $rfcIssuer,
        private string $rfcRecipient,
        private string $uuid,
        private string $total,
    ) {
    }

    public function rfcIssuer(): string
    {
        return $this->rfcIssuer;
    }

    public function rfcRecipient(): string
    {
        return $this->rfcRecipient;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function total(): string
    {
        return $this->total;
    }
}
