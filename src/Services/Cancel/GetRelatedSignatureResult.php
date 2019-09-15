<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class GetRelatedSignatureResult extends AbstractResult
{
    /** @var RelatedItems */
    private $parents;

    /** @var RelatedItems */
    private $children;

    /** @var string */
    private $error;

    public function __construct(stdClass $data)
    {
        $container = 'get_related_signatureResult';
        parent::__construct($data, $container);
        $parents = $this->findInDescendent($data, $container, 'Padres', 'Padre');
        $this->parents = new RelatedItems(is_array($parents) ? $parents : []);
        $children = $this->findInDescendent($data, $container, 'Hijos', 'Hijo');
        $this->children = new RelatedItems(is_array($children) ? $children : []);
        $this->error = $this->get('error');
    }

    public function parents(): RelatedItems
    {
        return $this->parents;
    }

    public function children(): RelatedItems
    {
        return $this->children;
    }

    public function error(): string
    {
        return $this->error;
    }
}
