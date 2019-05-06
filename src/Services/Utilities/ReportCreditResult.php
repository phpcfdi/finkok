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
        $this->items = [];

        $items = $this->findInDescendent($data, 'report_creditResult', 'result', 'ReportTotalCredit');
        if (! is_array($items)) {
            $items = [];
        }
        foreach ($items as $item) {
            $this->items[] = [
                'credit' => strval($item->credit),
                'date' => strval($item->date),
            ];
        }
    }

    public function items(): array
    {
        return $this->items;
    }
}
