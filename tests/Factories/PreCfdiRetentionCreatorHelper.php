<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Factories;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\Elements\Dividendos10\Dividendos;
use CfdiUtils\Elements\PagosAExtranjeros10\Pagosaextranjeros;
use CfdiUtils\Nodes\NodeInterface;
use CfdiUtils\Retenciones\RetencionesCreator10;
use DateTimeImmutable;
use DateTimeZone;

final class PreCfdiRetentionCreatorHelper
{
    /** @var Certificado */
    private $certificate;

    /** @var DateTimeImmutable */
    private $invoiceDate;

    /** @var string */
    private $cveReten;

    /** @var string */
    private $emisorRfc;

    /** @var string */
    private $emisorName;

    /** @var string */
    private $keyPemFile;

    /** @var string */
    private $passPhrase;

    public function __construct(
        string $cerFile,
        string $keyPemFile,
        string $passPhrase
    ) {
        $this->certificate = new Certificado($cerFile);
        $this->emisorRfc = $this->certificate->getRfc();
        $this->emisorName = $this->certificate->getName();
        $this->keyPemFile = $keyPemFile;
        $this->passPhrase = $passPhrase;
        $this->invoiceDate = new DateTimeImmutable('now -5 minutes', new DateTimeZone('America/Mexico_City'));
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

    public function createRetencionesCreator10(): RetencionesCreator10
    {
        $creator = new RetencionesCreator10();
        $retenciones = $creator->retenciones();

        $retenciones->addAttributes([
            'FechaExp' => $this->getInvoiceDate()->format('c'),
            'CveRetenc' => $this->getCveReten(),
        ]);
        $retenciones->addEmisor([
            'RFCEmisor' => $this->getEmisorRfc(),
            'NomDenRazSocE' => $this->getEmisorName(),
        ]);
        $retenciones->getReceptor()->addExtranjero([
            'NumRegIdTrib' => '34100005800',
            'NomDenRazSocR' => 'THE USA COMPANY, INC.',
        ]);

        $periodoEjercicio = intval($this->getInvoiceDate()->format('Y')) - 1;
        $retenciones->addPeriodo([
            'MesIni' => '1',
            'MesFin' => '12',
            'Ejerc' => $periodoEjercicio,
        ]);

        $retenciones->addTotales([
            'montoTotOperacion' => '0',
            'montoTotGrav' => '0',
            'montoTotExent' => '0',
            'montoTotRet' => '0',
        ]);
        return $creator;
    }

    public function signPrecfdi(RetencionesCreator10 $creator): string
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

    /**
     * @param array<string, string> $pagosAExtranjeros
     * @param array<string, string> $noBeneficiario
     * @return Pagosaextranjeros<NodeInterface>
     */
    public function createPagosAExtranjerosNoBeneficiario(
        array $pagosAExtranjeros = [],
        array $noBeneficiario = []
    ): Pagosaextranjeros {
        $pagosAExtranjeros = new Pagosaextranjeros(array_merge([
            'EsBenefEfectDelCobro' => 'NO',
        ], $pagosAExtranjeros));
        $pagosAExtranjeros->addNoBeneficiario(array_merge([
            'PaisDeResidParaEfecFisc' => 'US',
            'ConceptoPago' => '3', // 3 - Persona moral
            'DescripcionConcepto' => 'Dividendos provenientes de CUFIN al 31 de Dic 2013',
        ], $noBeneficiario));

        return $pagosAExtranjeros;
    }
}
