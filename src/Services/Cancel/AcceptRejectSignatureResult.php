<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class AcceptRejectSignatureResult extends AbstractResult
{
    /** @var AcceptRejectUuidList */
    private $uuids;

    /** @var string */
    private $error;

    public function __construct(stdClass $data)
    {
        $container = 'accept_rejectResult';
        parent::__construct($data, $container);
        $aceptacion = $this->findInDescendent($data, $container, 'aceptacion');
        $rechazo = $this->findInDescendent($data, $container, 'rechazo');
        $this->uuids = new AcceptRejectUuidList(
            array_merge(
                is_array($aceptacion) ? $aceptacion : [],
                is_array($rechazo) ? $rechazo : []
            )
        );
        $this->error = $this->get('error');
    }

    public function uuids(): AcceptRejectUuidList
    {
        return $this->uuids;
    }

    public function error(): string
    {
        return $this->error;
    }
}
