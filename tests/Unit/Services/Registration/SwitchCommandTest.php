<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Finkok\Services\Registration\SwitchCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class SwitchCommandTest extends TestCase
{
    public function testSwitchCommandCreation(): void
    {
        $type = CustomerType::ondemand();
        $command = new SwitchCommand('x-rfc', $type);
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame($type, $command->customerType());
    }
}
