<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @extends AbstractCollection<CancelledDocument>
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
