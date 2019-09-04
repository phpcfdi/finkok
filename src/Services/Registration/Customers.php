<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use LogicException;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

class Customers extends AbstractCollection
{
    protected function createItemFromStdClass(stdClass $content): object
    {
        return new Customer($content);
    }

    public function getByRfc(string $rfc): Customer
    {
        $customer = $this->findByRfc($rfc);
        if (null === $customer) {
            throw new LogicException(sprintf('There is no customer with RFC %s', $rfc));
        }
        return $customer;
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
