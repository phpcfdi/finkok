<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use DateTimeImmutable;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\Finkok\Definitions\ReceiptType;
use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\Finkok\Definitions\SignedDocumentFormat;
use PhpCfdi\Finkok\Services\Cancel;
use PhpCfdi\Finkok\Services\Manifest;
use PhpCfdi\Finkok\Services\Registration;
use PhpCfdi\Finkok\Services\Retentions;
use PhpCfdi\Finkok\Services\Stamping;
use PhpCfdi\Finkok\Services\Utilities;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;

class QuickFinkok
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    /**
     * Obtiene la configuración de conexión con la que será consumida la API de Finkok
     *
     * @return FinkokSettings
     */
    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    /**
     * Este método se encarga de realizar el timbrado de XML
     *
     * @param string $preCfdi
     * @return Stamping\StampingResult
     * @see https://wiki.finkok.com/doku.php?id=stamp
     */
    public function stamp(string $preCfdi): Stamping\StampingResult
    {
        $command = new Stamping\StampingCommand($preCfdi);
        $service = new Stamping\StampService($this->settings());
        return $service->stamp($command);
    }

    /**
     * Este método se encarga de realizar el timbrado de XML, una vez que se timbra el XML se guarda en una cola de
     * espera para ser enviado en el mejor momento a los servicios del SAT, por lo que el servicio de timbrado es
     * mucho más rápido, y es de uso recomendado en timbrado de alto número de timbres por segundo.
     *
     * Nota: El CFDI generado puede no estar disponible en los servidores del SAT hasta varios minutos después
     *
     * @param string $preCfdi
     * @return Stamping\StampingResult
     * @see https://wiki.finkok.com/doku.php?id=metodo_quick_stamp
     */
    public function quickStamp(string $preCfdi): Stamping\StampingResult
    {
        $command = new Stamping\StampingCommand($preCfdi);
        $service = new Stamping\QuickStampService($this->settings());
        return $service->quickstamp($command);
    }

    /**
     * Este método regresa la información de un XML ya timbrado previamente y que por algún motivo no se pudo recuperar
     * en la primera petición que se realizó, con este método se puede recuperar el UUID y el XML timbrado
     *
     * @param string $preCfdi
     * @return Stamping\StampingResult
     * @see https://wiki.finkok.com/doku.php?id=stamped
     */
    public function stamped(string $preCfdi): Stamping\StampingResult
    {
        $command = new Stamping\StampingCommand($preCfdi);
        $service = new Stamping\StampedService($this->settings());
        return $service->stamped($command);
    }

    /**
     * Obtiene el XML de un UUID timbrado en Finkok de tipo CFDI 3.3
     * solo es posible recuperar los timbrados en los últimos 3 meses.
     *
     * @param string $uuid
     * @param string $rfc
     * @return Utilities\DownloadXmlResult
     * @see https://wiki.finkok.com/doku.php?id=get_xml
     */
    public function cfdiDownload(string $uuid, string $rfc): Utilities\DownloadXmlResult
    {
        $command = new Utilities\DownloadXmlCommand($uuid, $rfc, 'I');
        $service = new Utilities\DownloadXmlService($this->settings());
        return $service->downloadXml($command);
    }

    /**
     * Este método se usa para consultar el estatus de una factura que se quedó pendiente de enviar al SAT debido a una
     * falla en el sistema del SAT o bien que se envió a través del método Quick_Stamp
     *
     * @param string $uuid
     * @return Stamping\QueryPendingResult
     * @see https://wiki.finkok.com/doku.php?id=query_pending
     */
    public function stampQueryPending(string $uuid): Stamping\QueryPendingResult
    {
        $command = new Stamping\QueryPendingCommand($uuid);
        $service = new Stamping\QueryPendingService($this->settings());
        return $service->queryPending($command);
    }

    /**
     * Este método es el encargado de cancelar uno o varios CFDI emitidos por medio de los webservices de Finkok
     * Durante el proceso no se envía ningún CSD a Finkok y la solicitud firmada es creada usando los datos del CSD
     *
     * @param Credential $credential
     * @param CancelDocument $document
     * @return Cancel\CancelSignatureResult
     * @see https://wiki.finkok.com/doku.php?id=cancelsigned_method
     * @see https://wiki.finkok.com/doku.php?id=cancel_method
     */
    public function cancel(Credential $credential, CancelDocument $document): Cancel\CancelSignatureResult
    {
        $signer = new Helpers\CancelSigner(new CancelDocuments($document));
        $signedRequest = $signer->sign($credential);
        $command = new Cancel\CancelSignatureCommand($signedRequest);
        $service = new Cancel\CancelSignatureService($this->settings());
        return $service->cancelSignature($command);
    }

    /**
     * Consulta el estatus del CFDI emitido con Finkok y la forma posible de cancelación a partir de todos sus datos.
     * También se puede consultar en modo producción utilizando la librería `phpcfdi/sat-estado-cfdi`
     *
     * @param string $rfcIssuer
     * @param string $rfcRecipient
     * @param string $uuid
     * @param string $total
     * @return Cancel\GetSatStatusResult
     * @see https://wiki.finkok.com/doku.php?id=get_sat_status
     * @see https://github.com/phpcfdi/sat-estado-cfdi
     */
    public function satStatus(
        string $rfcIssuer,
        string $rfcRecipient,
        string $uuid,
        string $total,
    ): Cancel\GetSatStatusResult {
        $command = new Cancel\GetSatStatusCommand($rfcIssuer, $rfcRecipient, $uuid, $total);
        $service = new Cancel\GetSatStatusService($this->settings());
        return $service->query($command);
    }

    /**
     * Consulta el estatus del CFDI emitido por Finkok y la forma posible de cancelación a partir del XML de un CFDI
     *
     * @param string $xmlCfdi
     * @return Cancel\GetSatStatusResult
     * @see https://wiki.finkok.com/doku.php?id=get_sat_status
     */
    public function satStatusXml(string $xmlCfdi): Cancel\GetSatStatusResult
    {
        $extractor = Helpers\GetSatStatusExtractor::fromXmlString($xmlCfdi);
        $command = $extractor->buildCommand();
        $service = new Cancel\GetSatStatusService($this->settings());
        return $service->query($command);
    }

    /**
     * Obtiene una lista de los UUID relacionados de un UUID
     *
     * @param Credential $credential
     * @param string $uuid
     * @param RfcRole|null $role Si es NULL entonces se usa el rol de emisor
     * @return Cancel\GetRelatedSignatureResult
     * @see https://wiki.finkok.com/doku.php?id=get_related_signature
     * @see https://wiki.finkok.com/doku.php?id=get_related
     */
    public function obtainRelated(
        Credential $credential,
        string $uuid,
        ?RfcRole $role = null,
    ): Cancel\GetRelatedSignatureResult {
        $signer = new Helpers\GetRelatedSigner($uuid, $role);
        $signedRequest = $signer->sign($credential);
        $command = new Cancel\GetRelatedSignatureCommand($signedRequest);
        $service = new Cancel\GetRelatedSignatureService($this->settings());
        return $service->getRelatedSignature($command);
    }

    /**
     * Consulta las solicitudes de cancelación tiene pendientes un receptor
     * Durante el proceso no se envía ningún CSD a Finkok y la solicitud firmada es creada usando los datos del CSD
     *
     * @param string $rfc
     * @return Cancel\GetPendingResult
     * @see https://wiki.finkok.com/doku.php?id=get_pending
     */
    public function obtainPendingToCancel(string $rfc): Cancel\GetPendingResult
    {
        $command = new Cancel\GetPendingCommand($rfc);
        $service = new Cancel\GetPendingService($this->settings());
        return $service->obtainPending($command);
    }

    /**
     * Permite al receptor de una factura Aceptar o Rechazar una determinada cancelación que tenga pendiente
     * Durante el proceso no se envía ningún CSD a Finkok y la solicitud firmada es creada usando los datos del CSD
     *
     * @param Credential $credential
     * @param string $uuid
     * @param CancelAnswer $answer
     * @return Cancel\AcceptRejectSignatureResult
     * @see https://wiki.finkok.com/doku.php?id=accept_reject_signature
     * @see https://wiki.finkok.com/doku.php?id=accept_reject
     */
    public function answerAcceptRejectCancellation(
        Credential $credential,
        string $uuid,
        CancelAnswer $answer,
    ): Cancel\AcceptRejectSignatureResult {
        $signer = new Helpers\AcceptRejectSigner($uuid, $answer);
        $signedRequest = $signer->sign($credential);
        $command = new Cancel\AcceptRejectSignatureCommand($signedRequest);
        $service = new Cancel\AcceptRejectSignatureService($this->settings());
        return $service->acceptRejectSignature($command);
    }

    /**
     * Obtiene el acuse de recepción de la solicitud de cancelación
     *
     * @param string $rfcIssuer
     * @param string $uuid
     * @return Cancel\GetReceiptResult
     * @see https://wiki.finkok.com/doku.php?id=get_receipt
     */
    public function obtainCancelRequestReceipt(string $rfcIssuer, string $uuid): Cancel\GetReceiptResult
    {
        $command = new Cancel\GetReceiptCommand($rfcIssuer, $uuid, ReceiptType::cancellation());
        $service = new Cancel\GetReceiptService($this->settings());
        return $service->download($command);
    }

    /**
     * Obtener la fecha exacta de los servidores de timbrado para realizar el XML del CFDI
     *
     * @param string $postalCode Si está vacío se utiliza el horario de America/Mexico_City
     * @return Utilities\DatetimeResult
     * @see https://wiki.finkok.com/doku.php?id=datetime
     */
    public function serversDateTime(string $postalCode = ''): Utilities\DatetimeResult
    {
        $command = new Utilities\DatetimeCommand($postalCode);
        $service = new Utilities\DatetimeService($this->settings());
        return $service->datetime($command);
    }

    /**
     * Reporte de los UUID timbrados con fecha, de un RFC entre un periodo de tiempo determinado
     *
     * @param string $rfc
     * @param DateTimeImmutable $since
     * @param DateTimeImmutable $until
     * @return Utilities\ReportUuidResult
     * @see https://wiki.finkok.com/doku.php?id=report_uuid
     */
    public function reportUuids(
        string $rfc,
        DateTimeImmutable $since,
        DateTimeImmutable $until,
    ): Utilities\ReportUuidResult {
        $command = new Utilities\ReportUuidCommand($rfc, 'I', $since, $until);
        $service = new Utilities\ReportUuidService($this->settings());
        return $service->reportUuid($command);
    }

    /**
     * Reporte de los créditos añadidos de un RFC
     *
     * @param string $rfc
     * @return Utilities\ReportCreditResult
     * @see https://wiki.finkok.com/doku.php?id=report_uuid
     */
    public function reportCredits(string $rfc): Utilities\ReportCreditResult
    {
        $command = new Utilities\ReportCreditCommand($rfc);
        $service = new Utilities\ReportCreditService($this->settings());
        return $service->reportCredit($command);
    }

    /**
     * Reporte de los créditos añadidos de un RFC
     *
     * @param string $rfc
     * @param int $startYear
     * @param int $startMonth
     * @param int $endYear
     * @param int $endMonth
     * @return Utilities\ReportTotalResult
     * @see https://wiki.finkok.com/doku.php?id=report_uuid
     */
    public function reportTotals(
        string $rfc,
        int $startYear,
        int $startMonth,
        int $endYear = 0,
        int $endMonth = 0,
    ): Utilities\ReportTotalResult {
        $command = new Utilities\ReportTotalCommand($rfc, 'I', $startYear, $startMonth, $endYear, $endMonth);
        $service = new Utilities\ReportTotalService($this->settings());
        return $service->reportTotal($command);
    }

    /**
     * Agrega un cliente que va a timbrar bajo la cuenta de un socio de negocios de Finkok
     *
     * @param string $rfc
     * @param Registration\CustomerType|null $type Si es NULL entonces se utiliza OnDemand
     * @return Registration\AddResult
     * @see https://wiki.finkok.com/doku.php?id=add
     */
    public function customersAdd(string $rfc, ?Registration\CustomerType $type = null): Registration\AddResult
    {
        $command = new Registration\AddCommand($rfc, $type);
        $service = new Registration\AddService($this->settings());
        return $service->add($command);
    }

    /**
     * Edita el estatus de un cliente (suspender o activar)
     *
     * @param string $rfc
     * @param Registration\CustomerStatus $status
     * @return Registration\EditResult
     * @see https://wiki.finkok.com/doku.php?id=edit
     */
    public function customersEdit(string $rfc, Registration\CustomerStatus $status): Registration\EditResult
    {
        $command = new Registration\EditCommand($rfc, $status);
        $service = new Registration\EditService($this->settings());
        return $service->edit($command);
    }

    /**
     * Edita el tipo de un cliente (prepago/Prepaid o ilimitado/Ondemand)
     *
     * @param string $rfc
     * @param Registration\CustomerType $type
     * @return Registration\SwitchResult
     * @see https://wiki.finkok.com/doku.php?id=switch
     */
    public function customersSwitch(string $rfc, Registration\CustomerType $type): Registration\SwitchResult
    {
        $command = new Registration\SwitchCommand($rfc, $type);
        $service = new Registration\SwitchService($this->settings());
        return $service->switch($command);
    }

    /**
     * Obtiene los datos del RFC especificado
     *
     * @param string $filterByRfc
     * @return Registration\ObtainResult
     * @see https://wiki.finkok.com/doku.php?id=get
     */
    public function customersObtain(string $filterByRfc): Registration\ObtainResult
    {
        $command = new Registration\ObtainCommand($filterByRfc);
        $service = new Registration\ObtainService($this->settings());
        return $service->obtain($command);
    }

    /**
     * Obtiene el listado completo de clientes, consultando todas las páginas necesarias.
     * El resultado es solo la lista de clientes, sin acceso a los resultados de cada una de las consultas.
     *
     * @return Registration\Customers
     * @see https://wiki.finkok.com/doku.php?id=customers
     */
    public function customersObtainAll(): Registration\Customers
    {
        $service = new Registration\ObtainCustomersService($this->settings());
        return $service->obtainAll();
    }

    /**
     * Asignar créditos un cliente que va a timbrar
     * Si el crédito es un valor positivo y el cliente está como OnDemand se cambiará a PrePaid con los créditos
     * Si el crédito es un valor positivo y el cliente está como PrePaid sumarán los créditos a los actuales
     * Si el crédito es -1 el cliente se pondrá como OnDemand
     *
     * @param string $rfc
     * @param int $credits
     * @return Registration\AssignResult
     * @see https://wiki.finkok.com/doku.php?id=assign
     */
    public function customersAssign(string $rfc, int $credits): Registration\AssignResult
    {
        $command = new Registration\AssignCommand($rfc, $credits);
        $service = new Registration\AssignService($this->settings());
        return $service->assign($command);
    }

    /**
     * Obtiene los textos del manifiesto de FINKOK (Aviso de Privacidad y Contrato de Servicio)
     *
     * @param string $rfc
     * @param string $name
     * @param string $address
     * @param string $email
     * @param string $snid
     * @return Manifest\GetContractsResult
     * @see https://wiki.finkok.com/doku.php?id=get_contracts_snid
     */
    public function customerGetContracts(
        string $rfc,
        string $name,
        string $address,
        string $email,
        string $snid,
    ): Manifest\GetContractsResult {
        $command = new Manifest\GetContractsCommand($rfc, $name, $address, $email, $snid);
        $service = new Manifest\GetContractsService($this->settings());
        return $service->obtainContracts($command);
    }

    /**
     * Envía el aviso de privacidad y contratos firmados
     *
     * @param string $snid
     * @param string $signedPrivacy
     * @param string $signedContract
     * @return Manifest\SignContractsResult
     * @see https://wiki.finkok.com/doku.php?id=firmar
     */
    public function customerSendContracts(
        string $snid,
        string $signedPrivacy,
        string $signedContract,
    ): Manifest\SignContractsResult {
        $command = new Manifest\SignContractsCommand($snid, $signedPrivacy, $signedContract);
        $service = new Manifest\SignContractsService($this->settings());
        return $service->sendSignedContracts($command);
    }

    /**
     * Obtiene los documentos legales (aviso de privacidad y contrato), los firma con la FIEL y envía
     *
     * @param Credential $fiel
     * @param string $snid
     * @param string $address
     * @param string $email
     * @param DateTimeImmutable|null $signedOn Si es NULL se utiliza la fecha y hora del sistema
     * @return Manifest\SignContractsResult
     */
    public function customerSignAndSendContracts(
        Credential $fiel,
        string $snid,
        string $address,
        string $email,
        ?DateTimeImmutable $signedOn = null,
    ): Manifest\SignContractsResult {
        $rfc = $fiel->rfc();
        $name = $fiel->legalName();
        $signedOn ??= new DateTimeImmutable();
        $documents = $this->customerGetContracts($rfc, $name, $address, $email, $snid);
        if (! $documents->success()) {
            return Manifest\SignContractsResult::createFromData(
                false,
                sprintf('Unable to get contracts: %s', $documents->error() ?: '(no error)'),
            );
        }
        $privacy = (new Helpers\DocumentSigner($rfc, $signedOn, $documents->privacy()))->signUsingCredential($fiel);
        $contract = (new Helpers\DocumentSigner($rfc, $signedOn, $documents->contract()))->signUsingCredential($fiel);
        return $this->customerSendContracts($snid, $privacy, $contract);
    }

    /**
     * Obtiene los documentos legales (aviso de privacidad y contrato) previamente firmados con el SNID y RFC
     *
     * @param string $snid
     * @param string $rfc
     * @param SignedDocumentFormat|null $format
     * @return Manifest\GetSignedContractsResult
     */
    public function customerGetSignedContracts(
        string $snid,
        string $rfc,
        ?SignedDocumentFormat $format = null,
    ): Manifest\GetSignedContractsResult {
        $format ??= SignedDocumentFormat::xml();
        $command = new Manifest\GetSignedContractsCommand($snid, $rfc, $format);
        $service = new Manifest\GetSignedContractsService($this->settings());
        return $service->getSignedContracts($command);
    }

    public function retentionStamp(string $xml): Retentions\StampResult
    {
        $command = new Retentions\StampCommand($xml);
        $service = new Retentions\StampService($this->settings());
        return $service->stamp($command);
    }

    /**
     * Obtiene el XML de un UUID timbrado en Finkok de tipo CFDI de retenciones e información de pagos
     * solo es posible recuperar los timbrados en los últimos 3 meses.
     *
     * @param string $uuid
     * @param string $rfc
     * @return Utilities\DownloadXmlResult
     * @see https://wiki.finkok.com/doku.php?id=get_xml
     */
    public function retentionDownload(string $uuid, string $rfc): Utilities\DownloadXmlResult
    {
        $command = new Utilities\DownloadXmlCommand($uuid, $rfc, 'R');
        $service = new Utilities\DownloadXmlService($this->settings());
        return $service->downloadXml($command);
    }

    /**
     * Este método regresa la información de un XML de retenciones e información de pagos ya timbrado previamente y
     * que por algún motivo no se pudo recuperar en la primera petición que se realizó,
     * con este método se puede recuperar el UUID y el XML timbrado
     *
     * Nota: el método no está documentado en Finkok
     *
     * @param string $preCfdi
     * @return Retentions\StampedResult
     * @see https://wiki.finkok.com/doku.php?id=retentions
     */
    public function retentionStamped(string $preCfdi): Retentions\StampedResult
    {
        $command = new Retentions\StampedCommand($preCfdi);
        $service = new Retentions\StampedService($this->settings());
        return $service->stamped($command);
    }

    /**
     * Este método es el encargado de cancelar un CFDI de retenciones emitido por medio de los webservices de Finkok
     * Durante el proceso no se envía ningún CSD a Finkok y la solicitud firmada es creada usando los datos del CSD
     *
     * @param Credential $credential
     * @param CancelDocument $document
     * @return Retentions\CancelSignatureResult
     * @see https://wiki.finkok.com/doku.php?id=cancel_signature_method_retentions
     * @see https://wiki.finkok.com/doku.php?id=cancel_method_retentions
     */
    public function retentionCancel(Credential $credential, CancelDocument $document): Retentions\CancelSignatureResult
    {
        $signer = new Helpers\CancelSigner(new CancelDocuments($document));
        $signedRequest = $signer->signRetention($credential);
        $command = new Retentions\CancelSignatureCommand($signedRequest);
        $service = new Retentions\CancelSignatureService($this->settings());
        return $service->cancelSignature($command);
    }
}
