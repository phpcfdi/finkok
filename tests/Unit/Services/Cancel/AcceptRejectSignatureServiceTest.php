<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\AcceptRejectSignatureCommand;
use PhpCfdi\Finkok\Services\Cancel\AcceptRejectSignatureService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use stdClass;

final class AcceptRejectSignatureServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        /** @var stdClass $preparedResult */
        $preparedResult = json_decode(TestCase::fileContentPath('cancel-accept-reject-signature-response.json'));
        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;
        $settings = $this->createSettingsFromEnvironment($soapFactory);

        $service = new AcceptRejectSignatureService($settings);
        $command = new AcceptRejectSignatureCommand('x-xml');
        $result = $service->acceptRejectSignature($command);

        // test call
        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('accept_reject_signature', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame($command->xml(), $caller->latestCallParameters['xml']);

        // test response was made based on given object
        $this->assertSame('predefined-error', $result->error());
    }
}
