<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

use Eclipxe\Enum\Enum;

/**
 * @method static self development()
 * @method static self production()
 *
 * @method bool isDevelopment()
 * @method bool isProduction()
 */
class Environment extends Enum
{
    /**
     * @inheritdoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected static function overrideValues(): array
    {
        return [
            'development' => 'https://demo-facturacion.finkok.com',
            'production' => 'https://facturacion.finkok.com',
        ];
    }
}
