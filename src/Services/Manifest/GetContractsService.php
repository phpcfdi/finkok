<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class GetContractsService
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

    public function obtainContracts(GetContractsCommand $command): GetContractsResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::manifest());
        $rawResponse = $soapCaller->call('get_contracts', [
            'taxpayer_id' => $command->rfc(),
            'name' => $command->name(),
            'address' => $command->address(),
            'email' => $command->email(),
        ]);
        return new GetContractsResult($rawResponse);
    }
}
