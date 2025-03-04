<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DateTimeImmutable;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\Definitions\CancelAnswer;
use PhpCfdi\XmlCancelacion\Credentials as XmlCancelacionCredentials;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

class AcceptRejectSigner
{
    /** @var string */
    public const DEFAULT_PACRFC = 'CVD110412TF6';

    /** @var string */
    private $uuid;

    /** @var CancelAnswer */
    private $answer;

    /** @var string */
    private $pacRfc;

    /** @var DateTimeImmutable */
    private $dateTime;

    /**
     * GetRelatedSigner constructor.
     *
     * @param string $uuid
     * @param CancelAnswer $answer
     * @param DateTimeImmutable|null $dateTime If null or omitted then use current time and time zone
     * @param string $pacRfc If empty or omitted then uses DEFAULT_PACRFC
     */
    public function __construct(
        string $uuid,
        CancelAnswer $answer,
        ?DateTimeImmutable $dateTime = null,
        string $pacRfc = self::DEFAULT_PACRFC
    ) {
        $this->uuid = $uuid;
        $this->answer = $answer;
        $this->dateTime = $dateTime ?? new DateTimeImmutable();
        $this->pacRfc = $pacRfc ?: static::DEFAULT_PACRFC;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function answer(): CancelAnswer
    {
        return $this->answer;
    }

    public function pacRfc(): string
    {
        return $this->pacRfc;
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function sign(Credential $credential): string
    {
        $helper = new XmlCancelacionHelper(XmlCancelacionCredentials::createWithPhpCfdiCredential($credential));
        return $helper->signCancellationAnswer($this->uuid(), $this->answer(), $this->pacRfc(), $this->dateTime());
    }
}
