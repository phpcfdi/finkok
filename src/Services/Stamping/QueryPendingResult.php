<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class QueryPendingResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'query_pendingResult');
    }

    public function status(): string
    {
        return $this->get('status');
    }

    public function xml(): string
    {
        return $this->get('xml');
    }

    public function uuid(): string
    {
        return $this->get('uuid');
    }

    public function uuidStatus(): string
    {
        return $this->get('uuid_status');
    }

    public function nextAttempt(): string
    {
        return $this->get('next_attempt');
    }

    public function attempts(): string
    {
        return $this->get('attempts');
    }

    public function error(): string
    {
        return $this->get('error');
    }

    public function date(): string
    {
        return $this->get('date');
    }
}
