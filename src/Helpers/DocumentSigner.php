<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DateTimeImmutable;
use DOMDocument;
use LogicException;
use PhpCfdi\Credentials\Credential;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class DocumentSigner
{
    public function __construct(private string $rfc, private DateTimeImmutable $date, private string $content)
    {
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
        $credential = Credential::openFiles($certificateFile, $privateKeyFile, $passPhrase);
        return $this->signUsingCredential($credential);
    }

    public function signUsingCredential(Credential $credential): string
    {
        $document = $this->createDocumentToSign();
        $this->signDocumentUsingCredential($document, $credential);
        return strval($document->saveXML());
    }

    public function createDocumentToSign(): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $root = $document->createElement('documento');
        $document->appendChild($root);
        $contract = $document->createElement('contrato', $this->content());
        $contract->setAttribute('rfc', $this->rfc());
        $contract->setAttribute('fecha', $this->date()->format('Y-m-d\TH:i:s'));
        $root->appendChild($contract);
        return $document;
    }

    public function signDocumentUsingCredential(DOMDocument $document, Credential $credential): void
    {
        $root = $document->documentElement;
        if (null === $root) {
            throw new LogicException('The DOM Document does not contains a root element');
        }
        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $objDSig->addReference(
            $document,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true],
        );

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->passphrase = $credential->privateKey()->passPhrase(); // set passphrase before loading key
        $objKey->loadKey($credential->privateKey()->pem(), false, false);

        $objDSig->sign($objKey);
        $objDSig->add509Cert($credential->certificate()->pem(), true, false);

        $objDSig->appendSignature($root);
    }
}
