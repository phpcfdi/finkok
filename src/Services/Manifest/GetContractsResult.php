<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetContractsResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'get_contracts_snidResult');
    }

    public static function createFromData(bool $success, string $contract, string $privacy, string $error): self
    {
        return new self((object) [
            'get_contracts_snidResult' => (object) [
                'success' => $success,
                'contract' => base64_encode($contract),
                'privacy' => base64_encode($privacy),
                'error' => $error,
            ],
        ]);
    }

    public function success(): bool
    {
        return boolval($this->get('success'));
    }

    public function contract(): string
    {
        return base64_decode($this->get('contract'), true) ?: '';
    }

    public function privacy(): string
    {
        return base64_decode($this->get('privacy'), true) ?: '';
    }

    public function error(): string
    {
        return $this->get('error');
    }
}
