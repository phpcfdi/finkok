<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Helpers;

use DateTimeImmutable;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\XmlCancelacion\Credentials as XmlCancelacionCredentials;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

class CancelSigner
{
    private CancelDocuments $documents;

    private DateTimeImmutable $dateTime;

    /**
     * CancelSigner constructor
     *
     * @param CancelDocuments $documents
     * @param DateTimeImmutable|null $dateTime If null or ommited then use current time and time zone
     */
    public function __construct(CancelDocuments $documents, ?DateTimeImmutable $dateTime = null)
    {
        $this->documents = $documents;
        $this->dateTime = $dateTime ?? new DateTimeImmutable();
    }

    /** @return CancelDocuments */
    public function documents(): CancelDocuments
    {
        return $this->documents;
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function sign(Credential $credential): string
    {
        $helper = new XmlCancelacionHelper(XmlCancelacionCredentials::createWithPhpCfdiCredential($credential));
        return $helper->signCancellationUuids($this->documents(), $this->dateTime());
    }

    public function signRetention(Credential $credential): string
    {
        $helper = new XmlCancelacionHelper(XmlCancelacionCredentials::createWithPhpCfdiCredential($credential));
        return $helper->signRetentionCancellationUuids($this->documents(), $this->dateTime());
    }
}
