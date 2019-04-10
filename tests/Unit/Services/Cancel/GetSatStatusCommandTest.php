<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class GetSatStatusCommandTest extends TestCase
{
    public function testCommandValues(): void
    {
        $stamping = new GetSatStatusCommand('emisor', 'receptor', 'uuid', 'total');
        $this->assertSame('emisor', $stamping->rfcIssuer());
        $this->assertSame('receptor', $stamping->rfcRecipient());
        $this->assertSame('uuid', $stamping->uuid());
        $this->assertSame('total', $stamping->total());
    }
}
