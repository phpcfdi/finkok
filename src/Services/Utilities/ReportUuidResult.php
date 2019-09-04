<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class ReportUuidResult extends AbstractResult
{
    /** @var array[] */
    private $items;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'report_uuidResult');
        $this->items = [];

        $items = $this->findInDescendent($data, 'report_uuidResult', 'invoices', 'ReportUUID');
        if (! is_array($items)) {
            $items = [];
        }
        foreach ($items as $item) {
            $this->items[] = [
                'date' => strval($item->date),
                'uuid' => strval($item->uuid),
            ];
        }
    }

    /** @var array[] */
    public function items(): array
    {
        return $this->items;
    }
}
