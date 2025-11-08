<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\DatetimeCommand;
use PhpCfdi\Finkok\Services\Utilities\DatetimeService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class DatetimeServiceTest extends TestCase
{
    public function testDatetimeServiceUsingPreparedResultWithEmptyPostalCode(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('utilities-datetime-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new DatetimeService($settings);

        $result = $service->datetime(new DatetimeCommand(''));
        $this->assertSame('2019-01-13T14:15:16', $result->datetime());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('datetime', $caller->latestCallMethodName);
        $this->assertArrayNotHasKey('zipcode', $caller->latestCallParameters);
    }

    public function testDatetimeServiceUsingPreparedResultWithPostalCode(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('utilities-datetime-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new DatetimeService($settings);
        $command = new DatetimeCommand('86000');

        $service->datetime($command);

        $caller = $soapFactory->latestSoapCaller;
        $this->assertArrayHasKey('zipcode', $caller->latestCallParameters);
        $this->assertSame('86000', $caller->latestCallParameters['zipcode']);
    }
}
