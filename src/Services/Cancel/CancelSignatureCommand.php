<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\CancelStorePending;

class CancelSignatureCommand
{
    private CancelStorePending $storePending;

    /**
     * CancelSignatureCommand constructor.
     *
     * @param string $xml The signed xml
     * @param CancelStorePending|null $storePending Defaults to CancelStorePending::no()
     */
    public function __construct(private string $xml, ?CancelStorePending $storePending = null)
    {
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
