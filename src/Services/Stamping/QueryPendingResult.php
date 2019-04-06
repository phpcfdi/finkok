<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use stdClass;

class QueryPendingResult
{
    /** @var string */
    public $container;

    /** @var stdClass */
    private $data;

    public function __construct(string $container, stdClass $data)
    {
        $this->container = $container;
        $this->data = $data;
    }

    public function rawData(): stdClass
    {
        return $this->data;
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{$this->container}->{$keyword} ?? '');
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
