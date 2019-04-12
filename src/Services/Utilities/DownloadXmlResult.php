<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class DownloadXmlResult extends AbstractResult
{
    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'get_xmlResult');
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
