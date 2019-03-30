<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Factories;

use DateTimeImmutable;
use DateTimeZone;
use PhpCfdi\Finkok\Tests\TestCase;

class RandomPreCfdi
{
    public function createDateFromString(string $dateExpression): DateTimeImmutable
    {
        return new DateTimeImmutable($dateExpression, new DateTimeZone('America/Mexico_City'));
    }

    public function createHelper(): PreCfdiCreatorHelper
    {
        return new PreCfdiCreatorHelper(
            TestCase::filePath('certs/TCM970625MB1.cer'),
            TestCase::filePath('certs/TCM970625MB1.key.pem'),
            trim(TestCase::fileContentPath('certs/TCM970625MB1.password.bin'))
        );
    }

    public function createValid(): string
    {
        return $this->createHelper()->create();
    }

    public function createInvalidByDate(): string
    {
        $helper = $this->createHelper();
        $helper->setInvoiceDate($this->createDateFromString('now -73 hours'));
        return $helper->create();
    }
}
