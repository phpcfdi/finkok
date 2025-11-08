<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Factories;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\Elements\Dividendos10\Dividendos;
use CfdiUtils\Nodes\NodeInterface;
use CfdiUtils\Retenciones\RetencionesCreator20;
use DateTimeImmutable;
use DateTimeZone;

final class PreCfdiRetentionCreatorHelper
{
    private Certificado $certificate;

    private DateTimeImmutable $invoiceDate;

    private string $cveReten;

    private string $emisorRfc;

    private string $emisorName;

    private string $keyPemFile;

    private string $passPhrase;

    private string $emisorLocation;

    private string $emisorRegimen;

    public function __construct(
        string $cerFile,
        string $keyPemFile,
        string $passPhrase,
        string $emisorRfc,
        string $emisorName,
        string $emisorLocation,
        string $emisorRegimen
    ) {
        $this->certificate = new Certificado($cerFile);
        $this->emisorRfc = $emisorRfc;
        $this->emisorName = $emisorName;
        $this->keyPemFile = $keyPemFile;
        $this->passPhrase = $passPhrase;
        $this->invoiceDate = new DateTimeImmutable('now -5 minutes', new DateTimeZone('America/Mexico_City'));
        $this->emisorLocation = $emisorLocation;
        $this->emisorRegimen = $emisorRegimen;
    }

    public function getInvoiceDate(): DateTimeImmutable
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(DateTimeImmutable $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    public function getEmisorRfc(): string
    {
        return $this->emisorRfc;
    }

    public function setEmisorRfc(string $emisorRfc): void
    {
        $this->emisorRfc = $emisorRfc;
    }

    public function getEmisorName(): string
    {
        return $this->emisorName;
    }

    public function setEmisorName(string $emisorName): void
    {
        $this->emisorName = $emisorName;
    }

    public function getEmisorLocation(): string
    {
        return $this->emisorLocation;
    }

    public function setEmisorLocation(string $emisorLocation): void
    {
        $this->emisorLocation = $emisorLocation;
    }

    public function getEmisorRegimen(): string
    {
        return $this->emisorRegimen;
    }

    public function setEmisorRegimen(string $emisorRegimen): void
    {
        $this->emisorRegimen = $emisorRegimen;
    }

    public function getKeyPemFile(): string
    {
        return $this->keyPemFile;
    }

    public function getPassPhrase(): string
    {
        return $this->passPhrase;
    }

    public function getCertificate(): Certificado
    {
        return $this->certificate;
    }

    public function getCveReten(): string
    {
        return $this->cveReten;
    }

    public function setCveReten(string $cveReten): void
    {
        $this->cveReten = $cveReten;
    }

    public function createRetencionesCreator20(): RetencionesCreator20
    {
        $creator = new RetencionesCreator20();
        $retenciones = $creator->retenciones();

        $retenciones->addAttributes([
            'FechaExp' => $this->getInvoiceDate()->format('Y-m-d\TH:i:s'),
            'CveRetenc' => $this->getCveReten(),
            'LugarExpRetenc' => $this->getEmisorLocation(),
        ]);
        $retenciones->addEmisor([
            'RfcE' => $this->getEmisorRfc(),
            'NomDenRazSocE' => $this->getEmisorName(),
            'RegimenFiscalE' => $this->getEmisorRegimen(),
        ]);
        $retenciones->getReceptor()->addExtranjero([
            'NumRegIdTribR' => '34100005800',
            'NomDenRazSocR' => 'THE USA COMPANY, INC.',
        ]);

        $periodoEjercicio = intval($this->getInvoiceDate()->format('Y')) - 1;
        $retenciones->addPeriodo([
            'MesIni' => '01',
            'MesFin' => '12',
            'Ejercicio' => $periodoEjercicio,
        ]);

        $retenciones->addTotales([
            'MontoTotOperacion' => '0',
            'MontoTotGrav' => '0',
            'MontoTotExent' => '0',
            'MontoTotRet' => '0',
        ]);
        return $creator;
    }

    public function signPreCfdi(RetencionesCreator20 $creator): string
    {
        $creator->putCertificado($this->getCertificate());
        $creator->addSello('file://' . $this->getKeyPemFile(), $this->getPassPhrase());
        return $creator->asXml();
    }

    /**
     * @param array<string, string> $dividOUtil
     * @return Dividendos<NodeInterface>
     */
    public function createDividendosDividOUtil(array $dividOUtil = []): Dividendos
    {
        $dividendos = new Dividendos();
        $dividendos->addDividOUtil(array_merge([
            'CveTipDivOUtil' => '06', // 06 - Proviene de CUFIN al 31 de diciembre 2013
            'MontISRAcredRetMexico' => '0',
            'MontISRAcredRetExtranjero' => '0',
            'MontRetExtDivExt' => '0',
            'TipoSocDistrDiv' => 'Sociedad Nacional',
            'MontISRAcredNal' => '0',
            'MontDivAcumNal' => '0',
            'MontDivAcumExt' => '0',
        ], $dividOUtil));

        return $dividendos;
    }
}
