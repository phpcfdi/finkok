<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;
use RuntimeException;

class StampingAlerts implements Countable, IteratorAggregate
{
    /** @var ArrayObject */
    private $alerts;

    public function __construct(array $collection)
    {
        $this->alerts = new ArrayObject();
        foreach ($collection as $item) {
            $this->alerts->append(new StampingAlert($item));
        }
    }

    public function count(): int
    {
        return $this->alerts->count();
    }

    public function first(): StampingAlert
    {
        if (! isset($this->alerts[0])) {
            throw new RuntimeException('There are no stamping alerts');
        }
        return $this->alerts[0];
    }

    /**
     * @return ArrayIterator|StampingAlert[]
     */
    public function getIterator(): ArrayIterator
    {
        return $this->alerts->getIterator();
    }
}
