<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\AssignCommand;
use PhpCfdi\Finkok\Services\Registration\AssignService;

class AssignServiceTest extends RegistrationIntegrationTestCase
{
    public function testAssignServiceToOnDemandAccount(): void
    {
        $rfc = 'XDEL000101XX1';
        $service = new AssignService($this->createSettingsFromEnvironment());

        // if it is not ondemand change it
        if (-1 !== $this->findCustomerOrFail($rfc)) {
            $this->assertSame(-1, $service->assign(new AssignCommand($rfc, -1))->credit());
        }

        $this->assertSame(10, $service->assign(new AssignCommand($rfc, 10))->credit(), 'Add 10 credits (total 10)');
        $this->assertSame(10, $this->findCustomerOrFail($rfc)->credit(), 'get did not report same credits as assign');

        $this->assertSame(25, $service->assign(new AssignCommand($rfc, 15))->credit(), 'Add 15 credits (total 25)');
        $this->assertSame(25, $this->findCustomerOrFail($rfc)->credit(), 'get did not report same credits as assign');

        $this->assertSame(-1, $service->assign(new AssignCommand($rfc, -1))->credit(), 'Mark as ondemand');
        $this->assertSame(-1, $this->findCustomerOrFail($rfc)->credit(), 'get did not report same credits as assign');
    }
}
