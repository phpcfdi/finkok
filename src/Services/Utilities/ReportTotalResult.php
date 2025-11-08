<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class ReportTotalResult extends AbstractResult
{
    use MethodsFilterVariablesTrait;

    private string $rfc;

    private string $total;

    private string $error;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'report_totalResult');
        /** @var array{stdClass|null} $items */
        $items = $this->findInDescendent($data, 'report_totalResult', 'result', 'ReportTotal') ?? [];
        $result = $items[0] ?? (object) [];
        $this->rfc = $this->filterString($result->taxpayer_id ?? '');
        $this->total = $this->filterString($result->total ?? '');
        $this->error = $this->get('error');
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function total(): string
    {
        return $this->total;
    }

    public function error(): string
    {
        return $this->error;
    }
}
