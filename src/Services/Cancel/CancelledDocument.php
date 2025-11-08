<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use stdClass;

class CancelledDocument
{
    use MethodsFilterVariablesTrait;

    public function __construct(private stdClass $data)
    {
    }

    private function get(string $keyword): string
    {
        return $this->filterString($this->data->{$keyword} ?? '');
    }

    public function uuid(): string
    {
        return $this->get('UUID');
    }

    public function documentStatus(): string
    {
        return $this->get('EstatusUUID');
    }

    public function cancellationStatus(): string
    {
        return $this->get('EstatusCancelacion');
    }

    /** @return array<mixed> */
    public function values(): array
    {
        return (array) $this->data;
    }
}
