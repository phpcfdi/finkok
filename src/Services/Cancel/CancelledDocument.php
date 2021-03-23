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

    /**
     * @return string
     * @deprecated 0.3.2
     * @see self::cancellationStatus
     */
    public function cancellationSatatus(): string
    {
        trigger_error(
            sprintf('%s is deprecated since 0.3.2 and will be removed on 0.4.0', __METHOD__),
            E_USER_DEPRECATED
        );
        return $this->cancellationStatus();
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
