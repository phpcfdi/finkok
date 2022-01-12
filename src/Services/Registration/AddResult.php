<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class AddResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'addResult');
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
