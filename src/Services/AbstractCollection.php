<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services;

use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;
use stdClass;

abstract class AbstractCollection implements Countable, IteratorAggregate
{
    abstract protected function createItemFromStdClass(stdClass $content): object;

    /** @var ArrayObject */
    protected $collection;

    public function __construct(array $collection)
    {
        $this->collection = new ArrayObject();
        foreach ($collection as $content) {
            $this->collection->append($this->createItemFromStdClass($content));
        }
    }

    public function get(int $index): object
    {
        if (! isset($this->collection[$index])) {
            return $this->createItemFromStdClass((object) []);
        }
        return $this->collection[$index];
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function first(): object
    {
        return $this->get(0);
    }

    public function getIterator(): ArrayIterator
    {
        return $this->collection->getIterator();
    }
}
