<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

final class ObtainCustomersResult extends AbstractResult
{
    /** @var Customers */
    private $customers;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'customersResult');
        $customers = $this->findInDescendent($data, 'customersResult', 'users', 'ResellerUser');
        $this->customers = new Customers(is_array($customers) ? $customers : []);
    }

    public function message(): string
    {
        return $this->get('message');
    }

    public function customers(): Customers
    {
        return $this->customers;
    }

    public function pagesInformation(): PageInformation
    {
        $message = $this->message();
        return PageInformation::fromMessage($message);
    }
}
