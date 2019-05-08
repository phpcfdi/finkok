<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

class Customers extends AbstractCollection
{
    protected function createItemFromStdClass(stdClass $content): object
    {
        return new Customer($content);
    }

    public function findByRfc(string $rfc): ?Customer
    {
        /** @var Customer $customer */
        foreach ($this->collection as $customer) {
            if ($rfc === $customer->rfc()) {
                return $customer;
            }
        }
        return null;
    }
}
