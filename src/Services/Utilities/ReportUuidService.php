<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class ReportUuidService
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

    public function reportUuid(ReportUuidCommand $command): ReportUuidResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::utilities());
        $rawResponse = $soapCaller->call('report_uuid', [
            'taxpayer_id' => $command->rfc(),
            'invoice_type' => $command->type(),
            'date_from' => $command->sinceString(),
            'date_to' => $command->untilString(),
        ]);
        return new ReportUuidResult($rawResponse);
    }
}
