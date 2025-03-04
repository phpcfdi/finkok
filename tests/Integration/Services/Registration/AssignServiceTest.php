<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Services\Registration\AssignCommand;
use PhpCfdi\Finkok\Services\Registration\AssignService;
use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Finkok\Services\Registration\SwitchCommand;
use PhpCfdi\Finkok\Services\Registration\SwitchService;

final class AssignServiceTest extends RegistrationIntegrationTestCase
{
    protected function staticSettings(): FinkokSettings
    {
        /** @var FinkokSettings|null $settings */
        static $settings = null;
        if (null === $settings) {
            $settings = $this->createSettingsFromEnvironment();
        }
        return $settings;
    }

    protected function createService(): AssignService
    {
        return new AssignService($this->staticSettings());
    }

    public function resetCustomerAccountToOnDemand(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $customer = $this->findCustomerOrFail($rfc);
        if (! $customer->customerType()->isOndemand()) {
            $switchService = new SwitchService($this->staticSettings());
            $this->assertTrue(
                $switchService->switch(new SwitchCommand($rfc, CustomerType::ondemand()))->success(),
                'Unable to change the customer type to on-demand'
            );
            $customer = $this->findCustomerOrFail($rfc);
        }
        $this->assertTrue($customer->customerType()->isOndemand());
    }

    public function resetCustomerAccountToPrepaidWithZeroCredits(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();

        $customer = $this->findCustomerOrFail($rfc);
        if (! $customer->customerType()->isPrepaid()) {
            $switchService = new SwitchService($this->staticSettings());
            $this->assertTrue(
                $switchService->switch(new SwitchCommand($rfc, CustomerType::prepaid()))->success(),
                'Unable to change the customer type to prepaid'
            );
            $customer = $this->findCustomerOrFail($rfc);
        }
        $this->assertTrue($customer->customerType()->isPrepaid());

        // set credits to zero.
        if ($customer->credit() > 0) {
            $this->assertTrue(
                $service->assign(new AssignCommand($rfc, -1 * $customer->credit()))->success(),
                'Unable to set current credits to zero'
            );
            $customer = $this->findCustomerOrFail($rfc);
        }
        $this->assertSame(0, $customer->credit());
    }

    public function testAssignServiceToPrepaidAccount(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();

        // if it is prepaid change it to ondemand
        $this->resetCustomerAccountToPrepaidWithZeroCredits();

        $this->assertSame(10, $service->assign(new AssignCommand($rfc, 10))->credit(), 'Add 10 credits (total 10)');
        $this->assertSame(10, $this->findCustomerOrFail($rfc)->credit(), 'get did not report same credits as assign');

        $this->assertSame(25, $service->assign(new AssignCommand($rfc, 15))->credit(), 'Add 15 credits (total 25)');
        $this->assertSame(25, $this->findCustomerOrFail($rfc)->credit(), 'get did not report same credits as assign');

        $this->assertSame(24, $service->assign(new AssignCommand($rfc, -1))->credit(), 'Remove 1 credit (total 24)');
        $this->assertSame(24, $this->findCustomerOrFail($rfc)->credit(), 'get did not report same credits as assign');
    }

    public function testAssignServiceReducingMoreThanCurrentCredits(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();

        $this->resetCustomerAccountToPrepaidWithZeroCredits();

        $this->assertSame(3, $service->assign(new AssignCommand($rfc, 3))->credit(), 'Add 3 credits (total 3)');

        $assignResult = $service->assign(new AssignCommand($rfc, -10));
        $this->assertFalse($assignResult->success(), 'Expected to return non success');
        $this->assertSame(3, $assignResult->credit(), 'Credit must contain previous credits');
        $this->assertSame(
            'The number of credits to decrease is greater than the number of current credits.',
            $assignResult->message()
        );
    }

    public function testAssignServiceToOnDemandAccount(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();
        $this->resetCustomerAccountToOnDemand();
        $result = $service->assign(new AssignCommand($rfc, 100));

        // expected properties
        $this->assertFalse($result->success());
        $this->assertSame('The user is on demand type', $result->message());
        $this->assertSame(-1, $result->credit());
    }

    public function testAssignUsingNonRegisteredRfc(): void
    {
        $rfc = self::CUSTOMER_NON_EXISTENT;
        $this->assertNull($this->findCustomer($rfc), "For this test RFC $rfc must not exists");

        $service = $this->createService();
        $result = $service->assign(new AssignCommand($rfc, 100));

        $this->assertFalse($result->success());
        $this->assertSame('RFC no encontrado', $result->message());
    }
}
