<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;

class CancelledDocuments implements Countable, IteratorAggregate
{
    /** @var ArrayObject|CancelledDocument[] */
    private $documents;

    public function __construct(array $collection)
    {
        $this->documents = new ArrayObject();
        foreach ($collection as $item) {
            $this->documents->append(new CancelledDocument($item));
        }
    }

    public function get(int $index): CancelledDocument
    {
        if (! isset($this->documents[$index])) {
            return new CancelledDocument((object) []);
        }
        return $this->documents[$index];
    }

    public function count(): int
    {
        return $this->documents->count();
    }

    public function first(): CancelledDocument
    {
        return $this->get(0);
    }

    public function find(string $uuid): ?CancelledDocument
    {
        foreach ($this->documents as $document) {
            if ($uuid === $document->uuid()) {
                return $document;
            }
        }
        return null;
    }

    /**
     * @return ArrayIterator|CancelledDocument[]
     */
    public function getIterator(): ArrayIterator
    {
        return $this->documents->getIterator();
    }
}
