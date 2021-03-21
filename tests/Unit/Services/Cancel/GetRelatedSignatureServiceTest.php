<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Cancel;

use PhpCfdi\Finkok\Services\Cancel\GetRelatedSignatureCommand;
use PhpCfdi\Finkok\Services\Cancel\GetRelatedSignatureService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class GetRelatedSignatureServiceTest extends TestCase
{
    public function testServiceUsingPreparedResult(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('cancel-get-related-signature-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new GetRelatedSignatureService($settings);

        $command = new GetRelatedSignatureCommand('x-xml');
        $result = $service->getRelatedSignature($command);

        $this->assertCount(2, $result->parents());
        $this->assertCount(2, $result->children());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('get_related_signature', $caller->latestCallMethodName);
        $this->assertArrayHasKey('xml', $caller->latestCallParameters);
        $this->assertSame($command->xml(), $caller->latestCallParameters['xml']);
    }
}
