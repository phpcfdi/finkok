<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\Tests\TestCase;

class FinkokEnvironmentTest extends TestCase
{
    public function testConstructDevelopment(): void
    {
        $environment = FinkokEnvironment::makeDevelopment();
        $this->assertFalse($environment->isProduction(), 'Must return false for production');
        $this->assertTrue($environment->isDevelopment(), 'Must return true for development');
    }

    public function testConstructProduction(): void
    {
        $environment = FinkokEnvironment::makeProduction();
        $this->assertFalse($environment->isDevelopment(), 'Must return false for development');
        $this->assertTrue($environment->isProduction(), 'Must return true for production');
    }
}
