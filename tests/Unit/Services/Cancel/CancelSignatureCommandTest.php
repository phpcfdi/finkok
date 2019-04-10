<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Definitions\CancelStorePending;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class CancelSignatureCommandTest extends TestCase
{
    public function testCommandDefaultValue(): void
    {
        $stamping = new CancelSignatureCommand('xml');
        $this->assertFalse($stamping->storePending()->asBool(), 'Default value for store pending should be FALSE');
    }

    public function testCommandValues(): void
    {
        $storePending = CancelStorePending::yes();
        $stamping = new CancelSignatureCommand('xml', $storePending);
        $this->assertSame('xml', $stamping->xml());
        $this->assertEquals($storePending, $stamping->storePending());
    }
}
