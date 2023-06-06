<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Unit;

use DateTimeImmutable;
use LogicException;
use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\Finkok\QuickFinkok;
use PhpCfdi\Finkok\Services\Manifest\GetContractsResult;
use PhpCfdi\Finkok\Services\Manifest\SignContractsResult;
use PhpCfdi\Finkok\Services\Registration\CustomerStatus;
use PhpCfdi\Finkok\Services\Registration\CustomerType;
use PhpCfdi\Finkok\Tests\Fakes\FakeSoapFactory;
use PhpCfdi\Finkok\Tests\TestCase;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Stringable;

/** @covers \PhpCfdi\Finkok\QuickFinkok */
final class QuickFinkokTest extends TestCase
{
    /** @var FakeSoapFactory */
    private $soapFactory;

    private function createdPreparedQuickFinkok(stdClass $rawData): QuickFinkok
    {
        $fakeFactory = new FakeSoapFactory();
        $fakeFactory->preparedResult = $rawData;
        $settings = $this->createSettingsFromEnvironment($fakeFactory);
        $this->soapFactory = $fakeFactory;
        return new QuickFinkok($settings);
    }

    /**
     * @param string $serviceName
     * @param array<mixed> $expectedParameters
     */
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

    private function obtainParameterFromLatestCall(string $key): string
    {
        $value = $this->soapFactory->latestSoapCaller->latestCallParameters[$key] ?? null;
        if (null === $value || is_scalar($value) || (is_object($value) && is_callable([$value, '__toString']))) {
            /** @phpstan-var scalar|null|Stringable $value PHPStan false positive */
            return strval($value);
        }

        throw new LogicException('Cannot return an parameter that is not stringable');
    }

