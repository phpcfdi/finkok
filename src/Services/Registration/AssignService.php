<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class AssignService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function assign(AssignCommand $command): AssignResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::registration());
        $rawResponse = $soapCaller->call('assign', [
            'taxpayer_id' => $command->rfc(),
            'credit' => $command->credit(),
        ]);
        return new AssignResult($rawResponse);
    }
}
