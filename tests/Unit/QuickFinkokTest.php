<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\Finkok\QuickFinkok;
use PhpCfdi\Finkok\Services\Manifest\GetContractsResult;
use PhpCfdi\Finkok\Services\Manifest\SignContractsResult;
use PhpCfdi\Finkok\Services\Registration\CustomerStatus;
use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

/** @covers \PhpCfdi\Finkok\QuickFinkok */
class QuickFinkokTest extends TestCase
{
    /** @var FakeSoapFactory */
    private $soapFactory;

    private function createdPreparedQuickFinkok(stdClass $rawData): QuickFinkok
    {
        $fakeFactory = new FakeSoapFactory();
        $fakeFactory->preparedResult = $rawData;
        $settings = $this->createSettingsFromEnvironment($fakeFactory);
        $this->soapFactory = $fakeFactory;
        $finkok = new QuickFinkok($settings);
        return $finkok;
    }

    private function performTestOnLatestCall(string $serviceName, array $expectedParameters = []): void
    {
        $lastCall = $this->soapFactory->latestSoapCaller;
        $this->assertSame($serviceName, $lastCall->latestCallMethodName);
        $this->assertSame(
            array_merge($lastCall->latestCallParameters, $expectedParameters),
            $lastCall->latestCallParameters,
            'Expected parameters does not match'
        );
    }

    private function obtainParameterFromLatestCall(string $key)
    {
        return $this->soapFactory->latestSoapCaller->latestCallParameters[$key] ?? null;
    }

