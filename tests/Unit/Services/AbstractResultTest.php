<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services;

use InvalidArgumentException;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class AbstractResultTest extends TestCase
{
    /** @var stdClass */
    private $data;

    /** @var TestingResult */
    private $result;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = (object)[
            'mean' => (object)[
                'foo' => 'x-foo',
                'bar' => (object)['aaa' => 'x-aaa', 'bbb' => 'x-bbb'],
                'baz' => [
                    (object) ['position' => 'zero'],
                    (object) ['position' => 'one'],
                    (object) ['position' => 'two'],
                ],
            ],
            'other' => 'x-other',
        ];
        $this->result = new TestingResult($this->data, 'mean');
    }

    public function testConstructUsingDeepMeanObject(): void
    {
        $result = new TestingResult($this->data, 'mean', 'bar');
        $this->assertSame('x-aaa', $result->exposeGet('aaa'));
    }

    public function testConstructWithoutMeanObjectPath(): void
    {
        $result = new TestingResult($this->data);
        $this->assertSame('x-other', $result->exposeGet('other'));
    }

    public function testConstructWithoutMeanObjectFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to find mean object at /other');
        /** @noinspection PhpExpressionResultUnusedInspection */
        new TestingResult($this->data, 'other');
    }

    public function testRawData(): void
    {
        $this->assertEquals($this->data, $this->result->rawData());
    }

    public function testGetInsideRoot(): void
    {
        $this->assertSame('x-foo', $this->result->exposeGet('foo'));
    }

    public function testFindInsideObject(): void
    {
        $this->assertSame('x-aaa', $this->result->exposeFindInData('mean', 'bar', 'aaa'));
    }

    public function testFindInsideObjectNotFound(): void
    {
        $this->assertNull($this->result->exposeFindInData('mean', 'not-existent', 'children'));
    }

    public function testFindInsideArray(): void
    {
        $this->assertSame('one', $this->result->exposeFindInData('mean', 'baz', '1', 'position'));
    }

    public function testFindInsideArrayNotFound(): void
    {
        $this->assertNull($this->result->exposeFindInData('mean', 'baz', 'not-existent', 'children'));
    }

    public function testFindInsideNonObjectOrArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot find descendent on non-array non-object haystack');
        $this->result->exposeFindInData('mean', 'foo', 'unexpected-property');
    }
}
