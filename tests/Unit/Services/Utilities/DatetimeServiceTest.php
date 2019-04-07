<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Utilities;

use PhpCfdi\Finkok\Services\Utilities\DatetimeService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

class DatetimeServiceTest extends TestCase
{
    public function testDatetimeServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('utilities-datetime-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new DatetimeService($settings);

        $result = $service->datetime();
        $this->assertSame('2019-01-13T14:15:16', $result->datetime());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('datetime', $caller->latestCallMethodName);
    }
}
