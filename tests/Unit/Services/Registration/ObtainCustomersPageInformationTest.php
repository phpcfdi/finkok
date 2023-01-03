<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\PageInformation;
use PhpCfdi\Finkok\Tests\TestCase;

final class ObtainCustomersPageInformationTest extends TestCase
{
    public function testCreateFromMessage(): void
    {
        $message = 'Showing 51 to 100 of 151 entries';
        $info = PageInformation::fromMessage($message);
        $this->assertSame(51, $info->firstRecord());
        $this->assertSame(100, $info->lastRecord());
        $this->assertSame(151, $info->totalRecords());
        $this->assertSame(50, $info->pageLength());
        $this->assertSame(2, $info->currentPage());
        $this->assertSame(4, $info->totalPages());
    }
}
