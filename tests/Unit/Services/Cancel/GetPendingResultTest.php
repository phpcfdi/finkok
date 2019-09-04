<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetPendingResult;
use PhpCfdi\Finkok\Tests\TestCase;

class GetPendingResultTest extends TestCase
{
    public function testResultUsingPredefinedResponse(): void
    {
        $data = json_decode($this->fileContentPath('cancel-get-pending-response-2-items.json'));
        $result = new GetPendingResult($data);
        $this->assertSame([
            '11111111-2222-3333-4444-000000000001',
            '11111111-2222-3333-4444-000000000002',
        ], $result->uuids());
        $this->assertSame('predefined-error', $result->error());
    }

    public function testResultUsingEmptyList(): void
    {
        $data = json_decode('{"get_pendingResult": {}}');
        $result = new GetPendingResult($data);
        $this->assertSame([], $result->uuids());
        $this->assertSame('', $result->error());
    }
}
