<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class StampingResult extends AbstractResult
{
    use MethodsFilterVariablesTrait;

    private StampingAlerts $alerts;

    public function __construct(string $container, stdClass $data)
    {
        parent::__construct($data, $container);
        $alerts = $this->findInDescendent($data, $container, 'Incidencias', 'Incidencia');
        $this->alerts = new StampingAlerts($this->filterArrayOfStdClass($alerts));
    }

    public function xml(): string
    {
        return $this->get('xml');
    }

    public function uuid(): string
    {
        return $this->get('UUID');
    }

    public function faultString(): string
    {
        return $this->get('faultstring');
    }

    public function faultCode(): string
    {
        return $this->get('faultcode');
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

    public function hasAlerts(): bool
    {
        return $this->alerts->count() > 0;
    }
}
