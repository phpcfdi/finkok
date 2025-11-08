<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class GetRelatedSignatureService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function getRelatedSignature(GetRelatedSignatureCommand $command): GetRelatedSignatureResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::cancel());
        $rawResponse = $soapCaller->call('get_related_signature', [
            'xml' => $command->xml(),
        ]);
        return new GetRelatedSignatureResult($rawResponse);
    }
}
