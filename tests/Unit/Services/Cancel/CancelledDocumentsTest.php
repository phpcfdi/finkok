<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\CancelledDocuments;
use PhpCfdi\Finkok\Tests\TestCase;

class CancelledDocumentsTest extends TestCase
{
    protected function createCancelledDocuments(): CancelledDocuments
    {
        $input = [
            (object)[
                'UUID' => $this->createTestingUuid(1),
                'EstatusUUID' => '201',
                'EstatusCancelacion' => 'Cancelado sin aceptación',
            ],
            (object)[
                'UUID' => $this->createTestingUuid(2),
                'EstatusUUID' => '201',
                'EstatusCancelacion' => 'Cancelado sin aceptación',
            ],
        ];

        $documents = new CancelledDocuments($input);
        return $documents;
    }

    protected function createTestingUuid(int $index): string
    {
        return sprintf('11111111-2222-3333-4444-%012d', $index);
    }

    public function testCreateEmpty(): void
    {
        $documents = new CancelledDocuments([]);
        $this->assertCount(0, $documents);
    }

    public function testCreateWithCollection(): void
    {
        $documents = $this->createCancelledDocuments();
        $this->assertCount(2, $documents);
        $index = 1;
        foreach ($documents as $document) {
            $expectedUuid = $this->createTestingUuid($index);
            $this->assertSame($expectedUuid, $document->uuid());
            $index = $index + 1;
        }
    }

    public function testGetWithExistent(): void
    {
        $documents = $this->createCancelledDocuments();
        $this->assertSame($this->createTestingUuid(1), $documents->get(0)->uuid());
        $this->assertSame($this->createTestingUuid(2), $documents->get(1)->uuid());
    }

    public function testFirst(): void
    {
        $documents = $this->createCancelledDocuments();
        $this->assertSame($this->createTestingUuid(1), $documents->first()->uuid());
    }

    public function testFind(): void
    {
        $documents = $this->createCancelledDocuments();

        $this->assertNull($documents->find('foo'));

        $search = $this->createTestingUuid(1);
        $document = $documents->find($search);
        if (null === $document) {
            $this->fail(sprintf('The expected UUID %s was not found', $search));
        }
        $this->assertSame($search, $document->uuid());
    }
}
