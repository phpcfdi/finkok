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

    public function datetime(DatetimeCommand $command = null): DatetimeResult
    {
        if (null === $command) {
            $command = new DatetimeCommand('');
        }
        $soapCaller = $this->settings()->createCallerForService(Services::utilities());
        $rawResponse = $soapCaller->call('datetime', array_filter([
            'zipcode' => $command->postalCode(),
        ]));
        $result = new DatetimeResult($rawResponse);
        return $result;
    }
}
