<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use stdClass;

class DownloadXmlResult
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
        return strval($this->data->{'get_xmlResult'}->{$keyword} ?? '');
    }

    public function xml(): string
    {
        return $this->get('xml');
    }

    public function error(): string
    {
        return $this->get('error');
    }
}
