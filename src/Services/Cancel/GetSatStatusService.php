<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class GetSatStatusService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function query(GetSatStatusCommand $command): GetSatStatusResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::cancel());
        $rawResponse = $soapCaller->call('get_sat_status', [
            'taxpayer_id' => $command->rfcIssuer(),
            'rtaxpayer_id' => $command->rfcRecipient(),
            'uuid' => $command->uuid(),
            'total' => $command->total(),
        ]);
        return new GetSatStatusResult($rawResponse);
    }

    public function queryUntilFoundOrTime(GetSatStatusCommand $command, int $waitSeconds = 120): GetSatStatusResult
    {
        $runUntilTime = time() + $waitSeconds;
        do {
            $result = $this->query($command);
            if ('No Encontrado' === $result->cfdi() && time() <= $runUntilTime) {
                usleep(200000);
                continue;
            }
            break;
        } while (true);
        return $result;
    }
}
