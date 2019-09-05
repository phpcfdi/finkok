<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use BadMethodCallException;
use InvalidArgumentException;
use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Services\Manifest\GetContractsCommand;
use PhpCfdi\Finkok\Services\Manifest\GetContractsResult;
use PhpCfdi\Finkok\Services\Manifest\GetContractsService;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;
use PhpCfdi\Finkok\Services\Stamping\StampingResult;
use PhpCfdi\Finkok\Services\Stamping\StampService;
use PhpCfdi\Finkok\Services\Utilities\DatetimeResult;
use PhpCfdi\Finkok\Services\Utilities\DatetimeService;
use PhpCfdi\Finkok\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FinkokTest extends TestCase
{
    public function testConstructor(): void
    {
        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);
        $finkok = new Finkok($settings);
        $this->assertSame($settings, $finkok->settings());
    }

    public function testInvokingOneMappedMagicMethod(): void
    {
        /** @var StampingResult $result */
        $result = $this->createMock(StampingResult::class);

        /** @var StampingCommand $command */
        $command = $this->createMock(StampingCommand::class);

        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);

        /** @var Finkok&MockObject $finkok */
        $finkok = $this->getMockBuilder(Finkok::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$settings])
            ->onlyMethods(['executeService'])
            ->getMock();
        $finkok->expects($this->once())->method('executeService')->with(
            $this->equalTo('stamp'),
            $this->isInstanceOf(StampService::class),
            $this->isInstanceOf(StampingCommand::class)
        )->willReturn($result);

        $this->assertSame($result, $finkok->stamp($command));
    }

    public function testCheckCommandWithoutParameter(): void
    {
        // the only registered helper is datetime
        /** @var DatetimeResult $result */
        $result = $this->createMock(DatetimeResult::class);

        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);

        /** @var Finkok&MockObject $finkok */
        $finkok = $this->getMockBuilder(Finkok::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$settings])
            ->onlyMethods(['executeService'])
            ->getMock();
        $finkok->expects($this->once())->method('executeService')->with(
            $this->equalTo('datetime'),
            $this->isInstanceOf(DatetimeService::class),
            $this->isNull()
        )->willReturn($result);

        $this->assertSame($result, $finkok->datetime());
    }

    public function testBadMethodCall(): void
    {
        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);
        $finkok = new Finkok($settings);
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Helper invalid-method is not registered');
        $finkok->{'invalid-method'}();
    }

    public function testMagicCallWithInvalidParameter(): void
    {
        /** @var StampingCommand $result */
        $command = $this->createMock(StampingCommand::class);
        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);
        $finkok = new Finkok($settings);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Call PhpCfdi\Finkok\Finkok::getContracts'
            . ' expect PhpCfdi\Finkok\Services\Manifest\GetContractsCommand'
            . ' but received ' . get_class($command)
        );
        $finkok->{'getContracts'}($command);
    }

    public function testExecuteServiceWithCallNameDifferentFromServiceMethodName(): void
    {
        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);
        /** @var Finkok&MockObject $finkok */
        $finkok = new class($settings) extends Finkok {
            public function executeService(string $method, $service, $command)
            {
                $result = parent::executeService($method, $service, $command);
                return $result;
            }
        };

        /** @var MockObject $command */
        $command = $this->createMock(GetContractsCommand::class);
        /** @var MockObject $service */
        $service = $this->createMock(GetContractsService::class);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageRegExp('/The service \w+ does not have a method foo$/');
        $finkok->{'executeService'}('foo', $service, $command);
    }

    public function testInvokingOneMappedMagicMethodWithDifferentName(): void
    {
        /** @var GetContractsResult $result */
        $result = $this->createMock(GetContractsResult::class);
        /** @var GetContractsCommand $command */
        $command = $this->createMock(GetContractsCommand::class);
        /** @var GetContractsService $command */
        $service = $this->createMock(GetContractsService::class);
        $service->expects($this->once())->method('obtainContracts')->willReturn($result);

        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);
        /** @var Finkok&MockObject $finkok */
        $finkok = $this->getMockBuilder(Finkok::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$settings])
            ->onlyMethods(['createService'])
            ->getMock();
        $finkok->method('createService')->willReturn($service);

        // this test looks stupid, but means that it was able to override the method name to call on service
        $this->assertSame($result, $finkok->getContracts($command));
    }

    public function testServicesMapHaveCorrectNamedAttribute(): void
    {
        /** @var FinkokSettings&MockObject $settings */
        $settings = $this->createMock(FinkokSettings::class);
        $exposer = new class($settings) extends Finkok {
            public function exposeServicesMap(): array
            {
                return parent::SERVICES_MAP;
            }

            public function exposeCreateService(string $method)
            {
                return $this->createService($method);
            }
        };
        $servicesMap = $exposer->exposeServicesMap();
        foreach ($servicesMap as $methodName => $definition) {
            $finalMethodName = $definition[2] ?? $methodName;
            $service = $exposer->exposeCreateService($methodName);
            $this->assertTrue(is_callable([$service, $finalMethodName]));
        }
    }
}
