<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use ArrayIterator;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @method StampingAlert get(int $index)
 * @method StampingAlert first()
 * @method ArrayIterator|StampingAlert[] getIterator()
 */
class StampingAlerts extends AbstractCollection
{
    protected function createItemFromStdClass(stdClass $content): object
    {
        return new StampingAlert($content);
    }

    public function findByErrorCode(string $errorCode): ?StampingAlert
    {
        /** @var StampingAlert $alert */
        foreach ($this->collection as $alert) {
            if ($errorCode === $alert->errorCode()) {
                return $alert;
            }
        }
        return null;
    }
}
