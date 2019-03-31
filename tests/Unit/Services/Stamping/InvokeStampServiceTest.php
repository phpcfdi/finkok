<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\SoapCaller;
use PhpCfdi\Finkok\Tests\Factories\RandomPreCfdi;
use PhpCfdi\Finkok\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class InvokeStampServiceTest extends TestCase
{
    public function testInvokeExpectingFailure(): void
    {
        $settings = $this->createSettingsFromEnvironment();
        $xml = (new RandomPreCfdi())->createInvalidByDate();

        $service = new StampService($settings);
        $command = new StampingCommand($xml);
        $result = $service->stamp($command);
        $this->assertGreaterThan(0, $result->alerts()->count());
        $this->assertSame('Fecha y hora de generación fuera de rango', $result->alerts()->first()->message());
    }

    public function testInvokeNotContactingServer(): void
    {
        /** @var SoapCaller&MockObject $service */
        $preparedResult = json_decode(TestCase::fileContentPath('stamp-response-with-alerts.json'));
        $soapCaller = $this->createMock(SoapCaller::class);
        $soapCaller->method('call')->willReturn($preparedResult);

        /** @var StampService&MockObject $service */
        $service = $this->getMockBuilder(StampService::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['stamp']) // do not mock stamp
            ->setMethods(['createSoapCaller']) // include protected
            ->getMock();
        $service->method('createSoapCaller')->willReturn($soapCaller);

        $command = new StampingCommand('foo');
        $result = $service->stamp($command);
        $this->assertCount(2, $result->alerts());
        $this->assertSame('Error falso 1', $result->alerts()->first()->message());
    }
}
