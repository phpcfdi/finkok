<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Internal;

use stdClass;

trait MethodsFilterVariablesTrait
{
    /** @return array<stdClass> */
    private function filterArrayOfStdClass(mixed $variable): array
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

    /** @return array<string> */
    private function filterArrayOfStrings(mixed $variable): array
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

    private function filterString(mixed $variable): string
    {
        if (! is_scalar($variable)) {
            return '';
        }
        return strval($variable);
    }
}
