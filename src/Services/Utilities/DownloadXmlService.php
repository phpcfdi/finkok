<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Utilities;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class DownloadXmlService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function downloadXml(DownloadXmlCommand $command): DownloadXmlResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::utilities());
        do {
            $rawResponse = $soapCaller->call('get_xml', [
                'uuid' => $command->uuid(),
                'taxpayer_id' => $command->rfc(),
                'invoice_type' => $command->type(),
            ]);
            $result = new DownloadXmlResult($rawResponse);
            // Finkok sometimes returns the path to the file instead of content (Ticket #18950)
            if (str_ends_with($result->xml(), '.xml')) {
                usleep(200000); // 0.2 seconds
                continue;
            }
            break;
        } while (true);
        return $result;
    }
}
