<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetSignedContractsResult extends AbstractResult
{
    /** @var bool */
    private $success;

    /** @var string */
    private $contract;

    /** @var string */
    private $privacy;

    /** @var string */
    private $error;

    public function __construct(stdClass $data, bool $isBase64)
    {
        parent::__construct($data, 'get_documentsResult');
        $this->success = boolval($this->get('success'));
        $this->contract = $this->get('contract');
        $this->privacy = $this->get('privacy');
        $this->error = $this->get('error');
        if ($isBase64) {
            $this->contract = base64_decode($this->contract) ?: '';
            $this->privacy = base64_decode($this->privacy) ?: '';
        }
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function contract(): string
    {
        return $this->contract;
    }

    public function privacy(): string
    {
        return $this->privacy;
    }

    public function error(): string
    {
        return $this->error;
    }
}
