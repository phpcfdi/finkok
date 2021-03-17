<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Services\Cancel\AcceptRejectUuidItem;
use PhpCfdi\Finkok\Services\Cancel\AcceptRejectUuidStatus;
use PhpCfdi\Finkok\Tests\TestCase;

final class AcceptRejectUuidItemTest extends TestCase
{
    public function testCreateAndGetProperties(): void
    {
        $uuid = '12345678-1234-1234-1234-000000000001';
        $status = new AcceptRejectUuidStatus('1000');
        $answer = CancelAnswer::accept();
        $item = new AcceptRejectUuidItem($uuid, $status, $answer);
        $this->assertSame($uuid, $item->uuid());
        $this->assertSame($status, $item->status());
        $this->assertSame($answer, $item->answer());
    }
}
