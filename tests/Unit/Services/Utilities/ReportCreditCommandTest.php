<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\ReportCreditCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class ReportCreditCommandTest extends TestCase
{
    public function testReportCreditCommandCreation(): void
    {
        $command = new ReportCreditCommand('x-rfc');
        $this->assertSame('x-rfc', $command->rfc());
    }
}
