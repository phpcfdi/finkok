<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

use Eclipxe\Enum\Enum;

/**
 * @method static self stamping()
 * @method static self utilities()
 * @method static self cancel()
 * @method static self manifest()
 * @method static self registration()
 *
 * @method bool isStamping()
 * @method bool isUtilities()
 * @method bool isCancel()
 * @method bool isManifest()
 * @method bool isRegistration()
 */
class Services extends Enum
{
    protected static function overrideValues(): array
    {
        return [
            'stamping' => '/servicios/soap/stamp.wsdl',
            'utilities' => '/servicios/soap/utilities.wsdl',
            'cancel' => '/servicios/soap/cancel.wsdl',
            'manifest' => '/servicios/soap/firmar.wsdl',
            'registration' => '/servicios/soap/registration.wsdl',
        ];
    }
}
