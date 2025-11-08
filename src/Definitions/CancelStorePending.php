<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Definitions;

use Eclipxe\Enum\Enum;

/**
 * @method static self no()
 * @method static self yes()
 */
class CancelStorePending extends Enum
{
    public function asBool(): bool
    {
        return 'yes' === $this->value();
    }
}
