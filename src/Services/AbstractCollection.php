<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services;

use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;
use stdClass;

/**
 * @template TItem of object
 * @implements IteratorAggregate<int, TItem>
 */
abstract class AbstractCollection implements Countable, IteratorAggregate
{
    /**
     * @param stdClass $content
     * @return TItem
     */
    abstract protected function createItemFromStdClass(stdClass $content): object;

    /** @var ArrayObject<int, TItem> */
    protected $collection;

    /** @param array<stdClass> $collection */
    public function __construct(array $collection)
    {
        $this->collection = new ArrayObject();
        foreach ($collection as $content) {
            $this->collection->append($this->createItemFromStdClass($content));
        }
    }

    /**
     * @param int $index
     * @return TItem
     */
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

    /** @return TItem */
    public function first(): object
    {
        return $this->get(0);
    }

    /** @return ArrayIterator<int, TItem> */
    public function getIterator(): ArrayIterator
    {
        return $this->collection->getIterator();
    }
}
