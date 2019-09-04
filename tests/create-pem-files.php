<?php

declare(strict_types=1);

use CfdiUtils\OpenSSL\OpenSSL;
use PhpCfdi\Finkok\Tests\TestCase;

require __DIR__ . '/bootstrap.php';

$openSsl = new OpenSSL();

$converter = function (string $cerFile, string $keyFile, string $passPhrase) use ($openSsl): void {
    $cerFilePem = $cerFile . '.pem';
    $keyFilePem = $keyFile . '.pem';
    if (file_exists($cerFilePem)) {
        echo "$cerFilePem already exists\n";
    } else {
        $openSsl->derCerConvert($cerFile, $cerFilePem);
        echo "$cerFilePem created\n";
    }

    if (file_exists($keyFilePem)) {
        echo "$keyFilePem already exists\n";
    } else {
        $openSsl->derKeyProtect($keyFile, $passPhrase, $keyFilePem, $passPhrase);
        echo "$keyFilePem created\n";
    }
};

// CSD
$converter(
    TestCase::filePath('certs/EKU9003173C9.cer'),
    TestCase::filePath('certs/EKU9003173C9.key'),
    trim(TestCase::fileContentPath('certs/EKU9003173C9.password.bin'))
);

// FIEL
$converter(
    TestCase::filePath('fiel/EKU9003173C9.cer'),
    TestCase::filePath('fiel/EKU9003173C9.key'),
    trim(TestCase::fileContentPath('fiel/EKU9003173C9.password.bin'))
);
