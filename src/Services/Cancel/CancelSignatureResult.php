<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use stdClass;

class CancelSignatureResult
{
    /** @var string */
    public $container;

    /** @var CancelledDocuments */
    private $alerts;

    /** @var stdClass */
    private $data;

    public function __construct(stdClass $data)
    {
        $container = 'cancel_signatureResult';
        $this->container = $container;
        $this->data = $data;

        $documents = $data->{$container}->{'Folios'}->{'Folio'} ?? null;
        if (! is_array($documents)) {
            $documents = [];
        }

        $this->alerts = new CancelledDocuments($documents);
    }

    public function rawData(): stdClass
    {
        return $this->data;
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{$this->container}->{$keyword} ?? '');
    }

    public function voucher(): string
    {
        return $this->get('Acuse');
    }

    public function date(): string
    {
        return $this->get('Fecha');
    }

    public function rfc(): string
    {
        return $this->get('RfcEmisor');
    }

    public function statusCode(): string
    {
        return $this->get('CodEstatus');
    }

    public function documents(): CancelledDocuments
    {
        return $this->alerts;
    }
}
