<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\Tests\TestCase;

class FinkokTest extends TestCase
{
    public function testFinkokConstruct(): void
    {
        new Finkok();
        $this->assertTrue(true, 'No exceptions!');
    }
}
