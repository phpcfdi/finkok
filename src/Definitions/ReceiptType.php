<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

use Eclipxe\Enum\Enum;

/**
 * @method static self reception()
 * @method static self cancellation()
 */
class ReceiptType extends Enum
{
    protected static function overrideValues(): array
    {
        return [
            'reception' => 'I',
            'cancellation' => 'C',
        ];
    }
}
