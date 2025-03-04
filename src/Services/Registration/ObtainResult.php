<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Internal\MethodsFilterVariablesTrait;
use PhpCfdi\Finkok\Services\AbstractResult;
use stdClass;

class ObtainResult extends AbstractResult
{
    use MethodsFilterVariablesTrait;

    /** @var Customers */
    private $customers;

    public function __construct(stdClass $data)
    {
        parent::__construct($data, 'getResult');
        $customers = $this->findInDescendent($data, 'getResult', 'users', 'ResellerUser');
        $this->customers = new Customers($this->filterArrayOfStdClass($customers));
    }

    public function message(): string
    {
        return $this->get('message');
    }

    public function customers(): Customers
    {
        return $this->customers;
    }
}
