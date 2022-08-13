<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\AddCommand;
use PhpCfdi\Finkok\Services\Registration\AddService;
use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Rfc\Rfc;

final class AddServiceTest extends RegistrationIntegrationTestCase
{
    protected function createService(): AddService
    {
        return new AddService($this->createSettingsFromEnvironment());
    }

    protected function searchForFreeRfc(): ?Rfc
    {
        // binary search: it will always make 16 queries, only 65536 records are allowed
        $lowerSerial = Rfc::parse('XDEL990101000')->calculateSerial();
        $upperSerial = $lowerSerial - 1 + (2 ** 16);
        $selected = null;
        while ($lowerSerial <= $upperSerial) {
            $currentSerial = intval(round(floor(($lowerSerial + $upperSerial) / 2), 0));
            $rfc = Rfc::fromSerial($currentSerial);
            if (null === $this->findCustomer($rfc->getRfc())) { // not found, then the RFC is free
                $selected = $rfc;
                $upperSerial = $currentSerial - 1;
            } else {
                $lowerSerial = $currentSerial + 1;
            }
        }
        return $selected;
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

    /**
     * @see searchForFreeRfc
     */
    public function testConsumeAddServiceWithRandomRfc(): void
    {
        // Finkok does not have a method (automated or manual) to remove customers.
        // This is why it takes so long to run.
        // To remove any RFC email soporte@finkok.com asking for it.

        if (! $this->getenv('FINKOK_REGISTRATION_ADD_CREATE_RANDOM_RFC')) {
            $this->markTestSkipped('This test is skipped as will create a lot of garbage customers');
        }

        $freeRfc = $this->searchForFreeRfc();
        if (null === $freeRfc) {
            $this->fail('Looks like all RFC for testing are used, ask Finkok to remove them');
        }
        $rfc = $freeRfc->getRfc();

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
