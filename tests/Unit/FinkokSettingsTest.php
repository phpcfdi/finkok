<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\Exceptions\InvalidArgumentException;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class FinkokSettingsTest extends TestCase
{
    public function testConstructWithUsernameAndPassword(): void
    {
        $settings = new FinkokSettings('username', 'password');
        $this->assertSame('username', $settings->username());
        $this->assertSame('password', $settings->password());
    }

    public function testConstructWithEmptyUsernameThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid username');
        new FinkokSettings('', '');
    }

    public function testConstructWithEmptyPasswordThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid password');
        new FinkokSettings('foo', '');
    }

    public function testContainsDefaultEnvironmentAsTesting(): void
    {
        $settings = new FinkokSettings('u', 'p');
        $this->assertTrue($settings->environment()->isDevelopment());
    }

    public function testConstructCanHaveEnvironment(): void
    {
        $environment = FinkokEnvironment::makeProduction();
        $settings = new FinkokSettings('u', 'p', $environment);
        $this->assertTrue($settings->environment()->isProduction());
    }

    public function testCreateCallerForServiceUsesDefaultUsernameAndPassword(): void
    {
        $settings = new FinkokSettings('u', 'p');
        $settings->changeSoapFactory(new FakeSoapFactory());
        $service = $settings->createCallerForService(Services::stamping());
        $this->assertSame([
            'username' => 'u',
            'password' => 'p',
        ], $service->extraParameters());
    }

    public function testCreateCallerForServiceChangingUsernameAndPassword(): void
    {
        $settings = new FinkokSettings('u', 'p');
        $settings->changeSoapFactory(new FakeSoapFactory());
        $service = $settings->createCallerForService(Services::stamping(), 'x-u', 'x-p');
        $this->assertSame([
            'x-u' => 'u',
            'x-p' => 'p',
        ], $service->extraParameters());
    }

    public function testCreateCallerForServiceChangingToEmptyUsernameAndPassword(): void
    {
        $settings = new FinkokSettings('u', 'p');
        $settings->changeSoapFactory(new FakeSoapFactory());
        $service = $settings->createCallerForService(Services::stamping(), '', '');
        $this->assertSame([], $service->extraParameters());
    }
}
