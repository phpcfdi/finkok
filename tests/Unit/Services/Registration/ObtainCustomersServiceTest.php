<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainCustomersCommand;
use PhpCfdi\Finkok\Services\Registration\ObtainCustomersService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class ObtainCustomersServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('registration-customers-response-2-items.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new ObtainCustomersService($settings);

        $page = 2;

        $command = new ObtainCustomersCommand($page);
        $service->obtainPage($command);

        $caller = $soapFactory->latestSoapCaller;
        $this->assertArrayHasKey('page', $caller->latestCallParameters);
        $this->assertSame($page, $caller->latestCallParameters['page']);
    }
}
