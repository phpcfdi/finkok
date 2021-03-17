<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use Eclipxe\Enum\Enum;

/**
 * @method static self ondemand()
 * @method static self prepaid()
 *
 * @method bool isOndemand()
 * @method bool isPrepaid()
 */
class CustomerType extends Enum
{
    /**
     * @inheritdoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected static function overrideValues(): array
    {
        return [
            'ondemand' => 'O',
            'prepaid' => 'P',
        ];
    }
}
