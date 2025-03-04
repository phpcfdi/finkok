<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\Definitions\RfcRole;
use PhpCfdi\XmlCancelacion\Credentials as XmlCancelacionCredentials;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

class GetRelatedSigner
{
    /** @var string */
    public const DEFAULT_PACRFC = 'CVD110412TF6';

    /** @var string */
    private $uuid;

    /** @var RfcRole */
    private $role;

    /** @var string */
    private $pacRfc;

    /**
     * GetRelatedSigner constructor.
     *
     * @param string $uuid
     * @param RfcRole|null $role If null or omitted then uses issuer role
     * @param string $pacRfc If empty or omitted then uses DEFAULT_PACRFC
     */
    public function __construct(string $uuid, RfcRole $role = null, string $pacRfc = self::DEFAULT_PACRFC)
    {
        $this->uuid = $uuid;
        $this->role = $role ?? RfcRole::issuer();
        $this->pacRfc = $pacRfc ?: static::DEFAULT_PACRFC;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function role(): RfcRole
    {
        return $this->role;
    }

    public function pacRfc(): string
    {
        return $this->pacRfc;
    }

    public function sign(Credential $credential): string
    {
        $helper = new XmlCancelacionHelper(XmlCancelacionCredentials::createWithPhpCfdiCredential($credential));
        return $helper->signObtainRelated($this->uuid(), $this->role(), $this->pacRfc());
    }
}
