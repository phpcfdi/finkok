<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class AddService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function add(AddCommand $command): AddResult
    {
        $soapCaller = $this->settings()->createCallerForService(
            Services::registration(),
            'reseller_username',
            'reseller_password',
        );
        $rawResponse = $soapCaller->call('add', array_filter([
            'taxpayer_id' => $command->rfc(),
            'type_user' => $command->type()->value(),
            'cer' => $command->certificate(),
            'key' => $command->privateKey(),
            'passphrase' => $command->passPhrase(),
        ]));
        return new AddResult($rawResponse);
    }
}
