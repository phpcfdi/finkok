<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class SwitchResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'switchResult');
    }

    public function success(): bool
    {
        return boolval($this->get('success'));
    }

    public function message(): string
    {
        return $this->get('message');
    }
}
