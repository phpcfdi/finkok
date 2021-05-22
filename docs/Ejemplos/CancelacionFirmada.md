# Ejemplo de cancelación

Para este ejemplo de cancelación partiremos del CSD `certificado.cer`,
`llave-privada.key.pem` y la contraseña `12345678a` y enviaremos la solicitud
de cancelación del CFDI `11111111-2222-3333-4444-000000000001` del RFC `EKU9003173C9`.

## Ejemplo usando QuickFinkok (versión 0.2.1)

```php
<?php
declare(strict_types=1);

use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\QuickFinkok;

$uuid = '12345678-1234-1234-1234-000000000001';
$credential = Credential::openFiles('certificado.cer', 'llave-privada.key.pem', '12345678a');
$finkok = new QuickFinkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));

$result = $finkok->cancel($credential, $uuid);
$documentInfo = $result->documents()->first();

echo 'Código de estado de la solicitud de cancelación: ', $result->statusCode();
echo 'UUID: ', $documentInfo->uuid();
echo 'Estado del CFDI: ', $documentInfo->documentStatus();
echo 'Estado de cancelación: ', $documentInfo->cancellationStatus();
```

## Ejemplo usando phpcfdi/xml-cancelacion (versión 0.2.0)

```shell
composer require phpcfdi/finkok
composer require phpcfdi/credentials
```

```php
<?php

declare(strict_types=1);

use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Helpers\CancelSigner;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureCommand;

$cancelHelper = new CancelSigner(['11111111-2222-3333-4444-000000000001']);
$credential = Credential::openFiles('certificado.cer', 'llave-privada.key.pem', '12345678a');
$cancelCommand = new CancelSignatureCommand($cancelHelper->sign($credential));

$finkok = new Finkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));

$result = $finkok->cancelSignature($cancelCommand);
$result->statusCode(); // código de estado de la solucitud de cancelación

```

## Ejemplo usando phpcfdi/xml-cancelacion

Para crear el XML de cancelación está usando [`phpcfdi/xml-cancelacion`](https://github.com/phpcfdi/xml-cancelacion).
Puedes instalar vía composer tanto esta librería como la que genera xml firmados para cancelación.

```shell
composer require phpcfdi/finkok
composer require phpcfdi/xml-cancelacion
```

```php
<?php declare(strict_types=1);

use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Services\Cancel\CancelSignatureCommand;
use PhpCfdi\XmlCancelacion\XmlCancelacionHelper;

$cancelXml = (new XmlCancelacionHelper())
    ->setNewCredentials('certificado.cer', 'llave-privada.key.pem', '12345678a')
    ->signCancellation('11111111-2222-3333-4444-000000000001', new DateTimeImmutable());

$finkok = new Finkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$result = $finkok->cancelSignature(new CancelSignatureCommand($cancelXml));
```
