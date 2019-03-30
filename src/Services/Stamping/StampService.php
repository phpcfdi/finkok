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

    public function createSoapCaller(): SoapCaller
    {
        $environment = $this->settings()->environment();
        $soapFactory = $this->settings()->soapFactory();
        return new SoapCaller($soapFactory->create(
            $environment->endpoint(Services::stamping())
        ));
    }

    public function stamp(StampingCommand $command): StampingResult
    {
        $settings = $this->settings();

        $soapCaller = $this->createSoapCaller();

        $rawResponse = $soapCaller->call('stamp', [[
            'xml' => $command->xml(),
            'username' => $settings->username(),
            'password' => $settings->password(),
        ]]);

        return StampingResult::makeFromSoapResponse($rawResponse);
    }
}