    public function testStamp(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('stamp-response-with-alerts.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->stamp('precfdi');
        $this->performTestOnLatestCall('stamp', ['xml' => 'precfdi']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testQuickStamp(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('quickstamp-response-with-alerts.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->quickStamp('precfdi');
        $this->performTestOnLatestCall('quick_stamp', ['xml' => 'precfdi']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testStamped(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('stamped-response-with-alerts.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->stamped('precfdi');
        $this->performTestOnLatestCall('stamped', ['xml' => 'precfdi']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCfdiDownload(): void
    {
        /** @var stdClass $rawData */
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
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('querypending-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->stampQueryPending('x-uuid');
        $this->performTestOnLatestCall('query_pending', ['uuid' => 'x-uuid']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCancel(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('cancel-cancelsignature-response-2-items.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $uuid = '12345678-1234-aaaa-1234-1234567890ab';
        $result = $finkok->cancel($this->createCsdCredential(), CancelDocument::newWithErrorsUnrelated($uuid));
        $this->performTestOnLatestCall('cancel_signature', [
            'store_pending' => false,
        ]);
        $this->assertStringContainsString(strtoupper($uuid), $this->obtainParameterFromLatestCall('xml'));
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testSatStatus(): void
    {
        /** @var stdClass $rawData */
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

    public function testSatStatusXml33(): void
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
        /** @var stdClass $rawData */
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

    public function testSatStatusXml40(): void
    {
        $fakeCfdi = <<<EOT
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
                            xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital"
                            Version="4.0" Total="123.45" Sello="">
                <cfdi:Emisor Rfc="EKU9003173C9"/>
                <cfdi:Receptor Rfc="COSC8001137NA"/>
                <cfdi:Complemento>
                    <tfd:TimbreFiscalDigital UUID="12345678-1234-1234-1234-000000000001"/>
                </cfdi:Complemento>
                </cfdi:Comprobante>
            EOT;

        /** @var stdClass $rawData */
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
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('cancel-get-related-signature-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->obtainRelated($this->createCsdCredential(), 'x-uuid', RfcRole::receiver());

        $this->performTestOnLatestCall('get_related_signature');
        $signedXml = $this->obtainParameterFromLatestCall('xml');
        $this->assertStringContainsString('RfcReceptor="EKU9003173C9"', $signedXml);
        $this->assertStringContainsString('Uuid="x-uuid"', $signedXml);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testObtainPendingToCancel(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('cancel-get-pending-response-2-items.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->obtainPendingToCancel('EKU9003173C9');
        $this->performTestOnLatestCall('get_pending', ['rtaxpayer_id' => 'EKU9003173C9']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testAnswerAcceptRejectCancellation(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('cancel-accept-reject-signature-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->answerAcceptRejectCancellation(
            $this->createCsdCredential(),
            'x-uuid',
            CancelAnswer::reject()
        );

        $this->performTestOnLatestCall('accept_reject_signature');
        $signedXml = $this->obtainParameterFromLatestCall('xml');
        $this->assertStringContainsString('RfcReceptor="EKU9003173C9"', $signedXml);
        $this->assertStringContainsString('<Respuesta>Rechazo</Respuesta>', $signedXml);
        $this->assertStringContainsString('<UUID>x-uuid</UUID>', $signedXml);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testObtainCancelRequestReceipt(): void
    {
        /** @var stdClass $rawData */
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
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('utilities-datetime-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->serversDateTime();

        $this->performTestOnLatestCall('datetime');
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testReportUuids(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('utilities-report-uuid-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $since = new DateTimeImmutable('2019-01-13 00:00:00');
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
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('utilities-report-credit-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->reportCredits('x-rfc');

        $this->performTestOnLatestCall('report_credit', ['taxpayer_id' => 'x-rfc']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testReportTotals(): void
    {
        /** @var stdClass $rawData */
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
        /** @var stdClass $rawData */
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
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('registration-edit-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customersEdit('x-rfc', CustomerStatus::suspended());

        $this->performTestOnLatestCall('edit', [
            'taxpayer_id' => 'x-rfc',
            'status' => CustomerStatus::suspended()->value(),
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomersSwitch(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('registration-switch-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $type = CustomerType::prepaid();
        $result = $finkok->customersSwitch('x-rfc', $type);

        $this->performTestOnLatestCall('switch', [
            'taxpayer_id' => 'x-rfc',
            'type_user' => $type->value(),
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomersAssign(): void
    {
        /** @var stdClass $rawData */
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
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('registration-get-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customersObtain('x-rfc');

        $this->performTestOnLatestCall('get', [
            'taxpayer_id' => 'x-rfc',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomersObtainAll(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('registration-customers-response-2-items.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customersObtainAll();

        $this->performTestOnLatestCall('customers', [
            'page' => 1,
        ]);

        $this->assertCount(2, $result);
        $this->assertNotNull($result->findByRfc('MAG041126GT8'));
        $this->assertNotNull($result->findByRfc('LAN7008173R5'));
        $this->assertNull($result->findByRfc('AAA010101AAA'));
    }

    public function testCustomerGetContracts(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('manifest-getcontracts-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);

        $result = $finkok->customerGetContracts('x-rfc', 'x-name', 'x-address', 'x-email', 'x-snid');

        $this->performTestOnLatestCall('get_contracts_snid', [
            'taxpayer_id' => 'x-rfc',
            'name' => 'x-name',
            'address' => 'x-address',
            'email' => 'x-email',
            'snid' => 'x-snid',
        ]);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testCustomerSendContracts(): void
    {
        /** @var stdClass $rawData */
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
        /** @var stdClass $dataGetContracts */
        $dataGetContracts = json_decode($this->fileContentPath('manifest-getcontracts-response.json'));
        $getContractsResult = new GetContractsResult($dataGetContracts);
        /** @var stdClass $dataSignContracts */
        $dataSignContracts = json_decode($this->fileContentPath('manifest-signcontracts-response.json'));
        $signContractsResult = new SignContractsResult($dataSignContracts);
        /** @var QuickFinkok&MockObject $finkok */
        $finkok = $this->getMockBuilder(QuickFinkok::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['customerGetContracts', 'customerSendContracts'])
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
            ->method('customerSendContracts')->willReturn($signContractsResult)
            ->with(
                $this->identicalTo('x-snid'),
                $this->stringContains($getContractsResult->privacy()),
                $this->stringContains($getContractsResult->contract())
            );

        $signedOn = new DateTimeImmutable('2019-01-13 14:15:16');
        $finkok->customerSignAndSendContracts($fiel, 'x-snid', 'x-address', 'x-email', $signedOn);
    }

    public function testCustomerSignAndSendContractsWithFailureOnGetContracts(): void
    {
        $fiel = $this->createCsdCredential();
        $getContractsResult = GetContractsResult::createFromData(false, '', '', 'FOO');
        /** @var QuickFinkok&MockObject $finkok */
        $finkok = $this->getMockBuilder(QuickFinkok::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['customerGetContracts'])
            ->getMock();
        $finkok->expects($this->once())->method('customerGetContracts')->willReturn($getContractsResult);
        $result = $finkok->customerSignAndSendContracts($fiel, 'x-snid', 'x-address', 'x-email');
        $this->assertFalse($result->success());
        $this->assertSame('Unable to get contracts: FOO', $result->message());
    }

    public function testRetentionStamp(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('retentions-stamp-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->retentionStamp('precfdi');
        $this->performTestOnLatestCall('stamp', ['xml' => 'precfdi']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testRetentionDownload(): void
    {
        $rawData = (object) ['get_xmlResult' => (object) []];
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->retentionDownload('x-uuid', 'x-rfc');
        $this->performTestOnLatestCall('get_xml', ['invoice_type' => 'R']);
        $this->assertEquals($rawData, $result->rawData());
    }

    public function testRetentionStamped(): void
    {
        /** @var stdClass $rawData */
        $rawData = json_decode($this->fileContentPath('retentions-stamped-response.json'));
        $finkok = $this->createdPreparedQuickFinkok($rawData);
        $result = $finkok->retentionStamped('x-xml');
        $this->performTestOnLatestCall('stamped', ['xml' => 'x-xml']);
        $this->assertEquals($rawData, $result->rawData());
    }
}
