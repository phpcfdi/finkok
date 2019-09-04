<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\Customers;
use PhpCfdi\Finkok\Tests\TestCase;

class CustomersTest extends TestCase
{
    public function testCreateUsingNoItems(): void
    {
        $customers = new Customers([]);
        $this->assertCount(0, $customers);
    }

    public function testFindByRfc(): void
    {
        $data = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $customers = new Customers($data->getResult->users->ResellerUser);
        $known = $customers->findByRfc('LAN7008173R5');
        if (null === $known) {
            $this->fail('Could not find a predefined customer in customers');
            return;
        }
        $this->assertSame('LAN7008173R5', $known->rfc(), 'known rfc finding must match');
        $this->assertNull($customers->findByRfc('AAA010101AAA'));
    }

    public function testGetByRfcUsingExistentRfc(): void
    {
        $expectedRfc = 'LAN7008173R5';
        $data = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $customers = new Customers($data->getResult->users->ResellerUser);
        $this->assertSame($expectedRfc, $customers->getByRfc($expectedRfc)->rfc());
    }

    public function testGetByRfcUsingNonExistentRfcThrowsException(): void
    {
        $data = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $customers = new Customers($data->getResult->users->ResellerUser);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('There is no customer with RFC AAA010101AAA');
        $customers->getByRfc('AAA010101AAA');
    }
}
