<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingAlert;
use PhpCfdi\Finkok\Tests\TestCase;

class StampingAlertTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $alert = new StampingAlert((object) []);
        $this->assertSame([], $alert->values());
    }

    public function testCreateWithSampleData(): void
    {
        $data = [
            'IdIncidencia' => 'IdIncidencia',
            'Uuid' => 'Uuid',
            'CodigoError' => 'CodigoError',
            'WorkProcessId' => 'WorkProcessId',
            'MensajeIncidencia' => 'MensajeIncidencia',
            'RfcEmisor' => 'RfcEmisor',
            'NoCertificadoPac' => 'NoCertificadoPac',
            'FechaRegistro' => 'FechaRegistro',
        ];
        $alert = new StampingAlert((object) $data);
        $this->assertSame('IdIncidencia', $alert->id());
        $this->assertSame('Uuid', $alert->uuid());
        $this->assertSame('CodigoError', $alert->errorCode());
        $this->assertSame('WorkProcessId', $alert->workProcessId());
        $this->assertSame('MensajeIncidencia', $alert->message());
        $this->assertSame('RfcEmisor', $alert->rfc());
        $this->assertSame('NoCertificadoPac', $alert->certificatePac());
        $this->assertSame('FechaRegistro', $alert->date());
        $this->assertSame($data, $alert->values());
    }
}
