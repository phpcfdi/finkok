<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetReceiptResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetReceiptResultTest extends TestCase
{
    public function testResultUsingPredefinedResponse(): void
    {
        $data = json_decode($this->fileContentPath('cancel-get-receipt-response.json'));
        $result = new GetReceiptResult($data);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('x-uuid', $result->uuid());
        $this->assertSame('x-receipt', $result->receipt());
        $this->assertSame('x-rfc', $result->rfc());
        $this->assertSame('x-error', $result->error());
        $this->assertSame('x-date', $result->date());
    }
}
