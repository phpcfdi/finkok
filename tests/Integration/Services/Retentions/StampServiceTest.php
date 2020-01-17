<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Retentions;

use PhpCfdi\Finkok\QuickFinkok;

final class StampServiceTest extends RetentionsTestCase
{
    public function testStampInvalidXml(): void
    {
        $result = $this->stampRetentionPreCfdi('invalid xml');
        $this->assertEmpty($result->xml());
        $this->assertEmpty($result->uuid());
        $this->assertTrue($result->hasAlerts());
    }

    public function testStampValidPreCfdiRetentions(): void
    {
        $result = $this->currentRetentionsStampResult();
        $this->assertSame('Comprobante timbrado satisfactoriamente', $result->statusCode());
        $this->assertNotEmpty($result->xml());
        $this->assertNotEmpty($result->uuid());
        $this->assertNotEmpty($result->seal());
    }

    public function testStampValidPreCfdiRetentionsTwice(): void
    {
        $currentResult = $this->currentRetentionsStampResult();
        $this->assertNotEmpty($currentResult->uuid(), 'There must be an already stamped precfdi');

        $preCfdi = $this->currentRetentionsPreCfdi(); // this is the same precfdi used on currentStampResult
        $repeatedResult = $this->stampRetentionPreCfdi($preCfdi);

        $this->assertSame($currentResult->uuid(), $repeatedResult->uuid(), 'UUID on first and second stamp must match');
        $this->assertSame('Comprobante timbrado previamente', $repeatedResult->statusCode());
        $this->assertSame('El CFDI contiene un timbre previo', $repeatedResult->alerts()->first()->message());
    }

    public function testDownloadRetentionRecentlyCreated(): void
    {
        $currentResult = $this->currentRetentionsStampResult();
        $this->assertNotEmpty($currentResult->uuid(), 'There must be an already stamped precfdi');

        // consume quickfinkok to simplify the execution
        $quickFinkok = new QuickFinkok($this->createSettingsFromEnvironment());
        $downloadResult = $quickFinkok->retentionDownload($currentResult->uuid(), 'EKU9003173C9');

        $this->assertXmlStringEqualsXmlString(
            $currentResult->xml(),
            $downloadResult->xml(),
            'Created and downloaded RET must be XML equal'
        );
        $this->assertSame(
            $currentResult->xml(),
            $downloadResult->xml(),
            'Created and downloaded RET must be identical'
        );
    }
}