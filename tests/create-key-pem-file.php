<?php

declare(strict_types=1);

use CfdiUtils\OpenSSL\OpenSSL;
use PhpCfdi\Finkok\Tests\TestCase;

require __DIR__ . '/bootstrap.php';

$pemfile = TestCase::filePath('certs/TCM970625MB1.key.pem');
if (file_exists($pemfile)) {
    echo "$pemfile already exists\n";
    return;
}

$keyfile = TestCase::filePath('certs/TCM970625MB1.key');
$passPhrase = trim(TestCase::fileContentPath('certs/TCM970625MB1.password.bin'));

$openSsl = new OpenSSL();
$openSsl->derKeyConvert($keyfile, $passPhrase, $pemfile);

echo "$pemfile created\n";
