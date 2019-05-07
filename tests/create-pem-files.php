<?php

declare(strict_types=1);

use CfdiUtils\OpenSSL\OpenSSL;
use PhpCfdi\Finkok\Tests\TestCase;

require __DIR__ . '/bootstrap.php';

$cerFile = TestCase::filePath('certs/TCM970625MB1.cer');
$cerFilePem = TestCase::filePath('certs/TCM970625MB1.cer.pem');
$keyFile = TestCase::filePath('certs/TCM970625MB1.key');
$passPhrase = trim(TestCase::fileContentPath('certs/TCM970625MB1.password.bin'));
$keyFilePem = TestCase::filePath('certs/TCM970625MB1.key.pem');

$openSsl = new OpenSSL();

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
