<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use stdClass;

class CancelledDocument
{
    /** @var stdClass */
    private $data;

    public function __construct(stdClass $raw)
    {
        $this->data = $raw;
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{$keyword} ?? '');
    }

    public function uuid(): string
    {
        return $this->get('UUID');
    }

    public function documentStatus(): string
    {
        return $this->get('EstatusUUID');
    }

    public function cancellationSatatus(): string
    {
        return $this->get('EstatusCancelacion');
    }

    /** @return array<mixed> */
    public function values(): array
    {
        return (array) $this->data;
    }
}
