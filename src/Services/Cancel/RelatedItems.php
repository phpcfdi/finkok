<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use ArrayIterator;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @method RelatedItem get(int $index)
 * @method RelatedItem first()
 * @method ArrayIterator|RelatedItem[] getIterator()
 */
class RelatedItems extends AbstractCollection
{
    protected function createItemFromStdClass(stdClass $content): object
    {
        return new RelatedItem(
            strval($content->{'uuid'} ?? ''),
            strval($content->{'emisor'} ?? ''),
            strval($content->{'receptor'} ?? '')
        );
    }
}
