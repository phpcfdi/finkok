<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class GetSatStatusCommandTest extends TestCase
{
    public function testCommandValues(): void
    {
        $command = new GetSatStatusCommand('emisor', 'receptor', 'uuid', 'total');
        $this->assertSame('emisor', $command->rfcIssuer());
        $this->assertSame('receptor', $command->rfcRecipient());
        $this->assertSame('uuid', $command->uuid());
        $this->assertSame('total', $command->total());
    }
}
