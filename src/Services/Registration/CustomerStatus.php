<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use Eclipxe\Enum\Enum;

/**
 * @method static self active()
 * @method static self suspended()
 *
 * @method bool isActive()
 * @method bool isSuspended()
 */
class CustomerStatus extends Enum
{
    /**
     * @inheritdoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected static function overrideValues(): array
    {
        return [
            'active' => 'A',
            'suspended' => 'S',
        ];
    }
}