    public function testStamp(): void
    {
        $rawData = json_decode($this->fileContentPath('stamp-response-with-alerts.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->stamp('precfdi');
        $this->performTestOnLatestCall('stamp', ['xml' => 'precfdi']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testQuickStamp(): void
    {
        $rawData = json_decode($this->fileContentPath('quickstamp-response-with-alerts.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->quickStamp('precfdi');
        $this->performTestOnLatestCall('quick_stamp', ['xml' => 'precfdi']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testStamped(): void
    {
        $rawData = json_decode($this->fileContentPath('stamped-response-with-alerts.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->stamped('precfdi');
        $this->performTestOnLatestCall('stamped', ['xml' => 'precfdi']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCfdiDownload(): void
    {
        $rawData = json_decode($this->fileContentPath('utilities-getxml-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->cfdiDownload('x-uuid', 'x-rfc');
        $this->performTestOnLatestCall('get_xml', [
            'uuid' => 'x-uuid',
            'taxpayer_id' => 'x-rfc',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testStampQueryPending(): void
    {
        $rawData = json_decode($this->fileContentPath('querypending-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->stampQueryPending('x-uuid');
        $this->performTestOnLatestCall('query_pending', ['uuid' => 'x-uuid']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCancel(): void
    {
        $rawData = json_decode($this->fileContentPath('cancel-cancelsignature-response-2-items.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->cancel($this->createCsdCredential(), 'x-uuid');
        $this->performTestOnLatestCall('cancel_signature', [
            'store_pending' => false,
        ]);
        $this->assertStringContainsString('X-UUID', strval($this->obtainParameterFromLatestCall('xml')));
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testSatStatus(): void
    {
        $rawData = json_decode($this->fileContentPath('cancel-get-sat-status-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->satStatus('EKU9003173C9', 'COSC8001137NA', 'x-uuid', '123.45');
        $this->performTestOnLatestCall('get_sat_status', [
            'taxpayer_id' => 'EKU9003173C9',
            'rtaxpayer_id' => 'COSC8001137NA',
            'uuid' => 'x-uuid',
            'total' => '123.45',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testSatStatusXml(): void
    {
        $fakeCfdi = <<<EOT
<cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
                  xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital"
                  Version="3.3" Total="123.45" Sello="">
    <cfdi:Emisor Rfc="EKU9003173C9"/>
    <cfdi:Receptor Rfc="COSC8001137NA"/>
    <cfdi:Complemento>
        <tfd:TimbreFiscalDigital UUID="12345678-1234-1234-1234-000000000001"/>
    </cfdi:Complemento>
</cfdi:Comprobante>
EOT;
        $rawData = json_decode($this->fileContentPath('cancel-get-sat-status-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->satStatusXml($fakeCfdi);
        $this->performTestOnLatestCall('get_sat_status', [
            'taxpayer_id' => 'EKU9003173C9',
            'rtaxpayer_id' => 'COSC8001137NA',
            'uuid' => '12345678-1234-1234-1234-000000000001',
            'total' => '123.45',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testObtainRelated(): void
    {
        $rawData = json_decode($this->fileContentPath('cancel-get-related-signature-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->obtainRelated($this->createCsdCredential(), 'x-uuid', RfcRole::receiver());

        $this->performTestOnLatestCall('get_related_signature');
        $signedXml = strval($this->obtainParameterFromLatestCall('xml'));
        $this->assertStringContainsString('RfcReceptor="EKU9003173C9"', $signedXml);
        $this->assertStringContainsString('Uuid="x-uuid"', $signedXml);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testObtainPendingToCancel(): void
    {
        $rawData = json_decode($this->fileContentPath('cancel-get-pending-response-2-items.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->obtainPendingToCancel('EKU9003173C9');
        $this->performTestOnLatestCall('get_pending', ['rtaxpayer_id' => 'EKU9003173C9']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testAnswerAcceptRejectCancellation(): void
    {
        $rawData = json_decode($this->fileContentPath('cancel-accept-reject-signature-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->answerAcceptRejectCancellation(
            $this->createCsdCredential(),
            'x-uuid',
            CancelAnswer::reject()
        );

        $this->performTestOnLatestCall('accept_reject_signature');
        $signedXml = strval($this->obtainParameterFromLatestCall('xml'));
        $this->assertStringContainsString('RfcReceptor="EKU9003173C9"', $signedXml);
        $this->assertStringContainsString('<Respuesta>Rechazo</Respuesta>', $signedXml);
        $this->assertStringContainsString('<UUID>x-uuid</UUID>', $signedXml);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testObtainCancelRequestReceipt(): void
    {
        $rawData = json_decode($this->fileContentPath('cancel-get-receipt-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->obtainCancelRequestReceipt('EKU9003173C9', 'x-uuid');

        $this->performTestOnLatestCall('get_receipt', [
            'taxpayer_id' => 'EKU9003173C9',
            'uuid' => 'x-uuid',
            'type' => 'C',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testServersDateTime(): void
    {
        $rawData = json_decode($this->fileContentPath('utilities-datetime-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->serversDateTime();

        $this->performTestOnLatestCall('datetime');
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testReportUuids(): void
    {
        $rawData = json_decode($this->fileContentPath('utilities-report-uuid-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $since = new \DateTimeImmutable('2019-01-13 00:00:00');
        $until = $since->modify('+1 day -1 second');
        $result = $finkok->reportUuids('x-rfc', $since, $until);

        $this->performTestOnLatestCall('report_uuid', [
            'taxpayer_id' => 'x-rfc',
            'invoice_type' => 'I',
            'date_from' => '2019-01-13T00:00:00',
            'date_to' => '2019-01-13T23:59:59',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testReportCredits(): void
    {
        $rawData = json_decode($this->fileContentPath('utilities-report-credit-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->reportCredits('x-rfc');

        $this->performTestOnLatestCall('report_credit', ['taxpayer_id' => 'x-rfc']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testReportTotals(): void
    {
        $rawData = json_decode($this->fileContentPath('utilities-report-total-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->reportTotals('x-rfc', 2019, 2, 2019, 3);

        $this->performTestOnLatestCall('report_total', [
            'taxpayer_id' => 'x-rfc',
            'invoice_type' => 'I',
            'date_from' => '2019-02-01T00:00:00',
            'date_to' => '2019-04-01T00:00:00', // yes, this is how Finkok works
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomersAdd(): void
    {
        $rawData = json_decode($this->fileContentPath('registration-add-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customersAdd('x-rfc');

        $this->performTestOnLatestCall('add', [
            'taxpayer_id' => 'x-rfc',
            'type_user' => CustomerType::ondemand()->value(),
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomersEdit(): void
    {
        $rawData = json_decode($this->fileContentPath('registration-edit-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customersEdit('x-rfc', CustomerStatus::suspended());

        $this->performTestOnLatestCall('edit', [
            'taxpayer_id' => 'x-rfc',
            'status' => CustomerStatus::suspended()->value(),
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomersAssign(): void
    {
        $rawData = json_decode($this->fileContentPath('registration-assign-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customersAssign('x-rfc', 100);

        $this->performTestOnLatestCall('assign', [
            'taxpayer_id' => 'x-rfc',
            'credit' => 100,
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomersObtain(): void
    {
        $rawData = json_decode($this->fileContentPath('registration-get-response-2-items.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customersObtain('x-rfc');

        $this->performTestOnLatestCall('get', [
            'taxpayer_id' => 'x-rfc',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomerGetContracts(): void
    {
        $rawData = json_decode($this->fileContentPath('manifest-getcontracts-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customerGetContracts('x-rfc', 'x-name', 'x-address', 'x-email');

        $this->performTestOnLatestCall('get_contracts', [
            'taxpayer_id' => 'x-rfc',
            'name' => 'x-name',
            'address' => 'x-address',
            'email' => 'x-email',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomerSendContracts(): void
    {
        $rawData = json_decode($this->fileContentPath('manifest-signcontracts-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customerSendContracts('x-snid', 'x-privacy', 'x-contract');

        $this->performTestOnLatestCall('sign_contract', [
            'snid' => 'x-snid',
            'privacy_xml' => 'x-privacy',
            'contract_xml' => 'x-contract',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomerSignAndSendContracts(): void
    {
        $fiel = $this->createCsdCredential();
        $getContractsResult = new GetContractsResult(
            json_decode($this->fileContentPath('manifest-getcontracts-response.json'))
        );
        $signContractsResult = new SignContractsResult(
            json_decode($this->fileContentPath('manifest-signcontracts-response.json'))
        );
        /** @var QuickFinkok&MockObject $finkok */
        $finkok = $this->getMockBuilder(QuickFinkok::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['customerSignAndSendContracts'])
            ->getMock();
        $finkok->expects($this->once())
            ->method('customerGetContracts')->willReturn($getContractsResult)
            ->with(
                $this->identicalTo($fiel->rfc()),
                $this->identicalTo($fiel->legalName()),
                $this->identicalTo('x-address'),
                $this->identicalTo('x-email')
            );
        $finkok->expects($this->once())
            ->method('customerSendContracts')->willReturn(
                $signContractsResult
            )->with(
                $this->identicalTo('x-snid'),
                $this->stringContains($getContractsResult->privacy()),
                $this->stringContains($getContractsResult->contract())
            );

        $signedOn = new \DateTimeImmutable('2019-01-13 14:15:16');
        $finkok->customerSignAndSendContracts($fiel, 'x-snid', 'x-address', 'x-email', $signedOn);
    }

    public function testCustomerSignAndSendContractsWithFailureOnGetContracts(): void
    {
        $fiel = $this->createCsdCredential();
        $getContractsResult = GetContractsResult::createFromData(false, '', '', 'FOO');
        /** @var QuickFinkok&MockObject $finkok */
        $finkok = $this->getMockBuilder(QuickFinkok::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['customerSignAndSendContracts'])
            ->getMock();
        $finkok->expects($this->once())->method('customerGetContracts')->willReturn($getContractsResult);
        $result = $finkok->customerSignAndSendContracts($fiel, 'x-snid', 'x-address', 'x-email');
        $this->assertFalse($result->success());
        $this->assertSame('Unable to get contracts: FOO', $result->message());
    }
}
