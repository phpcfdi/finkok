<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

use Eclipxe\Enum\Enum;

/**
 * @method static self stamping()
 * @method static self utilities()
 */
class Services extends Enum
{
    protected static function overrideValues(): array
    {
        return [
            'stamping' => '/servicios/soap/stamp.wsdl',
            'utilities' => '/servicios/soap/utilities.wsdl',
        ];
    }
}
