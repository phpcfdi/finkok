<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\CancelStorePending;

class CancelSignatureCommand
{
    /** @var string */
    private $xml;

    /** @var CancelStorePending */
    private $storePending;

    /**
     * CancelSignatureCommand constructor.
     *
     * @param string $xml The signed xml
     * @param CancelStorePending|null $storePending Defaults to CancelStorePending::no()
     */
    public function __construct(string $xml, CancelStorePending $storePending = null)
    {
        $this->xml = $xml;
        $this->storePending = $storePending ?? CancelStorePending::no();
    }

    public function xml(): string
    {
        return $this->xml;
    }

    public function storePending(): CancelStorePending
    {
        return $this->storePending;
    }
}
