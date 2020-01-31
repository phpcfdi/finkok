<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Services\Retentions\StampedResult;
use PhpCfdi\Finkok\Tests\TestCase;

class StampedResultTest extends TestCase
{
    public function testUsingKnownStampResponse(): void
    {
        $data = json_decode($this->fileContentPath('retentions-stamped-response.json'));
        $response = new StampedResult($data);
        $this->assertSame('x-xml', $response->xml());
        $this->assertSame('x-uuid', $response->uuid());
        $this->assertSame('x-date', $response->date());
        $this->assertSame('x-fault-string', $response->faultString());
        $this->assertSame('x-fault-code', $response->faultCode());
        $this->assertSame('x-code-status', $response->statusCode());
        $this->assertSame('x-sat-seal', $response->seal());
        $this->assertSame('x-no-certificado-sat', $response->certificateSat());
        $this->assertCount(2, $response->alerts());
        foreach ($response->alerts() as $index => $alert) {
            $key = sprintf('x%d', $index + 1);
            $this->assertSame("$key-00001", $alert->id());
            $this->assertSame("$key-rfc", $alert->rfc());
            $this->assertSame("$key-uuid", $alert->uuid());
            $this->assertSame("$key-error-code", $alert->errorCode());
            $this->assertSame("$key-work-process-id", $alert->workProcessId());
            $this->assertSame("$key-message", $alert->message());
            $this->assertSame("$key-extra-info", $alert->extraInfo());
            $this->assertSame("$key-no-certificado-pac", $alert->certificatePac());
            $this->assertSame("$key-registry-date", $alert->date());
        }
    }
}
