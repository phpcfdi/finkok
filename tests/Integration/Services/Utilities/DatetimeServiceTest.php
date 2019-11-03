<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Utilities;

use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\QuickFinkok;
use PhpCfdi\Finkok\Services\Utilities\DatetimeService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;

class DatetimeServiceTest extends IntegrationTestCase
{
    public function testTwoWellKnownDifferentPostalCodes(): void
    {
        $postalCodeCancun = '77500';
        $postalCodeTijuana = '22000';
        $settings = $this->createSettingsFromEnvironment();
        $finkok = new QuickFinkok($settings);
        $tsCancun = strtotime($finkok->serversDateTime($postalCodeCancun)->datetime());
        $tsTijuana = strtotime($finkok->serversDateTime($postalCodeTijuana)->datetime());
        $this->assertGreaterThanOrEqual(
            3600,
            $tsCancun - $tsTijuana + 1,
            'Cancún and Tijuana must always have a different time at least for 1 hour'
        );
    }

    public function testConsumeDateTimeService(): void
    {
        $settings = $this->createSettingsFromEnvironment();
        $service = new DatetimeService($settings);
        $result = $service->datetime();

        $this->assertRegExp('/^[\d:T\-]{19}$/', $result->datetime());
        /** @var int|false $converted */
        $converted = strtotime($result->datetime());
        if (false === $converted) {
            $this->fail(sprintf('Cannot convert %s to a php timestamp', $result->datetime()));
            return;
        }

        $margin = 1;
        $currentTime = time();
        $this->assertThat($converted, $this->logicalAnd(
            $this->greaterThanOrEqual($currentTime - $margin),
            $this->lessThanOrEqual($currentTime + $margin)
        ), sprintf('Finkok date %s is not %s +/- %d second', date('c', $converted), date('c', $currentTime), $margin));
    }

    public function testConsumeDateTimeServiceUsingInvalidUsernamePassword(): void
    {
        $settings = new FinkokSettings(
            'foo-bar-baz',
            'foo-bar-baz',
            FinkokEnvironment::makeDevelopment()
        );
        $service = new DatetimeService($settings);
        $result = $service->datetime();
        $this->assertSame('Invalid username or password', $result->error());
    }
}
