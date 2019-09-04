<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class DatetimeResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'datetimeResult');
    }

    public function datetime(): string
    {
        return $this->get('datetime');
    }

    public function error(): string
    {
        return $this->get('error');
    }
}
