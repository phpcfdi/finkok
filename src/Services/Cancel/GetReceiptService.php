<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Cancel;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class GetReceiptService
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

    public function download(GetReceiptCommand $command): GetReceiptResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::cancel());
        $rawResponse = $soapCaller->call('get_receipt', [
            'taxpayer_id' => $command->rfc(),
            'uuid' => $command->uuid(),
            'type' => $command->type()->value(),
        ]);
        return new GetReceiptResult($rawResponse);
    }
}
