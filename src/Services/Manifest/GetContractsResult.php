<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetContractsResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'get_contractsResult');
    }

    public function success(): bool
    {
        return boolval($this->get('success'));
    }

    public function contract(): string
    {
        return strval($this->get('contract'));
    }

    public function privacy(): string
    {
        return strval($this->get('privacy'));
    }

    public function error(): string
    {
        return strval($this->get('error'));
    }
}
