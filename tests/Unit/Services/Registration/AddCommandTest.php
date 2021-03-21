<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\AddCommand;
use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Finkok\Tests\TestCase;

final class AddCommandTest extends TestCase
{
    public function testAddCommandCreation(): void
    {
        $rfc = 'x-rfc';
        $type = CustomerType::prepaid();
        $cerfile = 'x-certificate';
        $keyfile = 'x-key';
        $passPhrase = 'qwerty';
        $command = new AddCommand($rfc, $type, $cerfile, $keyfile, $passPhrase);
        $this->assertSame($rfc, $command->rfc());
        $this->assertSame($type, $command->type());
        $this->assertSame($cerfile, $command->certificate());
        $this->assertSame($keyfile, $command->privateKey());
        $this->assertSame($passPhrase, $command->passPhrase());
    }

    public function testAddCommandCreationWithMinimalData(): void
    {
        $command = new AddCommand('x-rfc');
        $this->assertSame('x-rfc', $command->rfc());
        $this->assertTrue($command->type()->isOndemand());
        $this->assertSame('', $command->certificate());
        $this->assertSame('', $command->privateKey());
        $this->assertSame('', $command->passPhrase());
    }
}
