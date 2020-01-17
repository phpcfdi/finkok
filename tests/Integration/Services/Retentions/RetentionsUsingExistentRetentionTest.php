<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Retentions;

use PhpCfdi\Finkok\QuickFinkok;

/**
 * This class uses a currently signed cfdi to perform test that require this to exists
 * It uses QuickFinkok to simplify calls.
 */
final class RetentionsUsingExistentRetentionTest extends RetentionsTestCase
{
    /** @var QuickFinkok */
    private $quickFinkok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quickFinkok = new QuickFinkok($this->createSettingsFromEnvironment());
        $this->assertNotEmpty(
            $this->currentRetentionsStampResult()->uuid(),
            'To run this test there must be an already stamped retention precfdi'
        );
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
}
