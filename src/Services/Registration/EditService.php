<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class EditService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function edit(EditCommand $command): EditResult
    {
        $soapCaller = $this->settings()->createCallerForService(
            Services::registration(),
            'reseller_username',
            'reseller_password'
        );
        $rawResponse = $soapCaller->call('edit', array_filter([
            'taxpayer_id' => $command->rfc(),
            'status' => $command->status()->value(),
            'cer' => $command->certificate(),
            'key' => $command->privateKey(),
            'passphrase' => $command->passPhrase(),
        ]));
        return new EditResult($rawResponse);
    }
}
