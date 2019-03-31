<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use stdClass;

class StampingResult
{
    /** @var StampingAlerts */
    private $alerts;

    /** @var stdClass */
    private $data;

    public function __construct(stdClass $data)
    {
        $this->data = $data;
        $this->alerts = new StampingAlerts();
        $alerts = $data->{'Incidencias'}->{'Incidencia'} ?? null;
        if (! is_array($alerts)) {
            $alerts = [];
        }
        $this->alerts->hydrate($alerts);
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{$keyword} ?? '');
    }

    public function xml(): string
    {
        return $this->get('xml');
    }

    public function uuid(): string
    {
        return $this->get('UUID');
    }

    public function faultstring(): string
    {
        return $this->get('faultstring');
    }

    public function date(): string
    {
        return $this->get('Fecha');
    }

    public function statusCode(): string
    {
        return $this->get('CodEstatus');
    }

    public function seal(): string
    {
        return $this->get('SatSeal');
    }

    public function certificateSat(): string
    {
        return $this->get('NoCertificadoSAT');
    }

    public function alerts(): StampingAlerts
    {
        return $this->alerts;
    }
}
