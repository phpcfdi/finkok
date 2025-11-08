<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class QuickStampService
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

    public function quickstamp(StampingCommand $command): StampingResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::stamping());
        $rawResponse = $soapCaller->call('quick_stamp', [
            'xml' => $command->xml(),
        ]);
        return new StampingResult('quick_stampResult', $rawResponse);
    }
}
