<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

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

    public function stamp(StampingCommand $command): StampingResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::stamping());
        // Finkok, repeat to fix bad webservice behavior of remote stamp method
        // This will not be fixed according to Finkok
        do {
            $rawResponse = $soapCaller->call('stamp', [
                'xml' => $command->xml(),
            ]);
            $result = new StampingResult('stampResult', $rawResponse);
            if (null !== $result->alerts()->findByErrorCode('307') && '' === $result->uuid()) {
                usleep(200000); // 0.2 seconds
                continue;
            }
            break;
        } while (true);
        return $result;
    }
}
