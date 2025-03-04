<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class AcceptRejectSignatureResult extends AbstractResult
{
    use MethodsFilterVariablesTrait;

    /** @var AcceptRejectUuidList */
    private $uuids;

    /** @var string */
    private $error;

    public function __construct(stdClass $data)
    {
        $container = 'accept_reject_signatureResult';
        parent::__construct($data, $container);
        $aceptacion = $this->findInDescendent($data, $container, 'aceptacion');
        $rechazo = $this->findInDescendent($data, $container, 'rechazo');
        $this->uuids = new AcceptRejectUuidList(
            array_merge(
                $this->filterArrayOfStdClass($aceptacion),
                $this->filterArrayOfStdClass($rechazo),
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
