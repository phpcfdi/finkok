<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use BadMethodCallException;
use InvalidArgumentException;
use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\FinkokSettings;
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
}
