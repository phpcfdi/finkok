<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class GetContractsService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function obtainContracts(GetContractsCommand $command): GetContractsResult
    {
        // this empty string are for ommiting sending username and password
        $soapCaller = $this->settings()->createCallerForService(Services::manifest(), '', '');
        $rawResponse = $soapCaller->call('get_contracts_snid', [
            'snid' => $command->snid(),
            'taxpayer_id' => $command->rfc(),
            'name' => $command->name(),
            'address' => $command->address(),
            'email' => $command->email(),
        ]);
        return new GetContractsResult($rawResponse);
    }
}
