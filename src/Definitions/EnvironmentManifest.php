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
class EnvironmentManifest extends Enum
{
    /**
     * @inheritdoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected static function overrideValues(): array
    {
        return [
            'development' => 'https://manifiesto.cfdiquadrum.com.mx:8008/',
            'production' => 'https://manifiesto.cfdiquadrum.com.mx/',
        ];
    }
}
