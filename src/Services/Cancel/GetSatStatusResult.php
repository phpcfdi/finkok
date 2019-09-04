<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetSatStatusResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'get_sat_statusResult', 'sat');
    }

    public function query(): string
    {
        return $this->get('CodigoEstatus');
    }

    public function cfdi(): string
    {
        return $this->get('Estado');
    }

    public function cancellable(): string
    {
        return $this->get('EsCancelable');
    }

    public function cancellation(): string
    {
        return $this->get('EstatusCancelacion');
    }
}
