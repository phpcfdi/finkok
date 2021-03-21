<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\DownloadXmlCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class DownloadXmlCommandTest extends TestCase
{
    public function testDownloadXmlCommandCanReceiveAPrecfdi(): void
    {
        $stamping = new DownloadXmlCommand('x-uuid', 'x-rfc', 'x-type');
        $this->assertSame('x-uuid', $stamping->uuid());
        $this->assertSame('x-rfc', $stamping->rfc());
        $this->assertSame('x-type', $stamping->type());
    }
}
