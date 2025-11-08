<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services;

use InvalidArgumentException;
use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use stdClass;

abstract class AbstractResult
{
    use MethodsFilterVariablesTrait;

    protected stdClass $root;

    public function __construct(protected stdClass $data, string ...$meanLocation)
    {
        $root = $this->findInDescendent($this->data, ...$meanLocation);
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
     * @template T
     * @param T $haystack
     * @param string ...$location
     * @return T|null
     */
    protected function findInDescendent(mixed $haystack, string ...$location): mixed
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
