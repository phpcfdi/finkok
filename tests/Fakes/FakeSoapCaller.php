<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Fakes;

use PhpCfdi\Finkok\SoapCaller;
use stdClass;

final class FakeSoapCaller extends SoapCaller
{
    public stdClass $preparedResult;

    public string $latestCallMethodName = '';

    /** @var array<mixed> */
    public array $latestCallParameters = [];

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function call(string $methodName, array $parameters): stdClass
    {
        $this->latestCallMethodName = $methodName;
        $this->latestCallParameters = $parameters;
        return $this->preparedResult;
    }
}
