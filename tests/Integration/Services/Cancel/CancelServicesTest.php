<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Integration\Services\Cancel;

use PhpCfdi\Finkok\Definitions\ReceiptType;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureService;
use PhpCfdi\Finkok\Services\Cancel\GetReceiptCommand;
use PhpCfdi\Finkok\Services\Cancel\GetReceiptService;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusResult;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusService;
use PhpCfdi\Finkok\Tests\Integration\IntegrationTestCase;
use PhpCfdi\XmlCancelacion\Capsule;

class CancelServicesTest extends IntegrationTestCase
{
    public function testCreateCfdiThenGetSatStatusThenCancelSignatureThenGetReceipt(): void
    {
        $settings = $this->createSettingsFromEnvironment();

        // given a cfdi
        $cfdi = $this->stamp($this->newStampingCommand());
        $this->assertNotEmpty($cfdi->uuid(), 'Cannot create a CFDI to test against');

        // check that it has a correct status
        /** @var GetSatStatusResult $beforeCancelStatus */
        $beforeCancelStatus = null;
        $this->waitUntil(function () use (&$beforeCancelStatus, $settings, $cfdi): bool {
            $beforeCancelStatus = (new GetSatStatusService($settings))->query(
                $this->createGetSatStatusCommandFromCfdiContents($cfdi->xml())
            );
            return ('No Encontrado' !== $beforeCancelStatus->cfdi());
        }, 30, 1, 'Cannot assert cfdi before cancel status is not: No Encontrado');

        $this->assertSame('Vigente', $beforeCancelStatus->cfdi());
        $this->assertStringStartsWith('Cancelable ', $beforeCancelStatus->cancellable());

        // Create cancel signature command from capsule
        $command = $this->createCancelSignatureCommandFromCapsule(new Capsule('TCM970625MB1', [$cfdi->uuid()]));

        // evaluate if known response was 205, 708 or 300
        // this is common to happend on testing but not in production since the time
        // elapsed from stamping and cancelling is often more than 2 minutes
        $repeatUntil = strtotime('now +5 minutes');
        do {
            // perform cancel
            $result = (new CancelSignatureService($settings))->cancelSignature($command);
            $document = $result->documents()->first();
            // do not try again if a SAT issue is **not** found
            // 708: Fink ok cannot connect to SAT
            // 300: ?
            // 205: SAT does not have the uuid available for cancellation
            if (! (in_array($result->statusCode(), ['708', '300'], true) || '205' === $document->documentStatus())) {
                break;
            }
            // do not try again if in the loop for more than allowed
            if (time() > $repeatUntil) {
                break;
            }
            // wait and repeat
            sleep(5);
        } while (true);

        // check result related document
        $this->assertSame(
            '201', // 201 - Petición de cancelación realizada exitosamente
            $document->documentStatus(),
            'SAT did not return 201 EstatusUUID on CancelSignature, is the service down?'
        );
        // check result properties
        $this->assertNotEmpty($result->voucher(), 'Finkok did not return voucher (Acuse) on CancelSignature');
        $this->assertNotEmpty($result->date(), 'Finkok did not return the cancellation date');
        $this->assertSame('TCM970625MB1', $result->rfc(), 'Finkok did not return expected RFC');

        // Consume GetReceiptService
        $receipt = (new GetReceiptService($settings))->download(
            new GetReceiptCommand('TCM970625MB1', $cfdi->uuid(), ReceiptType::cancellation())
        );
        $this->assertSame($result->voucher(), $receipt->receipt());
    }
}
