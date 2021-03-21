<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\ReportUuidResult;
use PhpCfdi\Finkok\Tests\TestCase;

final class ReportUuidResultTest extends TestCase
{
    public function testResultUsingPredefinedResponse(): void
    {
        $data = json_decode($this->fileContentPath('utilities-report-uuid-response.json'));
        $result = new ReportUuidResult($data);

        $this->assertCount(2, $result->items());
        $obtained = [];
        foreach ($result->items() as $item) {
            $obtained[] = [
                'date' => $item['date'],
                'uuid' => $item['uuid'],
            ];
        }

        $expected = [
            ['date' => '2019-01-13T14:15:16', 'uuid' => '12345678-1234-1234-1234-000000000001'],
            ['date' => '2019-01-13T14:15:17', 'uuid' => '12345678-1234-1234-1234-000000000002'],
        ];

        $this->assertSame($expected, $obtained);
    }
}
