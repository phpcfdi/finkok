<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration;

use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\Services\Utilities\DatetimeCommand;

final class FinkokTest extends IntegrationTestCase
{
    public function testCallingDateTime(): void
    {
        $finkok = new Finkok($this->createSettingsFromEnvironment());
        $result = $finkok->datetime(new DatetimeCommand(''));
        $this->assertSame('', $result->error(), 'Is Finkok down? Are you using valid testing credentials?');
        $this->assertStringMatchesFormat('%d-%d-%dT%d:%d:%d', $result->datetime());
    }
}
