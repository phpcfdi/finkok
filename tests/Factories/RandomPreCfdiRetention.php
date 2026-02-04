<?php

declare(strict_types=1);

namespace PhpCfdi\Finkok\Tests\Factories;

use CfdiUtils\Utils\Format;
use CfdiUtils\Validate\Assert;
use DateTimeImmutable;
use LogicException;
use PhpCfdi\Finkok\Tests\TestCase;

final class RandomPreCfdiRetention
{
    public function createValid(): string
    {
        $amount = random_int(1000000, 10000000);
        $helper = new PreCfdiRetentionCreatorHelper(
            TestCase::filePath('certs/EKU9003173C9.cer'),
            TestCase::filePath('certs/EKU9003173C9.key.pem'),
            trim(TestCase::fileContentPath('certs/EKU9003173C9.password.bin')),
            'EKU9003173C9',
            'ESCUELA KEMPER URGATE',
            '52000',
            '601',
        );
        $helper->setCveReten('14');
        $helper->setInvoiceDate(new DateTimeImmutable());

        $creator = $helper->createRetencionesCreator20();
        $retenciones = $creator->retenciones();
        $retenciones->addPeriodo([
            'MesIni' => '05',
            'MesFin' => '05',
            'Ejercicio' => intval($helper->getInvoiceDate()->format('Y')) - 1,
        ]);
        $amountRet = Format::number(0.45 * (float)$amount, 2);
        $retenciones->addTotales([
            'MontoTotExent' => $amount,
            'MontoTotOperacion' => $amount,
            'MontoTotGrav' => '0',
            'MontoTotRet' => $amountRet,
        ]);
        $retenciones->addImpRetenidos([
            'BaseRet' => $amount,
            'ImpuestoRet' => '001',
            'TipoPagoRet' => '04',
            'MontoRet' => $amountRet,
        ]);
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
            ]),
        );

        $preCfdi = $helper->signPreCfdi($creator);

        $assets = $creator->validate();
        if ($assets->hasErrors()) {
            throw new LogicException(sprintf(
                'You must fix your RET since its is not valid (%d errors):%s%s',
                count($assets->errors()),
                PHP_EOL,
                implode(PHP_EOL, array_map(fn (Assert $assert): string => rtrim(
                    sprintf('%s - %s: %s', $assert->getCode(), $assert->getTitle(), $assert->getExplanation()),
                    ' :',
                ), $assets->errors())),
            ));
        }

        return $preCfdi;
    }
}
