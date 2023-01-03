<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use LogicException;
use PhpCfdi\Finkok\Services\Registration\Customers;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class CustomersTest extends TestCase
{
    public function testCreateUsingNoItems(): void
    {
        $customers = new Customers([]);
        $this->assertCount(0, $customers);
    }

    public function testFindByRfc(): void
    {
        /** @var stdClass $data */
        $data = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $customers = new Customers($data->getResult->users->ResellerUser);
        $known = $customers->findByRfc('LAN7008173R5');
        if (null === $known) {
            $this->fail('Could not find a predefined customer in customers');
        }
        $this->assertSame('LAN7008173R5', $known->rfc(), 'known rfc finding must match');
        $this->assertNull($customers->findByRfc('AAA010101AAA'));
    }

    public function testGetByRfcUsingExistentRfc(): void
    {
        $expectedRfc = 'LAN7008173R5';
        /** @var stdClass $data */
        $data = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $customers = new Customers($data->getResult->users->ResellerUser);
        $this->assertSame($expectedRfc, $customers->getByRfc($expectedRfc)->rfc());
    }

    public function testGetByRfcUsingNonExistentRfcThrowsException(): void
    {
        /** @var stdClass $data */
        $data = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $customers = new Customers($data->getResult->users->ResellerUser);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('There is no customer with RFC AAA010101AAA');
        $customers->getByRfc('AAA010101AAA');
    }

    public function testMerge(): void
    {
        $firstList = [
            (object) ['taxpayer_id' => 'AAA010101AAA', 'status' => 'S', 'counter' => 0, 'credit' => 0],
            (object) ['taxpayer_id' => 'BBB010101AAA', 'status' => 'S', 'counter' => 0, 'credit' => 0],
        ];
        $firstCustomers = new Customers($firstList);

        $secondList = [
            (object) ['taxpayer_id' => 'CCC010101AAA', 'status' => 'S', 'counter' => 0, 'credit' => 0],
            (object) ['taxpayer_id' => 'DDD010101AAA', 'status' => 'S', 'counter' => 0, 'credit' => 0],
            (object) ['taxpayer_id' => 'EEE010101AAA', 'status' => 'S', 'counter' => 0, 'credit' => 0],
        ];
        $secondCustomers = new Customers($secondList);

        $merged = $firstCustomers->merge($secondCustomers);

        $this->assertCount(5, $merged);
        foreach ($firstCustomers as $customer) {
            $this->assertSame($customer, $merged->findByRfc($customer->rfc()));
        }
        foreach ($secondCustomers as $customer) {
            $this->assertSame($customer, $merged->findByRfc($customer->rfc()));
        }
    }
}
