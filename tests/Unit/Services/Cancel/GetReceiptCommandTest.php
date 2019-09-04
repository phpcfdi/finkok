<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Definitions\ReceiptType;
use PhpCfdi\Finkok\Services\Cancel\GetReceiptCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class GetReceiptCommandTest extends TestCase
{
    public function testCommandValues(): void
    {
        $receiptType = ReceiptType::cancellation();
        $command = new GetReceiptCommand('x-rfc', 'x-uuid', $receiptType);
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame('x-uuid', $command->uuid());
        $this->assertSame($receiptType, $command->type());
    }
}
