<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class StampService
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

    public function stamp(StampingCommand $command): StampingResult
    {
        $settings = $this->settings();
        $soapCaller = $settings->soapCaller(Services::stamping());
        $rawResponse = $soapCaller->call('stamp', [
            'xml' => $command->xml(),
        ]);
        $result = new StampingResult($rawResponse->{'stampResult'} ?? (object) []);
        return $result;
    }
}
