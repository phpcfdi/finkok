<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use DateTimeImmutable;
use LogicException;

class ReportUuidCommand
{
    public function __construct(
        private string $rfc,
        private string $type,
        private DateTimeImmutable $since,
        private DateTimeImmutable $until,
    ) {
        if ($this->since > $this->until) {
            throw new LogicException('Since date is greater than until date');
        }
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function since(): DateTimeImmutable
    {
        return $this->since;
    }

    public function until(): DateTimeImmutable
    {
        return $this->until;
    }

    public function sinceString(): string
    {
        return $this->since->format('Y-m-d\TH:i:s');
    }

    public function untilString(): string
    {
        return $this->until->format('Y-m-d\TH:i:s');
    }
}
