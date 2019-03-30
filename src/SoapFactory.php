<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use SoapClient;

class SoapFactory
{
    public function create(string $endpoint): SoapClient
    {
        return new SoapClient($endpoint . '?singleWsdl', [
            'trace' => true,
        ]);
    }
}
