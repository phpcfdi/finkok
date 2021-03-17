<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Definitions\CancelStorePending;
use PhpCfdi\Finkok\Services\Retentions\CancelSignatureCommand;
use PhpCfdi\Finkok\Tests\TestCase;

final class CancelSignatureCommandTest extends TestCase
{
    public function testCommandDefaultValue(): void
    {
        $command = new CancelSignatureCommand('xml');
        $this->assertFalse($command->storePending()->asBool(), 'Default value for store pending should be FALSE');
    }

    public function testCommandValues(): void
    {
        $storePending = CancelStorePending::yes();
        $command = new CancelSignatureCommand('xml', $storePending);
        $this->assertSame('xml', $command->xml());
        $this->assertEquals($storePending, $command->storePending());
    }
}
