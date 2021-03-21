<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit\Services\Stamping;

use PhpCfdi\Finkok\Services\Stamping\QueryPendingCommand;
use PhpCfdi\Finkok\Services\Stamping\QueryPendingService;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;

final class QueryPendingServiceTest extends TestCase
{
    public function testCall(): void
    {
        $preparedResult = json_decode(TestCase::fileContentPath('querypending-response.json'));

        $soapFactory = new FakeSoapFactory();
        $soapFactory->preparedResult = $preparedResult;

        $settings = $this->createSettingsFromEnvironment($soapFactory);
        $service = new QueryPendingService($settings);
        $command = new QueryPendingCommand('uuid-search');

        $result = $service->queryPending($command);
        $this->assertSame('S', $result->status());

        $caller = $soapFactory->latestSoapCaller;
        $this->assertSame('query_pending', $caller->latestCallMethodName);
        $this->assertArrayHasKey('uuid', $caller->latestCallParameters);
        $this->assertSame('uuid-search', $caller->latestCallParameters['uuid']);
    }
}
