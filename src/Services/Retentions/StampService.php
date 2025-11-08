<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Retentions;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class StampService
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

    public function stamp(StampCommand $command): StampResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::retentions());
        $rawResponse = $soapCaller->call('stamp', [
            'xml' => $command->xml(),
        ]);
        return new StampResult($rawResponse);
    }
}
