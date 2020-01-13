<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\DiscoverExtractor;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;

class GetSatStatusExtractor
{
    /** @var string[] */
    private $expressionData;

    /**
     * GetSatStatusExtractor constructor.
     *
     * @param array<mixed> $expressionData
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
        $discoverer = new DiscoverExtractor();
        return new self($discoverer->obtain($document));
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
