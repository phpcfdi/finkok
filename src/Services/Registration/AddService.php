<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class AddService
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

    public function add(AddCommand $command): AddResult
    {
        $this->settings()->changeUsernameKey('reseller_username');
        $this->settings()->changePasswordKey('reseller_password');
        $soapCaller = $this->settings()->createCallerForService(Services::registration());
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
