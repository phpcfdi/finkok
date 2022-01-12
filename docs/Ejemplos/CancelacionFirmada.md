# Ejemplo de cancelación

Para este ejemplo de cancelación partiremos del CSD `certificado.cer`,
`llave-privada.key.pem` y la contraseña `12345678a` y enviaremos la solicitud
de cancelación del CFDI `11111111-2222-3333-4444-000000000001` del RFC `EKU9003173C9`.

## Ejemplo usando `QuickFinkok`

```php
<?php
declare(strict_types=1);

use PhpCfdi\Credentials\Credential;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\QuickFinkok;
use PhpCfdi\XmlCancelacion\Models\CancelDocument;

// Crear el objeto QuickFinkok
$credential = Credential::openFiles('certificado.cer', 'llave-privada.key.pem', '12345678a');
$quickFinkok = new QuickFinkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));

// Crear el documento a cancelar (cancelado con relación)
$documentToCancel = CancelDocument::newWithErrorsRelated(
    '12345678-1234-1234-1234-000000000001',  // el UUID a cancelar
    '12345678-1234-1234-1234-000000000AAA'   // el UUID que lo sustituye
);

// Presentar la solicitud de cancelación
$result = $quickFinkok->cancel($credential, $documentToCancel);
$documentInfo = $result->documents()->first();

// Trabajar con la respuesta
echo 'Código de estado de la solicitud de cancelación: ', $result->statusCode();
echo 'UUID: ', $documentInfo->uuid();
echo 'Estado del CFDI: ', $documentInfo->documentStatus();
echo 'Estado de cancelación: ', $documentInfo->cancellationStatus();
```

## Ejemplo usando `Finkok` y `CancelSigner`

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
use PhpCfdi\XmlCancelacion\Models\CancelDocument;
use PhpCfdi\XmlCancelacion\Models\CancelDocuments;

$cancelHelper = new CancelSigner(
    new CancelDocuments(CancelDocument::newWithErrorsUnrelated('11111111-2222-3333-4444-000000000001'))
);
$credential = Credential::openFiles('certificado.cer', 'llave-privada.key.pem', '12345678a');
$cancelXml = $cancelHelper->sign($credential);

$finkok = new Finkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$result = $finkok->cancelSignature(new CancelSignatureCommand($cancelXml));
echo $result->statusCode(); // código de estado de la solucitud de cancelación
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
use PhpCfdi\XmlCancelacion\Models\CancelDocument;

$cancelXml = (new XmlCancelacionHelper())
    ->setNewCredentials('certificado.cer', 'llave-privada.key.pem', '12345678a')
    ->signCancellation(CancelDocument::newWithErrorsUnrelated('11111111-2222-3333-4444-000000000001'), new DateTimeImmutable());

$finkok = new Finkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$result = $finkok->cancelSignature(new CancelSignatureCommand($cancelXml));
echo $result->statusCode(); // código de estado de la solucitud de cancelación
```
