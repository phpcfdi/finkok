<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\CustomerStatus;
use PhpCfdi\Finkok\Services\Registration\EditCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class EditCommandTest extends TestCase
{
    public function testEditCommandCreation(): void
    {
        $rfc = 'x-rfc';
        $status = CustomerStatus::suspended();
        $cerfile = 'x-certificate';
        $keyfile = 'x-key';
        $passPhrase = 'qwerty';
        $command = new EditCommand($rfc, $status, $cerfile, $keyfile, $passPhrase);
        $this->assertSame($rfc, $command->rfc());
        $this->assertSame($status, $command->status());
        $this->assertSame($cerfile, $command->certificate());
        $this->assertSame($keyfile, $command->privateKey());
        $this->assertSame($passPhrase, $command->passPhrase());
    }

    public function testEditCommandCreationWithMinimalArguments(): void
    {
        $rfc = 'x-rfc';
        $status = CustomerStatus::suspended();
        $command = new EditCommand($rfc, $status);
        $this->assertSame($rfc, $command->rfc());
        $this->assertSame($status, $command->status());
        $this->assertSame('', $command->certificate());
        $this->assertSame('', $command->privateKey());
        $this->assertSame('', $command->passPhrase());
    }
}
