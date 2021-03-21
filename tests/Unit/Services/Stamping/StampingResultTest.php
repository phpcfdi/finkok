<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class StampingResultTest extends TestCase
{
    public function testUsingKnownStampResponse(): void
    {
        $data = json_decode($this->fileContentPath('stamp-response-with-alerts.json'));
        $response = new StampingResult('stampResult', $data);
        $this->assertCount(2, $response->alerts());
    }

    public function testHasAlerts(): void
    {
        $data = json_decode($this->fileContentPath('stamp-response-with-alerts.json'));
        $response = new StampingResult('stampResult', $data);
        $this->assertTrue($response->hasAlerts());
    }
}
