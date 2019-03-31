<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingAlerts;
use PhpCfdi\Finkok\Tests\TestCase;

class StampingAlertsTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $alerts = new StampingAlerts();
        $this->assertCount(0, $alerts);
    }

    public function testHydrate(): void
    {
        $alerts = new StampingAlerts();
        $alerts->hydrate([
            (object) [
                'IdIncidencia' => '1',
                'Uuid' => 'Uuid',
                'CodigoError' => 'CodigoError',
                'WorkProcessId' => 'WorkProcessId',
                'MensajeIncidencia' => 'MensajeIncidencia',
                'RfcEmisor' => 'RfcEmisor',
                'NoCertificadoPac' => 'NoCertificadoPac',
                'FechaRegistro' => 'FechaRegistro',
            ],
            (object) [
                'IdIncidencia' => '2',
                'Uuid' => 'Uuid',
                'CodigoError' => 'CodigoError',
                'WorkProcessId' => 'WorkProcessId',
                'MensajeIncidencia' => 'MensajeIncidencia',
                'RfcEmisor' => 'RfcEmisor',
                'NoCertificadoPac' => 'NoCertificadoPac',
                'FechaRegistro' => 'FechaRegistro',
            ],
        ]);

        $this->assertCount(2, $alerts);
        $expectedId = 1;
        foreach ($alerts as $alert) {
            $this->assertSame(strval($expectedId), $alert->id());
            $expectedId = $expectedId + 1;
        }
    }
}
