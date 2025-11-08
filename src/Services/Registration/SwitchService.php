<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class SwitchService
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

    public function switch(SwitchCommand $command): SwitchResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::registration());
        $rawResponse = $soapCaller->call('switch', [
            'taxpayer_id' => $command->rfc(),
            'type_user' => $command->customerType()->value(),
        ]);
        return new SwitchResult($rawResponse);
    }
}
