<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class QueryPendingService
{
    public function __construct(private FinkokSettings $settings)
    {
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
        return new QueryPendingResult($rawResponse);
    }
}
