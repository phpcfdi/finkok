<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\DownloadXmlResult;

use PhpCfdi\Finkok\Tests\TestCase;

class DownloadXmlResultTest extends TestCase
{
    public function testResultUsingPredefinedResponses(): void
    {
        $data = json_decode($this->fileContentPath('utilities-getxml-response.json'));
        $result = new DownloadXmlResult($data);
        $this->assertSame('predefined-xml', $result->xml());
        $this->assertSame('predefined-error', $result->error());
    }
}
