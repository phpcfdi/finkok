<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class EditService
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

    public function edit(EditCommand $command): EditResult
    {
        $this->settings()->changeUsernameKey('reseller_username');
        $this->settings()->changeUsernameKey('reseller_password');
        $soapCaller = $this->settings()->createCallerForService(Services::registration());
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
