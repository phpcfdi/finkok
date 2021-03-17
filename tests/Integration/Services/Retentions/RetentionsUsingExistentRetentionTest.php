<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Retentions;

use PhpCfdi\Finkok\QuickFinkok;
use PhpCfdi\Finkok\Services\Retentions\StampResult;

/**
 * This class uses a currently signed cfdi to perform test that require this to exists
 * It uses QuickFinkok to simplify calls.
 */
final class RetentionsUsingExistentRetentionTest extends RetentionsTestCase
{
    /** @var QuickFinkok */
    private $quickFinkok;

    /** @var string|null */
    protected static $staticCurrentStampPrecfdi;

    /** @var StampResult|null */
    protected static $staticCurrentStampResult;

    protected function currentRetentionsPreCfdi(): string
    {
        if (null === self::$staticCurrentStampPrecfdi) {
            self::$staticCurrentStampPrecfdi = $this->newRetentionsPreCfdi();
        }
        return self::$staticCurrentStampPrecfdi;
    }

    protected function currentRetentionsStampResult(): StampResult
    {
        if (null === self::$staticCurrentStampResult) {
            self::$staticCurrentStampResult = $this->stampRetentionPreCfdi($this->currentRetentionsPreCfdi());
        }
        return self::$staticCurrentStampResult;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->quickFinkok = new QuickFinkok($this->createSettingsFromEnvironment());
        $this->assertNotEmpty(
            $this->currentRetentionsStampResult()->uuid(),
            'To run this test there must be an already stamped retention precfdi'
        );
    }

    public function testStampValidPreCfdiRetentionsTwice(): void
    {
        $currentResult = $this->currentRetentionsStampResult();
        $preCfdi = $this->currentRetentionsPreCfdi(); // this is the same precfdi used on currentStampResult

        $repeatedResult = $this->quickFinkok->retentionStamp($preCfdi);

        $this->assertSame($currentResult->uuid(), $repeatedResult->uuid(), 'UUID on first and second stamp must match');
        $this->assertSame('Comprobante timbrado previamente', $repeatedResult->statusCode());
        $this->assertSame('El CFDI contiene un timbre previo', $repeatedResult->alerts()->first()->message());
    }

    public function testStamped(): void
    {
        $currentResult = $this->currentRetentionsStampResult();
        $preCfdi = $this->currentRetentionsPreCfdi(); // this is the same precfdi used on currentStampResult

        // consume quickfinkok to simplify the execution
        $downloadResult = $this->quickFinkok->retentionStamped($preCfdi);

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

    public function testDownloadRetentionRecentlyCreated(): void
    {
        $currentResult = $this->currentRetentionsStampResult();

        $downloadResult = $this->quickFinkok->retentionDownload($currentResult->uuid(), 'EKU9003173C9');

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
