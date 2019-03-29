<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use PhpCfdi\Finkok\Exceptions\InvalidArgumentException;

class FinkokSettings
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var FinkokEnvironment */
    private $environment;

    public function __construct(string $username, string $password, FinkokEnvironment $environment = null)
    {
        if ('' === $username) {
            throw new InvalidArgumentException('Invalid username');
        }
        if ('' === $password) {
            throw new InvalidArgumentException('Invalid password');
        }
        $this->username = $username;
        $this->password = $password;
        $this->environment = $environment ?? FinkokEnvironment::makeDevelopment();
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function environment(): FinkokEnvironment
    {
        return $this->environment;
    }
}
