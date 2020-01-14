<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Finkok\Services\Registration\SwitchCommand;
use PhpCfdi\Finkok\Services\Registration\SwitchService;

class SwitchServiceTest extends RegistrationIntegrationTestCase
{
    protected function createService(): SwitchService
    {
        return new SwitchService($this->createSettingsFromEnvironment());
    }

    public function testSwitchServiceUsingExistentRfc(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();

        $currentType = $this->findCustomerOrFail($rfc)->customerType();
        $changeTo = ($currentType->isPrepaid()) ? CustomerType::ondemand() : CustomerType::prepaid();

        $service->switch(new SwitchCommand($rfc, $changeTo));
        $this->assertEquals(
            $changeTo,
            $this->findCustomerOrFail($rfc)->customerType(),
            'Expected to change user type to a different one'
        );

        $service->switch(new SwitchCommand($rfc, $currentType));
        $this->assertEquals(
            $currentType,
            $this->findCustomerOrFail($rfc)->customerType(),
            'Expected to change customer type to original'
        );
    }

    public function testSwitchServiceUsingExistentRfcToTheSameType(): void
    {
        $rfc = self::CUSTOMER_RFC;
        $service = $this->createService();
        $currentType = $this->findCustomerOrFail($rfc)->customerType();
        $response = $service->switch(new SwitchCommand($rfc, $currentType));
        $this->assertTrue($response->success(), 'Expected to change user type to the same type return success');
    }

    public function testSwitchUsingNonRegisteredRfc(): void
    {
        $rfc = self::CUSTOMER_NON_EXISTENT;
        $this->assertNull($this->findCustomer($rfc), "For this test RFC $rfc must not exists");

        $service = $this->createService();
        $result = $service->switch(new SwitchCommand($rfc, CustomerType::prepaid()));

        $this->assertFalse($result->success());
        $this->assertSame('RFC no encontrado', $result->message());
    }
}
