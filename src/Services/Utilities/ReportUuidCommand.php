<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use DateTimeImmutable;
use LogicException;

class ReportUuidCommand
{
    /** @var string */
    private $rfc;

    /** @var string */
    private $type;

    /** @var DateTimeImmutable */
    private $since;

    /** @var DateTimeImmutable */
    private $until;

    public function __construct(string $rfc, string $type, DateTimeImmutable $since, DateTimeImmutable $until)
    {
        if ($since > $until) {
            throw new LogicException('Since date is greater than until date');
        }
        $this->rfc = $rfc;
        $this->type = $type;
        $this->since = $since;
        $this->until = $until;
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
