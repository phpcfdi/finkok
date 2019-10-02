<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\AcceptRejectSignatureCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class AcceptRejectSignatureCommandTest extends TestCase
{
    public function testCommandValues(): void
    {
        $command = new AcceptRejectSignatureCommand('x-xml');
        $this->assertSame('x-xml', $command->xml());
    }
}
