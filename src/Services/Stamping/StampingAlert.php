<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use stdClass;

class StampingAlert
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

    public function id(): string
    {
        return $this->get('IdIncidencia');
    }

    public function uuid(): string
    {
        return $this->get('Uuid');
    }

    public function errorCode(): string
    {
        return $this->get('CodigoError');
    }

    public function workProcessId(): string
    {
        return $this->get('WorkProcessId');
    }

    public function message(): string
    {
        return $this->get('MensajeIncidencia');
    }

    public function extraInfo(): string
    {
        return $this->get('ExtraInfo');
    }

    public function rfc(): string
    {
        return $this->get('RfcEmisor');
    }

    public function certificatePac(): string
    {
        return $this->get('NoCertificadoPac');
    }

    public function date(): string
    {
        return $this->get('FechaRegistro');
    }

    /** @return array<mixed> */
    public function values(): array
    {
        return (array) $this->data;
    }
}
