<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\DiscoverExtractor;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante32;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante33;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante40;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;
use RuntimeException;

/**
 * Based on a XML string or a XML Document it can extract the appropiate values to build a GetSatStatusCommand object
 * It is using the CFDI QR expressions
 */
class GetSatStatusExtractor
{
    /** @var string[] */
    private $expressionData;

    /**
     * GetSatStatusExtractor constructor.
     *
     * @param array<string, string> $expressionData
     */
    public function __construct(array $expressionData)
    {
        $this->expressionData = [
            're' => strval($expressionData['re'] ?? ''),
            'rr' => strval($expressionData['rr'] ?? ''),
            'tt' => strval($expressionData['tt'] ?? ''),
            'id' => strval($expressionData['id'] ?? ''),
        ];
    }

    public static function fromXmlDocument(DOMDocument $document): self
    {
        $discoverer = new DiscoverExtractor(
            new Comprobante40(),
            new Comprobante33(),
            new Comprobante32(),
        );
        try {
            $values = $discoverer->obtain($document);
        } catch (UnmatchedDocumentException $exception) {
            $message = 'Unable to obtain the expression values, document must be valid a CFDI version 4.0, 3.3 or 3.2';
            throw new RuntimeException($message, 0, $exception);
        }
        return new self($values);
    }

    public static function fromXmlString(string $xmlCfdi): self
    {
        $document = new DOMDocument();
        $document->loadXML($xmlCfdi);
        return static::fromXmlDocument($document);
    }

    public function buildCommand(): GetSatStatusCommand
    {
        $expData = $this->expressionData;
        return new GetSatStatusCommand($expData['re'], $expData['rr'], $expData['id'], $expData['tt']);
    }
}
