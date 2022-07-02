<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Factories;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\CfdiCreator33;
use CfdiUtils\CfdiCreator40;
use CfdiUtils\Utils\Format as CfdiFormat;
use CfdiUtils\Utils\Rfc;
use DateTimeImmutable;
use DateTimeZone;

final class PreCfdiCreatorHelper
{
    /** @var DateTimeImmutable */
    private $invoiceDate;

    /** @var float */
    private $conceptoAmount;

    /** @var string */
    private $emisorRfc;

    /** @var string */
    private $emisorName;

    /** @var string */
    private $receptorRfc;

    /** @var string */
    private $cerFile;

    /** @var string */
    private $keyPemFile;

    /** @var string */
    private $passPhrase;

    /** @var string */
    private $relation = '';

    /** @var string[] */
    private $relatedUuids = [];

    public function __construct(
        string $cerFile,
        string $keyPemFile,
        string $passPhrase
    ) {
        $certificate = new Certificado($cerFile);
        $this->emisorRfc = $certificate->getRfc();
        $this->emisorName = $certificate->getName();
        $this->receptorRfc = Rfc::RFC_GENERIC;
        $this->cerFile = $cerFile;
        $this->keyPemFile = $keyPemFile;
        $this->passPhrase = $passPhrase;
        $this->invoiceDate = new DateTimeImmutable('now -5 minutes', new DateTimeZone('America/Mexico_City'));
        $this->conceptoAmount = round(random_int(1000, 4000) + random_int(0, 99) / 100, 2);
    }

    public function getInvoiceDate(): DateTimeImmutable
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(DateTimeImmutable $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    public function getConceptoAmount(): float
    {
        return $this->conceptoAmount;
    }

    public function setConceptoAmount(float $conceptoAmount): void
    {
        $this->conceptoAmount = $conceptoAmount;
    }

    public function setRelatedCfdis(string $relation, string ...$uuids): void
    {
        $this->relation = $relation;
        $this->relatedUuids = $uuids;
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

    public function getReceptorRfc(): string
    {
        return $this->receptorRfc;
    }

    public function setReceptorRfc(string $receptorRfc): void
    {
        $this->receptorRfc = $receptorRfc;
    }

    public function getCerFile(): string
    {
        return $this->cerFile;
    }

    public function getKeyPemFile(): string
    {
        return $this->keyPemFile;
    }

    public function getPassPhrase(): string
    {
        return $this->passPhrase;
    }

    public function create(): string
    {
        $creator = new CfdiCreator40();

        $comprobante = $creator->comprobante();
        $comprobante->addAttributes([
            'Fecha' => $this->getInvoiceDate()->format('Y-m-d\TH:i:s'),
            'FormaPago' => '01', // efectivo
            'Moneda' => 'MXN',
            'TipoDeComprobante' => 'I', // ingreso
            'MetodoPago' => 'PUE',
            'LugarExpedicion' => '86000',
        ]);
        if ('' !== $this->relation && count($this->relatedUuids) > 0) {
            $relacionados = $comprobante->getCfdiRelacionados();
            foreach ($this->relatedUuids as $relatedUuid) {
                $relacionados->addCfdiRelacionado(['UUID' => $relatedUuid]);
            }
            $relacionados['TipoRelacion'] = $this->relation;
        }
        $comprobante->addEmisor([
            'Rfc' => $this->getEmisorRfc(),
            'Nombre' => 'ACCEM SERVICIOS EMPRESARIALES SC',
            'RegimenFiscal' => '601',
        ]);
        $comprobante->addReceptor([
            'Rfc' => $this->getReceptorRfc(),
            'UsoCFDI' => 'G03', // gastos en general
        ]);
        $comprobante->addConcepto([
            'ClaveProdServ' => '52161557', // Consola portátil de juegos de computador
            'NoIdentificacion' => 'GAMEPAD007',
            'Cantidad' => '4',
            'ClaveUnidad' => 'H87', // Pieza
            'Unidad' => 'PIEZA',
            'Descripcion' => 'Portable tetris gamepad pro++ ⏻',
            'ValorUnitario' => CfdiFormat::number($this->getConceptoAmount() / 4, 2),
            'Importe' => CfdiFormat::number($this->getConceptoAmount(), 2),
            'Descuento' => CfdiFormat::number($this->getConceptoAmount() / 4, 2), // hot sale: take 4, pay 3
        ])->addTraslado([
            'Base' => CfdiFormat::number(3 * $this->getConceptoAmount() / 4, 2),
            'Impuesto' => '002', // IVA
            'TipoFactor' => 'Tasa',
            'TasaOCuota' => '0.160000',
            'Importe' => CfdiFormat::number(3 * 0.16 * $this->getConceptoAmount() / 4, 2),
        ]);

        $creator->addSumasConceptos();
        $creator->putCertificado(new Certificado($this->getCerFile()), false);
        $creator->addSello('file://' . $this->getKeyPemFile(), $this->getPassPhrase());

        return $creator->asXml();
    }
}
