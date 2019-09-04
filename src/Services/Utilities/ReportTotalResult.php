<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class ReportTotalResult extends AbstractResult
{
    /** @var string */
    private $rfc;

    /** @var string */
    private $total;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'report_totalResult');
        $items = $this->findInDescendent($data, 'report_totalResult', 'result', 'ReportTotal') ?? [];
        $result = $items[0] ?? (object) [];
        $this->rfc = $result->taxpayer_id ?? '';
        $this->total = strval($result->total ?? '');
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function total(): string
    {
        return $this->total;
    }
}
