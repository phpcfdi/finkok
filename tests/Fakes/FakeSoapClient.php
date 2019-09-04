<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Fakes;

use SoapClient;

class FakeSoapClient extends SoapClient
{
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        return '';
    }
}
