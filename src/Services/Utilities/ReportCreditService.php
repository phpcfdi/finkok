<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class ReportCreditService
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

    public function reportCredit(ReportCreditCommand $command): ReportCreditResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::utilities());
        $rawResponse = $soapCaller->call('report_credit', [
            'taxpayer_id' => $command->rfc(),
        ]);
        return new ReportCreditResult($rawResponse);
    }
}
