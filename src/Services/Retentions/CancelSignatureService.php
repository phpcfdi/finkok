<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Retentions;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class CancelSignatureService
{
    private FinkokSettings $settings;

    public function __construct(FinkokSettings $settings)
    {
        $this->settings = $settings;
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function cancelSignature(CancelSignatureCommand $command): CancelSignatureResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::retentions());
        $rawResponse = $soapCaller->call('cancel_signature', [
            'xml' => $command->xml(),
            'store_pending' => $command->storePending()->asBool(),
        ]);
        return new CancelSignatureResult($rawResponse);
    }
}
