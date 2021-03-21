<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Stamping;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

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
        return new QueryPendingResult($rawResponse);
    }
}
