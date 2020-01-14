<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\SwitchResult;

use PhpCfdi\Finkok\Tests\TestCase;

class SwitchResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('registration-switch-response.json'));
        $result = new SwitchResult($data);
        $this->assertSame(true, $result->success());
        $this->assertSame('predefined-message', $result->message());
    }
}
