<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Retentions;

use PhpCfdi\Finkok\Services\Retentions\StampCommand;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdiRetention;
use PhpCfdi\Finkok\Tests\TestCase;

final class StampCommandTest extends TestCase
{
    public function testSignStampCommandCanReceiveAPrecfdi(): void
    {
        $xml = (new RandomPreCfdiRetention())->createValid();
        $command = new StampCommand($xml);
        $this->assertSame($xml, $command->xml());
    }
}
