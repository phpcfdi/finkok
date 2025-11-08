<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Registration;

use PhpCfdi\Finkok\Services\Registration\ObtainCommand;
use PhpCfdi\Finkok\Services\Registration\ObtainService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class ObtainServiceTest extends TestCase
{
    public function testServiceUsingPreparedResultWithRfc(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode($this->fileContentPath('registration-get-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new ObtainService($settings);

        $command = new ObtainCommand('MAG041126GT8');
        $result = $service->obtain($command);
        $this->assertSame('predefined-message', $result->message());
        $this->assertCount(1, $result->customers());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get', $caller->latestCallMethodName);

        $this->assertArrayHasKey('taxpayer_id', $caller->latestCallParameters);
        $this->assertSame($command->rfc(), $caller->latestCallParameters['taxpayer_id']);
    }
}
