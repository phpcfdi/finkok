<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use stdClass;

class DatetimeResult
{
    /** @var stdClass */
    private $data;

    public function __construct(stdClass $data)
    {
        $this->data = $data;
    }

    public function rawData(): stdClass
    {
        return $this->data;
    }

    private function get(string $keyword): string
    {
        return strval($this->data->{'datetimeResult'}->{$keyword} ?? '');
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
