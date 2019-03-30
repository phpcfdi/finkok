<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

class StampingResult
{
    /** @var array */
    private $alerts = [];

    public static function makeFromSoapResponse(object $response): self
    {
        return new self();
    }

    public function hasAlerts(): bool
    {
        return (count($this->alerts) > 0);
    }

    public function alerts(): array
    {
        return $this->alerts;
    }
}
