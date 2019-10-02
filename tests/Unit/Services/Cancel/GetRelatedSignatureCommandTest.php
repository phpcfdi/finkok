<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetRelatedSignatureCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class GetRelatedSignatureCommandTest extends TestCase
{
    public function testCommandValues(): void
    {
        $command = new GetRelatedSignatureCommand('x-xml');
        $this->assertSame('x-xml', $command->xml());
    }
}
