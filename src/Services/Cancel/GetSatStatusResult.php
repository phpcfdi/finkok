<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use stdClass;

class GetSatStatusResult
{
    /** @var stdClass */
    private $data;

    public function __construct(stdClass $data)
    {
        $this->data = $data;
    }

    public function rawData(): stdClass
    {
        return $this->data;
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{'get_sat_statusResult'}->{'sat'}->{$keyword} ?? '');
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
