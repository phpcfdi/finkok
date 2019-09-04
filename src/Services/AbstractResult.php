<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services;

use InvalidArgumentException;
use stdClass;

abstract class AbstractResult
{
    /** @var stdClass */
    protected $data;

    /** @var stdClass */
    protected $root;

    public function __construct(stdClass $data, string ...$meanLocation)
    {
        $this->data = $data;
        $this->root = $this->findInDescendent($data, ...$meanLocation);
        if (! $this->root instanceof stdClass) {
            throw new InvalidArgumentException(
                sprintf('Unable to find mean object at /%s', implode('/', $meanLocation))
            );
        }
    }

    public function rawData(): stdClass
    {
        return clone $this->data;
    }

    /**
     * @param stdClass|array|mixed $haystack
     * @param string ...$location
     * @return mixed
     */
    protected function findInDescendent($haystack, string ...$location)
    {
        if (0 === count($location)) {
            return $haystack;
        }
        $search = array_shift($location);
        if (is_array($haystack)) {
            return (isset($haystack[$search])) ? $this->findInDescendent($haystack[$search], ...$location) : null;
        }
        if ($haystack instanceof stdClass) {
            return (isset($haystack->{$search})) ? $this->findInDescendent($haystack->{$search}, ...$location) : null;
        }
        throw new InvalidArgumentException('Cannot find descendent on non-array non-object haystack');
    }

    protected function get(string $keyword): string
    {
        return strval($this->root->{$keyword} ?? '');
    }
}
