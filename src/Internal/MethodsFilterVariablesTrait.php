<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Internal;

use stdClass;

trait MethodsFilterVariablesTrait
{
    /**
     * @param mixed $variable
     * @return array<stdClass>
     */
    private function filterArrayOfStdClass($variable): array
    {
        if (! is_array($variable)) {
            return [];
        }
        $result = [];
        foreach ($variable as $index => $item) {
            if (! $item instanceof stdClass) {
                return [];
            }
            $result[$index] = $item;
        }
        return $result;
    }

    /**
     * @param mixed $variable
     * @return array<string>
     */
    private function filterArrayOfStrings($variable): array
    {
        if (! is_array($variable)) {
            return [];
        }
        $result = [];
        foreach ($variable as $index => $item) {
            if (! is_string($item)) {
                return [];
            }
            $result[$index] = $item;
        }
        return $result;
    }

    /**
     * @param mixed $variable
     * @return string
     */
    private function filterString($variable): string
    {
        if (! is_scalar($variable)) {
            return '';
        }
        return strval($variable);
    }
}
