<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\ReportTotalResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class ReportTotalResultTest extends TestCase
{
    public function testResultUsingPredefinedResponse(): void
    {
        $data = json_decode($this->fileContentPath('utilities-report-total-response.json'));
        $result = new ReportTotalResult($data);

        $this->assertSame('EKU9003173C9', $result->rfc());
        $this->assertSame('123', $result->total());
    }

    public function testResultUsingEmptyResponse(): void
    {
        $data = json_decode('{"report_totalResult": {}}');
        $result = new ReportTotalResult($data);
        $this->assertSame('', $result->rfc());
        $this->assertSame('', $result->total());
    }
}
