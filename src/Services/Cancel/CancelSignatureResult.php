<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class CancelSignatureResult extends AbstractResult
{
    /** @var CancelledDocuments */
    private $documents;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'cancel_signatureResult');
        $documents = $this->findInDescendent($data, 'cancel_signatureResult', 'Folios', 'Folio');
        $this->documents = new CancelledDocuments(is_array($documents) ? $documents : []);
    }

    public function documents(): CancelledDocuments
    {
        return $this->documents;
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
}
