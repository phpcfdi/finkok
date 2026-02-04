<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @extends AbstractCollection<RelatedItem>
 */
class RelatedItems extends AbstractCollection
{
    use MethodsFilterVariablesTrait;

    protected function createItemFromStdClass(stdClass $content): object
    {
        return new RelatedItem(
            $this->filterString($content->{'uuid'} ?? ''),
            $this->filterString($content->{'emisor'} ?? ''),
            $this->filterString($content->{'receptor'} ?? ''),
        );
    }
}
