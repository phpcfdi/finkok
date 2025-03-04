<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @extends AbstractCollection<StampingAlert>
 */
class StampingAlerts extends AbstractCollection
{
    protected function createItemFromStdClass(stdClass $content): object
    {
        return new StampingAlert($content);
    }

    public function findByErrorCode(string $errorCode): ?StampingAlert
    {
        foreach ($this->getIterator() as $alert) {
            if ($errorCode === $alert->errorCode()) {
                return $alert;
            }
        }
        return null;
    }
}
