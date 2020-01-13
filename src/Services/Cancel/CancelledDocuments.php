<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use ArrayIterator;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @method CancelledDocument get(int $index)
 * @method CancelledDocument first()
 * @method ArrayIterator|CancelledDocument[] getIterator()
 */
class CancelledDocuments extends AbstractCollection
{
    protected function createItemFromStdClass(stdClass $content): object
    {
        return new CancelledDocument($content);
    }

    public function find(string $uuid): ?CancelledDocument
    {
        foreach ($this->getIterator() as $document) {
            if ($uuid === $document->uuid()) {
                return $document;
            }
        }
        return null;
    }
}
