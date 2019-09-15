<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DOMDocument;
use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\Credentials;
use PhpCfdi\XmlCancelacion\DOMSigner;

class GetRelatedSigner
{
    /** @var string */
    private $uuid;

    /** @var string */
    private $rfc;

    /** @var RfcRole */
    private $role;

    public function __construct(string $uuid, string $rfc, RfcRole $role)
    {
        $this->uuid = $uuid;
        $this->rfc = $rfc;
        $this->role = $role ?? RfcRole::emitter();
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

    public function createDocumentToSign(): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $root = $document->createElementNS('http://cancelacfd.sat.gob.mx', 'PeticionConsultaRelacionados');
        $xmlns = 'http://www.w3.org/2000/xmlns/';
        $root->setAttributeNS($xmlns, 'xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $root->setAttributeNS($xmlns, 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('RfcEmisor', ($this->role->isEmitter()) ? $this->rfc : '');
        $root->setAttribute('RfcPacEnviaSolicitud', 'CVD110412TF6');
        $root->setAttribute('RfcReceptor', ($this->role->isRecipient()) ? $this->rfc : '');
        $root->setAttribute('Uuid', $this->uuid);
        $document->appendChild($root);
        $document->normalizeDocument();
        return $document;
    }

    public function sign(string $certificateFile, string $privateKeyFile, string $passPhrase): string
    {
        $credentials = new Credentials($certificateFile, $privateKeyFile, $passPhrase);
        $document = $this->createDocumentToSign();
        $domSigner = new DOMSigner($document);
        $domSigner->sign($credentials);
        return $document->saveXML();
    }
}
