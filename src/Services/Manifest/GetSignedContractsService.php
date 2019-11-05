<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Manifest;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class GetSignedContractsService
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

    public function getSignedContracts(GetSignedContractsCommand $command): GetSignedContractsResult
    {
        // this empty string are for ommiting sending username and password
        $soapCaller = $this->settings()->createCallerForService(Services::manifest(), '', '');
        $rawResponse = $soapCaller->call('get_documents', [
            'snid' => $command->snid(),
            'taxpayer_id' => $command->rfc(),
            'type' => $command->format()->value(),
        ]);
        return new GetSignedContractsResult($rawResponse, $command->format()->isPdf());
    }
}
