<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\AssignResult;

use PhpCfdi\Finkok\Tests\TestCase;

final class AssignResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('registration-assign-response.json'));
        $result = new AssignResult($data);
        $this->assertSame(true, $result->success());
        $this->assertSame(23, $result->credit());
        $this->assertSame('predefined-message', $result->message());
    }
}
