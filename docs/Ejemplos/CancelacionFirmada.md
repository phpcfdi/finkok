# Ejemplo de cancelación

Para este ejemplo de cancelación partiremos del CSD `certificado.cer`,
`llaveprivada.key.pem` y la contraseña `12345678a` y enviaremos la solicitud
de cancelación del CFDI `11111111-2222-3333-4444-000000000001` del RFC `EKU9003173C9`.

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
    ->setNewCredentials('EKU9003173C9.cer', 'EKU9003173C9.key.pem', '12345678a')
    ->signCancellation('11111111-2222-3333-4444-000000000001', new DateTimeImmutable());

$finkok = new Finkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$result = $finkok->cancelSignature(new CancelSignatureCommand($cancelXml));
```
