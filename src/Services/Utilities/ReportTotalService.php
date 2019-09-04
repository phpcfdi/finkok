<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class ReportTotalService
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

    public function reportTotal(ReportTotalCommand $command): ReportTotalResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::utilities());
        $rawResponse = $soapCaller->call('report_total', [
            'taxpayer_id' => $command->rfc(),
            'invoice_type' => $command->type(),
            'date_from' => $command->startString(),
            'date_to' => $command->endString(),
        ]);
        return new ReportTotalResult($rawResponse);
    }
}
