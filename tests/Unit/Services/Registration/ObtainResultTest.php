<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainResult;

use PhpCfdi\Finkok\Tests\TestCase;

final class ObtainResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $result = new ObtainResult($data);
        $this->assertSame('predefined-message', $result->message());
        $this->assertCount(2, $result->customers());
    }
}
