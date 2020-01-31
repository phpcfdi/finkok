<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Retentions;

use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use stdClass;

class StampedResult extends StampingResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct('stampedResult', $data);
    }
}
