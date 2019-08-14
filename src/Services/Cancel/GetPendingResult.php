<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetPendingResult extends AbstractResult
{
    /** @var string[] */
    private $uuids;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'get_pendingResult');
        $items = $this->findInDescendent($data, 'get_pendingResult', 'uuids', 'string') ?? [];
        if (! is_array($items)) {
            $items = [];
        }
        $this->uuids = $items;
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
