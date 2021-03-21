<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\AcceptRejectSignatureResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class AcceptRejectSignatureResultTest extends TestCase
{
    public function testResultUsingPredefinedResponse(): void
    {
        $data = json_decode($this->fileContentPath('cancel-accept-reject-signature-response.json'));
        $result = new AcceptRejectSignatureResult($data);

        $uuids = $result->uuids();
        $this->assertCount(2, $uuids);

        $uuidAccept = $uuids->findByUuidOrFail('12345678-1234-1234-1234-000000000001');
        $this->assertSame('12345678-1234-1234-1234-000000000001', $uuidAccept->uuid());
        $this->assertSame('1001', $uuidAccept->status()->getCode());
        $this->assertTrue($uuidAccept->answer()->isAccept());

        $uuidReject = $uuids->findByUuidOrFail('12345678-1234-1234-1234-000000000002');
        $this->assertSame('12345678-1234-1234-1234-000000000002', $uuidReject->uuid());
        $this->assertSame('1002', $uuidReject->status()->getCode());
        $this->assertTrue($uuidReject->answer()->isReject());

        $this->assertSame('predefined-error', $result->error());
    }
}
