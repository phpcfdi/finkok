<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class SignContractsService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function sendSignedContracts(SignContractsCommand $command): SignContractsResult
    {
        // this empty string are for ommiting sending username and password
        $soapCaller = $this->settings()->createCallerForService(Services::manifest(), '', '');
        $rawResponse = $soapCaller->call('sign_contract', [
            'snid' => $command->snid(),
            'privacy_xml' => $command->privacy(),
            'contract_xml' => $command->contract(),
        ]);
        return new SignContractsResult($rawResponse);
    }
}
