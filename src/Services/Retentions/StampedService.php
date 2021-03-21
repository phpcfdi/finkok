<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Retentions;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class StampedService
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

    public function stamped(StampedCommand $command): StampedResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::retentions());
        $rawResponse = $soapCaller->call('stamped', [
            'xml' => $command->xml(),
        ]);
        return new StampedResult($rawResponse);
    }
}
