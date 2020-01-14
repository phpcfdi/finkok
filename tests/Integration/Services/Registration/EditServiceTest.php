<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\CustomerStatus;
use PhpCfdi\Finkok\Services\Registration\EditCommand;
use PhpCfdi\Finkok\Services\Registration\EditService;

class EditServiceTest extends RegistrationIntegrationTestCase
{
    protected function createService(): EditService
    {
        $editService = new EditService($this->createSettingsFromEnvironment());
        return $editService;
    }

    public function testConsumeEditServiceUsingExistentRfc(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();

        if (! $this->findCustomerOrFail($rfc)->status()->isActive()) {
            $service->edit(new EditCommand($rfc, CustomerStatus::active()));
        }

        $editToSuspended = $service->edit(new EditCommand($rfc, CustomerStatus::suspended()));
        $this->assertTrue($editToSuspended->success());
        $this->assertSame('Account was Suspended successfully', $editToSuspended->message());
        $this->assertTrue(
            $this->findCustomerOrFail($rfc)->status()->isSuspended(),
            "Customer $rfc was not changed to Suspended"
        );

        $editToActive = $service->edit(new EditCommand($rfc, CustomerStatus::active()));
        $this->assertTrue($editToActive->success());
        $this->assertSame('Account was Activated successfully', $editToActive->message());
        $this->assertTrue(
            $this->findCustomerOrFail($rfc)->status()->isActive(),
            "Customer $rfc was not changed to Active"
        );
    }

    public function testConsumeEditServiceDoubleEditWithSameData(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();

        if (! $this->findCustomerOrFail($rfc)->status()->isActive()) {
            $service->edit(new EditCommand($rfc, CustomerStatus::active()));
        }

        $editToActive = $service->edit(new EditCommand($rfc, CustomerStatus::active()));
        $this->assertTrue($editToActive->success());
        $this->assertSame('Account was Activated successfully', $editToActive->message());
        $this->assertTrue(
            $this->findCustomerOrFail($rfc)->status()->isActive(),
            "Customer $rfc was not changed to Active"
        );
    }

    public function testConsumeEditServiceUsingNotRegisteredRfc(): void
    {
        $rfc = self::CUSTOMER_NON_EXISTENT;
        $this->assertNull($this->findCustomer($rfc), "For this test RFC $rfc must not exists");

        $service = $this->createService();
        $result = $service->edit(new EditCommand($rfc, CustomerStatus::active()));

        $this->assertFalse($result->success());
        $this->assertSame('ERROR: El rfc no se encuentra registrado', $result->message());
    }
}
