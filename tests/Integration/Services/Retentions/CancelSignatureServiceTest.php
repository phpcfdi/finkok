<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Retentions;

use PhpCfdi\Finkok\QuickFinkok;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
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
        $result = $this->quickFinkok->retentionCancel(
            $this->createCsdCredential(),
            CancelDocument::newWithErrorsUnrelated($uuid)
        );
        $this->assertSame('UUID Not Found', $result->statusCode());
    }

    /** @group large */
    public function testCancelSignatureRecentlyCreatedDocument(): void
    {
        $stampedToCancel = $this->quickFinkok->retentionStamp($this->newRetentionsPreCfdi());
        $uuid = $stampedToCancel->uuid();
        if ('' === $uuid) {
            throw new RuntimeException('Cannot create a CFDI RET to cancel');
        }

        $maxtime = strtotime('+5 minutes');
        while (true) {
            $result = $this->quickFinkok->retentionCancel(
                $this->createCsdCredential(),
                CancelDocument::newWithErrorsUnrelated($uuid)
            );
            $document = $result->documents()->first();

            // do not try again if a SAT issue is **different** from:
            // 708: Finkok cannot connect to SAT
            // 1205: UUID no existe (¿el SAT aún no lo tiene?)
            // 1308: Certificado revocado o caduco (¿el SAT tiene problemas de tiempo?)
            if (! in_array($document->documentStatus(), ['708', '1205', '1308'], true)) {
                break;
            }

            // try again
            if (time() > $maxtime) {
                $this->markTestSkipped(<<<MESSAGE
                    Unable to test QuickFinkok::retentionCancel():
                    StatusCode: {$result->statusCode()}, DocumentStatus {$document->documentStatus()}
                    MESSAGE);
            }
            sleep(5);
        }

        $this->assertNotEmpty($result->voucher(), 'Expected to receive an Acuse, but it was empty');
        $this->assertEmpty(
            $result->statusCode(),
            'CodEstatus should have content only when command has incorrect values or SAT service is failing'
        );
        $this->assertSame($uuid, $document->uuid());
        $this->assertSame('1201', $document->documentStatus());
    }
}
