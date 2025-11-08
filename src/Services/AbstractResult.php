<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services;

use InvalidArgumentException;
use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use stdClass;

abstract class AbstractResult
{
    use MethodsFilterVariablesTrait;

    protected stdClass $data;

    protected stdClass $root;

    public function __construct(stdClass $data, string ...$meanLocation)
    {
        $this->data = $data;
        $root = $this->findInDescendent($data, ...$meanLocation);
        if (! $root instanceof stdClass) {
            throw new InvalidArgumentException(
                sprintf('Unable to find mean object at /%s', implode('/', $meanLocation))
            );
        }
        $this->root = $root;
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
        if ([] === $location) {
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
        return $this->filterString($this->root->{$keyword} ?? '');
    }
}
