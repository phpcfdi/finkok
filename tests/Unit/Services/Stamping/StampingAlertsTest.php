<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingAlerts;
use PhpCfdi\Finkok\Tests\TestCase;

final class StampingAlertsTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $alerts = new StampingAlerts([]);
        $this->assertCount(0, $alerts);
    }

    public function testHydrate(): void
    {
        $input = [
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
        ];

        $alerts = new StampingAlerts($input);

        $this->assertCount(2, $alerts);
        $expectedId = 1;
        foreach ($alerts as $alert) {
            $this->assertSame(strval($expectedId), $alert->id());
            $expectedId = $expectedId + 1;
        }
    }

    public function testGettingAnAlertByIndex(): void
    {
        $data = json_decode($this->fileContentPath('stamp-response-with-alerts.json'));
        $alerts = new StampingAlerts($data->{'stampResult'}->{'Incidencias'}->{'Incidencia'} ?? []);
        $this->assertSame('FAKE2', $alerts->get(1)->errorCode());
    }

    public function testGettingANonExistentAlertByIndexReturnsEmptyAlert(): void
    {
        $alerts = new StampingAlerts([]);
        $this->assertCount(0, $alerts);
        $this->assertEmpty($alerts->get(100)->errorCode());
    }

    public function testGettingFirstAlertOnEmptyCollectionReturnsEmptyAlert(): void
    {
        $alerts = new StampingAlerts([]);
        $this->assertCount(0, $alerts);
        $this->assertEmpty($alerts->first()->errorCode());
    }

    public function testFindByErrorCode(): void
    {
        $alerts = new StampingAlerts([
            (object) ['CodigoError' => '000'],
        ]);
        $this->assertNull($alerts->findByErrorCode('0'));
        $this->assertNotNull($alerts->findByErrorCode('000'));
    }
}
