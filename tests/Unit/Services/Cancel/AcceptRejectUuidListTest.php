<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use OutOfRangeException;
use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Services\Cancel\AcceptRejectUuidList;
use PhpCfdi\Finkok\Tests\TestCase;

final class AcceptRejectUuidListTest extends TestCase
{
    /** @var AcceptRejectUuidList */
    private $list;

    protected function setUp(): void
    {
        parent::setUp();
        $source = json_decode(json_encode([
            ['Rechaza' => ['uuid' => '12345678-AAAA-1234-1234-000000000001', 'status' => '1000']],
            ['Rechaza' => ['uuid' => '12345678-AAAA-1234-1234-000000000002', 'status' => '1001']],
            ['Acepta' => ['uuid' => '12345678-AAAA-1234-1234-000000000003', 'status' => '1000']],
        ]) ?: '');
        $this->list = new AcceptRejectUuidList($source);
    }

    public function testAllElementsHaveTheSameCancelAnswer(): void
    {
        $expected = [
            CancelAnswer::reject(),
            CancelAnswer::reject(),
            CancelAnswer::accept(),
        ];
        foreach ($this->list as $index => $item) {
            $this->assertEquals($expected[$index], $item->answer());
        }
    }

    public function testCount(): void
    {
        $this->assertCount(3, $this->list);
    }

    public function testFindGetExpected(): void
    {
        $search = '12345678-AAAA-1234-1234-000000000002';
        $found = $this->list->findByUuid($search);
        if (null === $found) {
            $this->fail('Expected UUID was not found');
        }
        $this->assertSame($search, $found->uuid());
        $this->assertSame('1001', $found->status()->getCode());
    }

    public function testFindOrFailGetExpected(): void
    {
        $search = '12345678-AAAA-1234-1234-000000000002';
        $found = $this->list->findByUuidOrFail($search);
        $this->assertSame($search, $found->uuid());
        $this->assertSame('1001', $found->status()->getCode());
    }

    public function testFindReturnsNullOnNotFound(): void
    {
        $this->assertNull($this->list->findByUuid('12345678-AAAA-1234-1234-000000000000'));
    }

    public function testFindOrFailThrowException(): void
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage('UUID 12345678-AAAA-1234-1234-000000000000 not found');
        $this->list->findByUuidOrFail('12345678-AAAA-1234-1234-000000000000');
    }

    public function testConstructUsingInvalidSource(): void
    {
        $source = json_decode(json_encode([
            ['Foo' => ['uuid' => '12345678-AAAA-1234-1234-000000000000', 'status' => '1001']],
            ['Rechaza' => ['status' => '1000']],
            ['Acepta' => ['uuid' => '12345678-AAAA-1234-1234-000000000002']],
        ]) ?: '');
        $list = new AcceptRejectUuidList($source);

        $this->assertCount(3, $list);
        $invalidAnswerType = $list->get(0);
        $this->assertTrue($invalidAnswerType->answer()->isAccept());
        $this->assertSame('', $invalidAnswerType->uuid());
        $this->assertTrue($invalidAnswerType->status()->isUndefined());

        $withoutUuid = $list->get(1);
        $this->assertTrue($withoutUuid->answer()->isReject());
        $this->assertSame('', $withoutUuid->uuid());
        $this->assertSame('1000', $withoutUuid->status()->getCode());

        $withoutStatus = $list->get(2);
        $this->assertTrue($withoutStatus->answer()->isAccept());
        $this->assertSame('12345678-AAAA-1234-1234-000000000002', $withoutStatus->uuid());
        $this->assertTrue($withoutStatus->status()->isUndefined());
    }
}
