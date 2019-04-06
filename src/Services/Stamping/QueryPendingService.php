<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\SoapFactory;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class QueryPendingService
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

    public function queryPending(QueryPendingCommand $command): QueryPendingResult
    {
        $soapCaller = $this->settings()->createCallerForService(Services::stamping());
        $rawResponse = $soapCaller->call('query_pending', [
            'uuid' => $command->uuid(),
        ]);
        $result = new QueryPendingResult('query_pendingResult', $rawResponse);
        return $result;
    }
}
