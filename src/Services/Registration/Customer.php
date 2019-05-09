<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use stdClass;

class Customer
{
    /** @var stdClass */
    private $data;

    public function __construct(stdClass $raw)
    {
        $this->data = $raw;
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{$keyword} ?? '');
    }

    public function status(): CustomerStatus
    {
        return new CustomerStatus($this->get('status'));
    }

    public function counter(): int
    {
        return intval($this->get('counter'));
    }

    public function rfc(): string
    {
        return $this->get('taxpayer_id');
    }

    public function credit(): int
    {
        return intval($this->get('credit'));
    }

    public function customerType(): CustomerType
    {
        if (-1 === $this->credit()) {
            return CustomerType::ondemand();
        }
        return CustomerType::prepaid();
    }

    public function values(): array
    {
        return (array) $this->data;
    }
}
