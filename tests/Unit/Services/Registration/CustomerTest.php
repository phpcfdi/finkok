<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\Customer;
use PhpCfdi\Finkok\Tests\TestCase;

class CustomerTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $customer = new Customer((object) []);
        $this->assertSame([], $customer->values());
    }

    public function testCreateWithSampleData(): void
    {
        $predefinedData = [
            'status' => 'S',
            'counter' => 10,
            'taxpayer_id' => 'MAG041126GT8',
            'credit' => 20,
        ];
        $customer = new Customer((object) $predefinedData);
        $this->assertSame($predefinedData, $customer->values());

        $this->assertSame('S', $customer->status());
        $this->assertSame(10, $customer->counter());
        $this->assertSame('MAG041126GT8', $customer->rfc());
        $this->assertSame(20, $customer->credit());
    }
}
