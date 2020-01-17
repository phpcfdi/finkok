<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Services\Retentions\StampedCommand;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdiRetention;
use PhpCfdi\Finkok\Tests\TestCase;

class StampedCommandTest extends TestCase
{
    public function testSignStampedCommandCanReceiveAPrecfdi(): void
    {
        $xml = (new RandomPreCfdiRetention())->createValid();
        $command = new StampedCommand($xml);
        $this->assertSame($xml, $command->xml());
    }
}
