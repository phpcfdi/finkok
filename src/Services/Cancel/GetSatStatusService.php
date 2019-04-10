<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class GetSatStatusService
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

    public function query(GetSatStatusCommand $command): GetSatStatusResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::cancel());
        $rawResponse = $soapCaller->call('get_sat_status', [
            'taxpayer_id' => $command->rfcIssuer(),
            'rtaxpayer_id' => $command->rfcRecipient(),
            'uuid' => $command->uuid(),
            'total' => $command->total(),
        ]);
        $result = new GetSatStatusResult($rawResponse);
        return $result;
    }
}
