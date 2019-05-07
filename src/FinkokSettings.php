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

    /** @var string */
    private $usernameKey = 'username';

    /** @var string */
    private $passwordKey = 'password';

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

    public function changeUsernameKey(string $usernameKey): void
    {
        $this->usernameKey = $usernameKey;
    }

    public function changePasswordKey(string $passwordKey): void
    {
        $this->passwordKey = $passwordKey;
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
     * This method created a configured SoapCaller with wsdLocation and default options
     *
     * @param Services $service
     * @return SoapCaller
     * @uses SoapFactory
     */
    public function createCallerForService(Services $service): SoapCaller
    {
        $wsdlLocation = $this->environment()->endpoint($service);
        $credentials = $this->credentialsParameters();
        return $this->soapFactory()->createSoapCaller($wsdlLocation, $credentials);
    }

    public function credentialsParameters(): array
    {
        return [
            $this->usernameKey => $this->username(),
            $this->passwordKey => $this->password(),
        ];
    }
}
