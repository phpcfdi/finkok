<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

use Eclipxe\Enum\Enum;

/**
 * @method static self xml()
 * @method static self pdf()
 * @method bool isXml()
 * @method bool isPdf()
 */
class SignedDocumentFormat extends Enum
{
    protected static function overrideValues(): array
    {
        return [
            'xml' => 'XML',
            'pdf' => 'PDF',
        ];
    }
}
