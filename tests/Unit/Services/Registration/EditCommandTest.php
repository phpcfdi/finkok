<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\EditCommand;
use PhpCfdi\Finkok\Tests\TestCase;

class EditCommandTest extends TestCase
{
    public function testEditCommandCreation(): void
    {
        $rfc = 'x-rfc';
        $status = 'S';
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
}
