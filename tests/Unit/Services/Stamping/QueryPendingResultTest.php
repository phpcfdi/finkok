<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QueryPendingResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class QueryPendingResultTest extends TestCase
{
    public function testUsingKnownStampResponse(): void
    {
        $data = json_decode($this->fileContentPath('querypending-response.json'));
        $response = new QueryPendingResult($data);
        $this->assertSame('S', $response->status());
        $this->assertSame('<xml/>', $response->xml());
        $this->assertSame('uuid', $response->uuid());
        $this->assertSame('uuid_status', $response->uuidStatus());
        $this->assertSame('next_attempt', $response->nextAttempt());
        $this->assertSame('1', $response->attempts());
        $this->assertSame('error', $response->error());
        $this->assertSame('date', $response->date());
    }
}
