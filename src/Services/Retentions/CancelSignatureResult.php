<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Retentions;

class CancelSignatureResult extends \PhpCfdi\Finkok\Services\Cancel\CancelSignatureResult
{
    /**
     * @internal This property is not documented, see https://support.finkok.com/support/tickets/41498
     * @return string
     */
    public function tracing(): string
    {
        return $this->get('SeguimientoCancelacion');
    }
}
