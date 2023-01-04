<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainCustomersResult;
use PhpCfdi\Finkok\Services\Registration\PageInformation;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class ObtainCustomersResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        /** @var stdClass $data */
        $data = json_decode($this->fileContentPath('registration-customers-response-2-items.json'));
        $result = new ObtainCustomersResult($data);
        $this->assertSame('Showing 1 to 50 of 2 entries', $result->message());
        $this->assertEquals(PageInformation::fromValues(1, 50, 2), $result->pagesInformation());
        $this->assertCount(2, $result->customers());
    }

    public function testPagesInformation(): void
    {
        $message = 'Showing 51 to 100 of 151 entries';
        $expectedInformation = PageInformation::fromValues(51, 100, 151);
        $result = new ObtainCustomersResult((object) ['customersResult' => (object) ['message' => $message]]);
        $this->assertEquals($expectedInformation, $result->pagesInformation());
    }
}
