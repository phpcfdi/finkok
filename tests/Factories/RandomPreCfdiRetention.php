<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Factories;

use DateTimeImmutable;
use PhpCfdi\Finkok\Tests\TestCase;

final class RandomPreCfdiRetention
{
    public function createValid(): string
    {
        $amount = random_int(1000000, 10000000);
        $helper = new PreCfdiRetentionCreatorHelper(
            TestCase::filePath('certs/EKU9003173C9.cer'),
            TestCase::filePath('certs/EKU9003173C9.key.pem'),
            trim(TestCase::fileContentPath('certs/EKU9003173C9.password.bin'))
        );
        $helper->setCveReten('14');
        $helper->setInvoiceDate(new DateTimeImmutable());

        $creator = $helper->createRetencionesCreator10();
        $retenciones = $creator->retenciones();
        $retenciones->addPeriodo(['MesIni' => '5', 'MesFin' => '5']);
        $retenciones->addTotales(['montoTotExent' => $amount, 'montoTotOperacion' => $amount]);
        $retenciones->addImpRetenidos(
            ['BaseRet' => '0', 'Impuesto' => '01', 'TipoPagoRet' => 'Pago provisional', 'montoRet' => '0']
        );
        $retenciones->addComplemento(
            $helper->createDividendosDividOUtil([
                'CveTipDivOUtil' => '06', // 06 - Proviene de CUFIN al 31 de diciembre 2013
                'MontISRAcredRetMexico' => '0',
                'MontISRAcredRetExtranjero' => '0',
                'MontRetExtDivExt' => '0',
                'TipoSocDistrDiv' => 'Sociedad Nacional',
                'MontISRAcredNal' => '0',
                'MontDivAcumNal' => '0',
                'MontDivAcumExt' => '0',
            ])
        );

        return $helper->signPrecfdi($creator);
    }
}
