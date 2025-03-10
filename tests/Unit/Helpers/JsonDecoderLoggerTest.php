<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Helpers;

use PhpCfdi\Finkok\Helpers\JsonDecoderLogger;
use PhpCfdi\Finkok\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\NullLogger;

final class JsonDecoderLoggerTest extends TestCase
{
    public function testSetAlsoLogJsonMessage(): void
    {
        $decoder = new JsonDecoderLogger(new NullLogger());
        $this->assertSame(false, $decoder->setAlsoLogJsonMessage(null));
        $this->assertSame(false, $decoder->setAlsoLogJsonMessage(true));
        $this->assertSame(true, $decoder->setAlsoLogJsonMessage(true));
        $this->assertSame(true, $decoder->setAlsoLogJsonMessage(null));
        $this->assertSame(true, $decoder->setAlsoLogJsonMessage(false));
        $this->assertSame(false, $decoder->setAlsoLogJsonMessage(false));
        $this->assertSame(false, $decoder->setAlsoLogJsonMessage(null));
    }

    public function testSetUseJsonValidateIfAvailable(): void
    {
        $decoder = new JsonDecoderLogger(new NullLogger());
        $this->assertSame(true, $decoder->setUseJsonValidateIfAvailable(null));
        $this->assertSame(true, $decoder->setUseJsonValidateIfAvailable(false));
        $this->assertSame(false, $decoder->setUseJsonValidateIfAvailable(false));
        $this->assertSame(false, $decoder->setUseJsonValidateIfAvailable(null));
        $this->assertSame(false, $decoder->setUseJsonValidateIfAvailable(true));
        $this->assertSame(true, $decoder->setUseJsonValidateIfAvailable(true));
        $this->assertSame(true, $decoder->setUseJsonValidateIfAvailable(null));
    }

    public function testLastMessageWasJsonValidReturnFalseWithoutCall(): void
    {
        $decoder = new JsonDecoderLogger(new NullLogger());
        $this->assertSame(false, $decoder->lastMessageWasJsonValid());
    }

    /** @return array<string, array{bool}> */
    public function providerUseJsonValidateIfAvailable(): array
    {
        return [
            'use json_validate' => [true],
            'do not use json_validate' => [false],
        ];
    }

    /** @dataProvider providerUseJsonValidateIfAvailable */
    public function testLogSendValidJsonMessageToLogger(bool $useJsonValidateIfAvailable): void
    {
        $jsonMessage = (string) json_encode(['foo' => 'bar']);
        $textMessage = print_r(json_decode($jsonMessage), true);
        /** @var NullLogger&MockObject $logger */
        $logger = $this->createMock(NullLogger::class);
        $logger->expects($this->once())->method('log')->with('debug', $textMessage, []);

        $decoder = new JsonDecoderLogger($logger);
        $decoder->setUseJsonValidateIfAvailable($useJsonValidateIfAvailable);
        $decoder->debug($jsonMessage);
        $this->assertTrue($decoder->lastMessageWasJsonValid());
    }

    /** @dataProvider providerUseJsonValidateIfAvailable */
    public function testLogSendInvalidJsonMessageToLogger(bool $useJsonValidateIfAvailable): void
    {
        $invalidJsonMessage = 'this is not a valid json message';
        $expectedMessage = $invalidJsonMessage;
        /** @var NullLogger&MockObject $logger */
        $logger = $this->createMock(NullLogger::class);
        $logger->expects($this->once())->method('log')->with('error', $expectedMessage, []);

        $decoder = new JsonDecoderLogger($logger);
        $decoder->setUseJsonValidateIfAvailable($useJsonValidateIfAvailable);
        $decoder->error($invalidJsonMessage);
        $this->assertFalse($decoder->lastMessageWasJsonValid());
    }

    public function testLogSendTextMessageToLoggerAndJson(): void
    {
        $jsonMessage = (string) json_encode(['foo' => 'bar']);
        $textMessage = print_r(json_decode($jsonMessage), true);
        /** @var NullLogger&MockObject $logger */
        $logger = $this->createMock(NullLogger::class);
        $expectedParameters = [
            $textMessage,
            $jsonMessage,
        ];
        $matcher = $this->exactly(count($expectedParameters));
        $logger->expects($matcher)->method('log')->with(
            'debug',
            $this->callback(
                function ($message) use ($matcher, $expectedParameters): bool {
                    $this->assertSame($expectedParameters[$matcher->getInvocationCount() - 1], $message);
                    return true;
                }
            ),
            []
        );

        $decoder = new JsonDecoderLogger($logger);
        $decoder->setAlsoLogJsonMessage(true);
        $decoder->debug($jsonMessage);
    }
}
