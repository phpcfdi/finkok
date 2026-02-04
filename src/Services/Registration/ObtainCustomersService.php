<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\FinkokSettings;

class ObtainCustomersService
{
    public function __construct(private FinkokSettings $settings)
    {
    }

    public function settings(): FinkokSettings
    {
        return $this->settings;
    }

    public function obtainAll(): Customers
    {
        $page = 0;
        $result = new Customers([]);

        do {
            $page = $page + 1;
            $command = new ObtainCustomersCommand($page);
            $current = $this->obtainPage($command);
            $result = $result->merge($current->customers());
        } while ($current->pagesInformation()->hasMorePages());

        return $result;
    }

    public function obtainPage(ObtainCustomersCommand $command): ObtainCustomersResult
    {
        $soapCaller = $this->settings()->createCallerForService(
            Services::registration(),
            'username',
            'password',
        );
        $rawResponse = $soapCaller->call('customers', [
            'page' => $command->page(),
        ]);
        return new ObtainCustomersResult($rawResponse);
    }
}
