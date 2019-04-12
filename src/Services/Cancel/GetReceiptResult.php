<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetReceiptResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'get_receiptResult');
    }

    public function isSuccess(): bool
    {
        return boolval($this->get('success'));
    }

    public function uuid(): string
    {
        return $this->get('uuid');
    }

    public function receipt(): string
    {
        return $this->get('receipt');
    }

    public function rfc(): string
    {
        return $this->get('taxpayer_id');
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
