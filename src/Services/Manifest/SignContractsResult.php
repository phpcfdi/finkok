<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class SignContractsResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'sign_contractResult');
    }

    public static function createFromData(bool $success, string $message): self
    {
        return new self((object) [
            'sign_contractResult' => (object) [
                'success' => $success,
                'message' => $message,
            ],
        ]);
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
