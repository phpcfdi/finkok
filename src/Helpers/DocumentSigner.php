<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DateTimeImmutable;
use DOMDocument;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class DocumentSigner
{
    /** @var string */
    private $rfc;

    /** @var DateTimeImmutable */
    private $date;

    /** @var string */
    private $content;

    public function __construct(string $rfc, DateTimeImmutable $date, string $content)
    {
        $this->rfc = $rfc;
        $this->date = $date;
        $this->content = $content;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function sign(string $certificateFile, string $privateKeyFile, string $passPhrase): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $root = $document->createElement('documento');
        $document->appendChild($root);
        $contract = $document->createElement('contrato', $this->content());
        $contract->setAttribute('rfc', $this->rfc());
        $contract->setAttribute('fecha', $this->date()->format('Y-m-d\TH:i:s'));
        $root->appendChild($contract);
        $document->normalizeDocument();

        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $objDSig->addReference(
            $document,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true]
        );

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->passphrase = $passPhrase; // set passphrase before loading key
        $objKey->loadKey($privateKeyFile, true, false);

        $objDSig->sign($objKey);
        $objDSig->add509Cert($certificateFile, true, true);

        $objDSig->appendSignature($root);
        return $document->saveXML();
    }
}
