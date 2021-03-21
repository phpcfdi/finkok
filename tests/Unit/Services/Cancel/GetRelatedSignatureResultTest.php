<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetRelatedSignatureResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetRelatedSignatureResultTest extends TestCase
{
    public function testResultUsingPredefinedResponse(): void
    {
        $data = json_decode($this->fileContentPath('cancel-get-related-signature-response.json'));
        $result = new GetRelatedSignatureResult($data);
        $parents = $result->parents();
        $firstParent = $parents->get(0);
        $this->assertCount(2, $parents);
        $this->assertSame('11111111-2222-3333-4444-000000000001', $firstParent->uuid());
        $this->assertSame('EKU9003173C9', $firstParent->rfcReceiver());
        $this->assertSame('LAN7008173R5', $firstParent->rfcEmitter());
        $this->assertSame('11111111-2222-3333-4444-000000000002', $parents->get(1)->uuid());

        $children = $result->children();
        $firstChildren = $children->get(0);
        $this->assertCount(2, $children);
        $this->assertSame('11111111-2222-3333-4444-000000000003', $firstChildren->uuid());
        $this->assertSame('EKU9003173C9', $firstChildren->rfcReceiver());
        $this->assertSame('LAN7008173R5', $firstChildren->rfcEmitter());
        $this->assertSame('11111111-2222-3333-4444-000000000004', $children->get(1)->uuid());

        $this->assertSame('predefined-error', $result->error());
    }
}
