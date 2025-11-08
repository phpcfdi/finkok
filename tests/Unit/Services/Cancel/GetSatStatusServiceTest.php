<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class GetSatStatusServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('cancel-get-sat-status-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new GetSatStatusService($settings);

        $command = new GetSatStatusCommand('x-rfc-issuer', 'x-rfc-recipient', 'x-uuid', 'x-total');
        $result = $service->query($command);
        $this->assertSame('S - Comprobante obtenido satisfactoriamente.', $result->query());
        $this->assertSame('Vigente', $result->cfdi());
        $this->assertSame('Cancelable sin aceptación', $result->cancellable());
        $this->assertSame('En proceso', $result->cancellation());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get_sat_status', $caller->latestCallMethodName);
        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfcIssuer(), $caller->latestCallParameters['taxpayer_id']);
        $this->assertArrayHasKey('rtaxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfcRecipient(), $caller->latestCallParameters['rtaxpayer_id']);
        $this->assertArrayHasKey('uuid', $caller->latestCallParameters);
        $this->assertSame($command->uuid(), $caller->latestCallParameters['uuid']);
        $this->assertArrayHasKey('total', $caller->latestCallParameters);
        $this->assertSame($command->total(), $caller->latestCallParameters['total']);
    }
}
