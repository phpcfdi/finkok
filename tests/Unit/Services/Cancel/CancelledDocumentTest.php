<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\CancelledDocument;
use PhpCfdi\Finkok\Tests\TestCase;

final class CancelledDocumentTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $folio = new CancelledDocument((object) []);
        $this->assertSame([], $folio->values());
    }

    public function testCreateWithSampleData(): void
    {
        $data = [
            'UUID' => '728147B1-D5B9-4FDD-AEA9-526AEA2E6698',
            'EstatusUUID' => '708',
            'EstatusCancelacion' => 'foo bar',
        ];
        $folio = new CancelledDocument((object) $data);
        $this->assertSame('728147B1-D5B9-4FDD-AEA9-526AEA2E6698', $folio->uuid());
        $this->assertSame('708', $folio->documentStatus());
        $this->assertSame('foo bar', $folio->cancellationStatus());
        $this->assertSame($data, $folio->values());
    }
}
