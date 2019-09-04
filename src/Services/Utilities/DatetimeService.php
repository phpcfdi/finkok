<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class DatetimeService
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

    public function datetime(): DatetimeResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::utilities());
        $rawResponse = $soapCaller->call('datetime', []);
        $result = new DatetimeResult($rawResponse);
        return $result;
    }
}
