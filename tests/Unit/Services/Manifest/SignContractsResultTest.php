<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Services\Manifest\SignContractsResult;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class SignContractsResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        /** @var stdClass $data */
        $data = json_decode($this->fileContentPath('manifest-signcontracts-response.json'));
        $result = new SignContractsResult($data);
        $this->assertTrue($result->success());
        $this->assertSame('predefined-message', $result->message());
    }

    public function testCreateFromData(): void
    {
        $result = SignContractsResult::createFromData(true, 'x-message');
        $this->assertTrue($result->success());
        $this->assertSame('x-message', $result->message());
    }
}
