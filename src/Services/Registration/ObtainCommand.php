<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Services\Registration;

use PhpCfdi\Finkok\Exceptions\InvalidArgumentException;

class ObtainCommand
{
    /** @var string */
    private $rfc;

    public function __construct(string $rfc)
    {
        if ('' === $rfc) {
            throw new InvalidArgumentException('Invalid RFC, cannot be empty');
        }

        $this->rfc = $rfc;
    }

    public function rfc(): string
    {
        return $this->rfc;
    }
}
