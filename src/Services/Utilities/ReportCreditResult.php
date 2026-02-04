<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class ReportCreditResult extends AbstractResult
{
    use MethodsFilterVariablesTrait;

    /** @var array<array{credit: string, date: string}> */
    private array $items = [];

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'report_creditResult');

        $items = $this->filterArrayOfStdClass(
            $this->findInDescendent($data, 'report_creditResult', 'result', 'ReportTotalCredit'),
        );
        foreach ($items as $item) {
            $this->items[] = [
                'credit' => $this->filterString($item->credit),
                'date' => $this->filterString($item->date),
            ];
        }
    }

    /** @return array<array{credit: string, date: string}> */
    public function items(): array
    {
        return $this->items;
    }

    public function error(): string
    {
        return $this->get('error');
    }
}
