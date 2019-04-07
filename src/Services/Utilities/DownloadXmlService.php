<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class DownloadXmlService
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

    public function downloadXml(DownloadXmlCommand $command): DownloadXmlResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::utilities());
        $rawResponse = $soapCaller->call('get_xml', [
            'uuid' => $command->uuid(),
            'taxpayer_id' => $command->rfc(),
            'invoice_type' => $command->type(),
        ]);
        $result = new DownloadXmlResult($rawResponse);
        return $result;
    }
}
