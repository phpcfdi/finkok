<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Retentions;

use PhpCfdi\Finkok\QuickFinkok;
use RuntimeException;

/**
 * This class uses QuickFinkok to simplify calls.
 */
final class CancelSignatureServiceTest extends RetentionsTestCase
{
    /** @var QuickFinkok */
    private $quickFinkok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quickFinkok = new QuickFinkok($this->createSettingsFromEnvironment());
    }

    public function testCancelSignatureWithNonExistentUUID(): void
    {
        $uuid = '11111111-2222-3333-4444-000000000001';
        $result = $this->quickFinkok->retentionCancel($this->createCsdCredential(), $uuid);
        $this->assertSame('UUID Not Found', $result->statusCode());
    }

    public function testCancelSignatureRecentlyCreatedDocument(): void
    {
        $stampedToCancel = $this->quickFinkok->retentionStamp($this->newRetentionsPreCfdi());
        $uuid = $stampedToCancel->uuid();
        if ('' === $uuid) {
            throw new RuntimeException('Cannot create a CFDI RET to cancel');
        }
        $result = $this->quickFinkok->retentionCancel($this->createCsdCredential(), $uuid);
        if ('1308' === $result->statusCode()) { // 1308 - Certificado revocado o caduco
            $this->markTestSkipped("SAT service error, finkok ticket #41610, RET UUID $uuid");
        }

        $this->assertNotEmpty($result->voucher(), 'Expected to receive an Acuse, but it was empty');
        $this->assertSame('1201', $result->statusCode());
        $this->assertSame($uuid, $result->documents()->first()->uuid());
    }

    /**
     * Use this method to test cancellation on one specific UUID, useful for debugging
     *
     * To enable this test you must add "@test" annotation
     */
    public function manualCancelSignatureRecentlyCreatedDocument(): void
    {
        $uuid = 'AAB81A24-8CD8-4703-A2CE-88F4E98E8044';
        $result = $this->quickFinkok->retentionCancel($this->createCsdCredential(), $uuid);
        echo json_encode($result->rawData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ('1308' === $result->statusCode()) {
            $this->markTestSkipped('Finkok ticket #41610, SAT error: Certificado revocado o caduco');
        }
        $this->assertSame($uuid, $result->documents()->first()->uuid(), 'Cancelled UUID must match with requested');
    }
}
