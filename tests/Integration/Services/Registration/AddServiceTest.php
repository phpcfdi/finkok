<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use DateTime;
use PhpCfdi\Finkok\Services\Registration\AddCommand;
use PhpCfdi\Finkok\Services\Registration\AddService;
use PhpCfdi\Finkok\Services\Registration\CustomerType;

class AddServiceTest extends RegistrationIntegrationTestCase
{
    protected function createService(): AddService
    {
        return new AddService($this->createSettingsFromEnvironment());
    }

    protected function searchForFreeRfc(): string
    {
        $date = new DateTime('1900-01-01');
        do {
            $rfc = sprintf('XDEL%sXX1', $date->format('ymd'));
            if (null === $this->findCustomer($rfc)) {
                break;
            }
            $date->modify('+1 day');
        } while (true);
        return $rfc;
    }

    public function testConsumeAddServiceUsingExistentRfc(): void
    {
        $customer = new AddCommand('EKU9003173C9');
        $service = $this->createService();
        $result = $service->add($customer);

        // Finkok report success as true even when record already exists!
        $this->assertTrue($result->success());
        $this->assertSame('Account Already exists', $result->message());
    }

    public function testConsumeAddServiceWithRandomRfc(): void
    {
        // Finkok does not have a method (automated or manual) to remove customers.
        // This is why this test is always skipped.
        // To remove any RFC send an email to soporte@finkok.com asking for it.

        // If you really need to test comment the following lines .
        if (! boolval(getenv('FINKOK_REGISTRATION_ADD_CREATE_RANDOM_RFC') ?: 0)) {
            $this->markTestSkipped('This test is skipped as will create a lot of garbage customers');
        }

        $rfc = $this->searchForFreeRfc();
        $customer = new AddCommand($rfc, CustomerType::prepaid());
        $service = $this->createService();
        $result = $service->add($customer);

        $this->assertTrue($result->success());
        $this->assertSame('Account Created successfully', $result->message());

        $created = $this->findCustomerOrFail($rfc);
        $this->assertSame(0, $created->credit(), 'Prepaid account was not created with zero credits');
    }

    public function testConsumeAddServiceWithInvalidRfc(): void
    {
        $customer = new AddCommand('X', CustomerType::prepaid());
        $service = $this->createService();
        $result = $service->add($customer);

        $this->assertFalse($result->success());
        $this->assertSame('RFC Invalido', $result->message());
    }
}
