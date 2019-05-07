<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\SignContractsResult;
use PhpCfdi\Finkok\Tests\TestCase;

class SignContractsResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('manifest-signcontracts-response.json'));
        $result = new SignContractsResult($data);
        $this->assertTrue($result->success());
        $this->assertSame('predefined-message', $result->message());
    }
}
