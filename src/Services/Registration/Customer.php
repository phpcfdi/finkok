<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use stdClass;

class Customer
{
    /** @var stdClass */
    private $data;

    /** @var CustomerStatus */
    private $status;

    /** @var CustomerType */
    private $type;

    public function __construct(stdClass $raw)
    {
        $this->data = $raw;
        $rawStatus = strval($this->get('status'));
        if (in_array($rawStatus, CustomerStatus::toArray())) {
            $this->status = new CustomerStatus($rawStatus);
        } else {
            $this->status = CustomerStatus::suspended();
        }
        $this->type = (-1 === $this->credit()) ? CustomerType::ondemand() : CustomerType::prepaid();
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{$keyword} ?? '');
    }

    public function status(): CustomerStatus
    {
        return $this->status;
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
        return $this->type;
    }

    public function values(): array
    {
        return (array) $this->data;
    }
}
