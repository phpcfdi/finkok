<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\SoapCaller;

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
        $soapCaller = $this->createSoapCaller();
        $rawResponse = $soapCaller->call('stamp', [
            'xml' => $command->xml(),
        ]);
        $result = new StampingResult('stampResult', $rawResponse);
        return $result;
    }

    protected function createSoapCaller(): SoapCaller
    {
        return $this->settings()->soapCaller(Services::stamping());
    }
}
