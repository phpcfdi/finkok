<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class EditResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'editResult');
    }

    public function success(): bool
    {
        return boolval($this->get('success'));
    }

    public function message(): string
    {
        return strval($this->get('message'));
    }
}
