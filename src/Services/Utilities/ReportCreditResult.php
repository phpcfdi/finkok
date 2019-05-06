<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class ReportCreditResult extends AbstractResult
{
    private $items;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'report_creditResult');

        $items = [];
        foreach ($this->findInDescendent($data, 'report_creditResult', 'result', 'ReportTotalCredit') as $item) {
            $items[] = [
                'credit' => strval($item->credit),
                'date' => strval($item->date),
            ];
        }
        $this->items = $items;
    }

    public function items(): array
    {
        return $this->items;
    }
}
