<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\AcceptRejectUuidStatus;
use PhpCfdi\Finkok\Tests\TestCase;

final class AcceptRejectUuidStatusTest extends TestCase
{
    public function testCreateSuccessStatus(): void
    {
        $status = new AcceptRejectUuidStatus(1000);
        $this->assertSame('1000', $status->getCode());
        $this->assertSame('Se recibió la respuesta de la petición de forma exitosa', $status->getMessage());
        $this->assertTrue($status->isSuccess());
    }

    public function testCreateFailureStatus(): void
    {
        $status = new AcceptRejectUuidStatus(1003);
        $this->assertSame('1003', $status->getCode());
        $this->assertSame('El sello no corresponde al RFC receptor', $status->getMessage());
        $this->assertFalse($status->isSuccess());
    }

    public function testCreateUndefinedStatus(): void
    {
        $status = new AcceptRejectUuidStatus(123456);
        $this->assertSame('123456', $status->getCode());
        $this->assertSame('Respuesta del SAT desconocida', $status->getMessage());
        $this->assertFalse($status->isSuccess());
    }
}
