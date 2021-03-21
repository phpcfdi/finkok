<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class AcceptRejectSignatureService
{
    /** @var FinkokSettings */
    private $settings;

    public function __construct(FinkokSettings $settings)
    {
        $this->settings = $settings;
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function acceptRejectSignature(AcceptRejectSignatureCommand $command): AcceptRejectSignatureResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::cancel());
        $rawResponse = $soapCaller->call('accept_reject_signature', [
            'xml' => $command->xml(),
        ]);
        return new AcceptRejectSignatureResult($rawResponse);
    }
}
