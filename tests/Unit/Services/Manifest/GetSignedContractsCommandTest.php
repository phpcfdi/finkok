<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Manifest;

use PhpCfdi\Finkok\Definitions\SignedDocumentFormat;
use PhpCfdi\Finkok\Services\Manifest\GetSignedContractsCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class GetSignedContractsCommandTest extends TestCase
{
    public function testCreateCommand(): void
    {
        $formatXml = SignedDocumentFormat::xml();
        $command = new GetSignedContractsCommand('x-snid', 'x-rfc', $formatXml);
        $this->assertSame('x-snid', $command->snid());
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertSame($formatXml, $command->format());
    }
}
