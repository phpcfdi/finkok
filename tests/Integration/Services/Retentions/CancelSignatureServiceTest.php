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
        $expectedStatusCode = sprintf('UUID: %s No Encontrado', $uuid);

        $result = $this->quickFinkok->retentionCancel(
            $this->createCsdCredential(),
            CancelDocument::newWithErrorsUnrelated($uuid)
        );

        $this->assertSame($expectedStatusCode, $result->statusCode());
    }

    /** @group large */
    public function testCancelSignatureRecentlyCreatedDocument(): void
    {
        $stampedToCancel = $this->quickFinkok->retentionStamp($this->newRetentionsPreCfdi());
        $uuid = $stampedToCancel->uuid();
        if ('' === $uuid) {
            throw new RuntimeException('Cannot create a CFDI RET to cancel');
        }

        $maxTime = $this->timePlusLongTestTimeOut();
        $cancelCredential = $this->createCsdCredential();
        while (true) {
            $result = $this->quickFinkok->retentionCancel(
                $cancelCredential,
                CancelDocument::newWithErrorsUnrelated($uuid)
            );
            $document = $result->documents()->first();

            // do not try again if a SAT issue is **different** from:
            // 708: Finkok cannot connect to SAT
            // 1205: UUID no existe (¿el SAT aún no lo tiene?)
            // 1308: Certificado revocado o caduco (¿el SAT tiene problemas de tiempo?)
            if (
                ! in_array($result->statusCode(), ['1205', '1308'], true) &&
                ! in_array($document->documentStatus(), ['708'], true)
            ) {
                break;
            }

            // do not try again if in the loop for more than allowed
            if (time() > $maxTime) {
                $this->markTestSkipped(<<<MESSAGE
                    Unable to test cancellation of a Retentions CFDI (timeout):
                    StatusCode: [{$result->statusCode()}], DocumentStatus [{$document->documentStatus()}]
                    MESSAGE);
            }

            // wait and repeat
            sleep(9);
        }

        $this->assertNotEmpty($result->voucher(), 'Expected to receive an Acuse, but it was empty');
        $this->assertEmpty(
            $result->statusCode(),
            "CodEstatus (value: {$result->statusCode()}) should have content only when failing"
        );
        $this->assertSame($uuid, $document->uuid());
        $this->assertSame('1201', $document->documentStatus());
    }
}
