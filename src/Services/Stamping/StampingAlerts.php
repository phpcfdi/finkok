<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;

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

    public function get(int $index): StampingAlert
    {
        if (! isset($this->alerts[$index])) {
            return new StampingAlert((object) []);
        }
        return $this->alerts[$index];
    }

    public function count(): int
    {
        return $this->alerts->count();
    }

    public function first(): StampingAlert
    {
        return $this->get(0);
    }

    /**
     * @return ArrayIterator|StampingAlert[]
     */
    public function getIterator(): ArrayIterator
    {
        return $this->alerts->getIterator();
    }
}
