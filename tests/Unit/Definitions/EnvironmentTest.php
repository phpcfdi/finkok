<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Definitions;

use PhpCfdi\Finkok\Definitions\Environment;
use PhpCfdi\Finkok\Tests\TestCase;

class EnvironmentTest extends TestCase
{
    public function testContainsDevelopmentValue(): void
    {
        $env = Environment::development();
        $this->assertSame('https://demo-facturacion.finkok.com', $env->value());
    }

    public function testContainsProductionValue(): void
    {
        $env = Environment::production();
        $this->assertSame('https://facturacion.finkok.com', $env->value());
    }
}
