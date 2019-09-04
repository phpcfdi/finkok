<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok;

use PhpCfdi\Finkok\Definitions\Services;
use PhpCfdi\Finkok\Exceptions\InvalidArgumentException;

/**
 * @todo Renombrar a FinkokContainer
 */
class FinkokSettings
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var FinkokEnvironment */
    private $environment;

    /** @var SoapFactory */
    private $soapFactory;

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
        $this->soapFactory = new SoapFactory();
    }

    public function changeSoapFactory(SoapFactory $soapFactory): void
    {
        $this->soapFactory = $soapFactory;
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

    public function soapFactory(): SoapFactory
    {
        return $this->soapFactory;
    }

    /**
     * This method created a configured SoapCaller with wsdlLocation and default options
     *
     * @param Services $service
     * @param string $usernameKey defaults to username, if empty then it will be ommited
     * @param string $passwordKey defaults to password, if empty then it will be ommited
     * @return SoapCaller
     */
    public function createCallerForService(
        Services $service,
        string $usernameKey = 'username',
        string $passwordKey = 'password'
    ): SoapCaller {
        $wsdlLocation = $this->environment()->endpoint($service);
        $credentials = array_merge(
            ('' !== $usernameKey) ? [$usernameKey => $this->username()] : [],
            ('' !== $passwordKey) ? [$passwordKey => $this->password()] : []
        );
        return $this->soapFactory()->createSoapCaller($wsdlLocation, $credentials);
    }
}
