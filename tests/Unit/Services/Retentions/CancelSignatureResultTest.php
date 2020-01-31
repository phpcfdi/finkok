<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Services\Retentions\CancelSignatureResult;
use PhpCfdi\Finkok\Tests\TestCase;

class CancelSignatureResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('retentions-cancelsignature-response.json'));
        $result = new CancelSignatureResult($data);
        $this->assertCount(2, $result->documents());
        $this->assertSame('11111111-2222-3333-4444-000000000001', $result->documents()->get(0)->uuid());
        $this->assertSame('voucher', $result->voucher());
        $this->assertSame('2019-04-05 16:29:47.138032', $result->date());
        $this->assertSame('LAN7008173R5', $result->rfc());
        $this->assertSame('304', $result->statusCode());
    }
}
