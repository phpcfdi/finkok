<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetPendingResult extends AbstractResult
{
    use MethodsFilterVariablesTrait;

    /** @var string[] */
    private $uuids;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'get_pendingResult');
        $items = $this->findInDescendent($data, 'get_pendingResult', 'uuids', 'string') ?? [];
        $this->uuids = $this->filterArrayOfStrings($items);
    }

    /** @return string[] */
    public function uuids(): array
    {
        return $this->uuids;
    }

    public function error(): string
    {
        return $this->get('error');
    }
}
