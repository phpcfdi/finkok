<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Factories;

use DateTimeImmutable;
use DateTimeZone;
use PhpCfdi\Finkok\Tests\TestCase;

final class RandomPreCfdi
{
    public function createDateFromString(string $dateExpression): DateTimeImmutable
    {
        return new DateTimeImmutable($dateExpression, new DateTimeZone('America/Mexico_City'));
    }

    public function createHelper(): PreCfdiCreatorHelper
    {
        return new PreCfdiCreatorHelper(
            TestCase::filePath('certs/EKU9003173C9.cer'),
            TestCase::filePath('certs/EKU9003173C9.key.pem'),
            trim(TestCase::fileContentPath('certs/EKU9003173C9.password.bin'))
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
