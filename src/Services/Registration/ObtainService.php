<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class ObtainService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function obtain(ObtainCommand $command): ObtainResult
    {
        $soapCaller = $this->settings()->createCallerForService(
            Services::registration(),
            'reseller_username',
            'reseller_password'
        );
        $rawResponse = $soapCaller->call('get', [
            'taxpayer_id' => $command->rfc(),
        ]);
        return new ObtainResult($rawResponse);
    }
}
