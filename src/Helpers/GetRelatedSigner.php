<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DOMDocument;
use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\DOMSigner;

class GetRelatedSigner
{
    public const DEFAULT_PACRFC = 'CVD110412TF6';

    /** @var string */
    private $uuid;

    /** @var string */
    private $rfc;

    /** @var RfcRole */
    private $role;

    /** @var string */
    private $pacRfc;

    /**
     * GetRelatedSigner constructor.
     *
     * @param string $uuid
     * @param string $rfc
     * @param RfcRole|null $role If null (ommited) then uses emitter role
     * @param string $pacRfc If empty (ommited) then uses DEFAULT_PACRFC
     */
    public function __construct(string $uuid, string $rfc, RfcRole $role = null, string $pacRfc = self::DEFAULT_PACRFC)
    {
        $this->uuid = $uuid;
        $this->rfc = $rfc;
        $this->role = $role ?? RfcRole::emitter();
        $this->pacRfc = $pacRfc ?: static::DEFAULT_PACRFC;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function role(): RfcRole
    {
        return $this->role;
    }

    public function pacRfc(): string
    {
        return $this->pacRfc;
    }

    public function createDocumentToSign(): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $root = $document->createElementNS('http://cancelacfd.sat.gob.mx', 'PeticionConsultaRelacionados');
        $xmlns = 'http://www.w3.org/2000/xmlns/';
        $root->setAttributeNS($xmlns, 'xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $root->setAttributeNS($xmlns, 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('RfcEmisor', ($this->role->isEmitter()) ? $this->rfc : '');
        $root->setAttribute('RfcPacEnviaSolicitud', $this->pacRfc);
        $root->setAttribute('RfcReceptor', ($this->role->isRecipient()) ? $this->rfc : '');
        $root->setAttribute('Uuid', $this->uuid);
        $document->appendChild($root);
        $document->normalizeDocument();
        return $document;
    }

    public function sign(string $certificateFile, string $privateKeyFile, string $passPhrase): string
    {
        $credentials = new Credentials($certificateFile, $privateKeyFile, $passPhrase);
        return $this->signUsingCredentials($credentials);
    }

    public function signUsingCredentials(Credentials $credentials): string
    {
        $document = $this->createDocumentToSign();
        $domSigner = new DOMSigner($document);
        $domSigner->sign($credentials);
        return $document->saveXML();
    }
}
