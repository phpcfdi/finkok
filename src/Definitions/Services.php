<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

use Eclipxe\Enum\Enum;

/**
 * @method static self stamping()
 */
class Services extends Enum
{
    protected static function overrideValues(): array
    {
        return [
            'stamping' => '/servicios/soap/stamp.wsdl',
        ];
    }
}
