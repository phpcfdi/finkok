<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use PhpCfdi\Finkok\Exceptions\InvalidArgumentException;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Tests\TestCase;

class FinkokSettingsTest extends TestCase
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
        $this->assertSame([
            'username' => 'u',
            'password' => 'p',
        ], $settings->credentialsParameters());
    }

    public function testCreateCallerChangingUsernameAndPasswordKeys(): void
    {
        $settings = new FinkokSettings('u', 'p');
        $settings->changeUsernameKey('x-username');
        $settings->changePasswordKey('x-password');
        $this->assertSame([
            'x-username' => 'u',
            'x-password' => 'p',
        ], $settings->credentialsParameters());
    }
}
