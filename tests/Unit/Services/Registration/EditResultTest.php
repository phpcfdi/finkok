<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\EditResult;

use PhpCfdi\Finkok\Tests\TestCase;

class EditResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('registration-edit-response.json'));
        $result = new EditResult($data);
        $this->assertSame(true, $result->success());
        $this->assertSame('predefined-message', $result->message());
    }
}
