<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\GetSignedContractsResult;
use PhpCfdi\Finkok\Tests\TestCase;

class GetSignedContractsResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('manifest-getsignedcontracts-response.json'));
        $result = new GetSignedContractsResult($data, false);
        $this->assertTrue($result->success());
        $this->assertSame('predefined-privacy', $result->privacy());
        $this->assertSame('predefined-contract', $result->contract());
        $this->assertSame('predefined-error', $result->error());
    }
}
