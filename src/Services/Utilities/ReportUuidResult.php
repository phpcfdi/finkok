<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class ReportUuidResult extends AbstractResult
{
    use MethodsFilterVariablesTrait;

    /** @var array<int, array{date: string, uuid:string}> */
    private array $items = [];

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'report_uuidResult');

        $items = $this->filterArrayOfStdClass(
            $this->findInDescendent($data, 'report_uuidResult', 'invoices', 'ReportUUID'),
        );
        foreach ($items as $item) {
            $this->items[] = [
                'date' => $this->filterString($item->date),
                'uuid' => $this->filterString($item->uuid),
            ];
        }
    }

    /**
     * The returned array contains an array with keys date (string) and uuid (string)
     *
     * @return array<int, array{date: string, uuid:string}>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function error(): string
    {
        return $this->get('error');
    }
}
