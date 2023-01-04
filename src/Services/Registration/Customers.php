<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use ArrayIterator;
use LogicException;
use PhpCfdi\Finkok\Services\AbstractCollection;
use stdClass;

/**
 * @method Customer get(int $index)
 * @method Customer first()
 * @method ArrayIterator|Customer[] getIterator()
 * @extends AbstractCollection<Customer>
 */
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
        foreach ($this->getIterator() as $customer) {
            if ($rfc === $customer->rfc()) {
                return $customer;
            }
        }
        return null;
    }

    public function merge(self $customers): self
    {
        $clone = clone $this;
        foreach ($customers as $customer) {
            $clone->collection->append($customer);
        }
        return $clone;
    }
}
