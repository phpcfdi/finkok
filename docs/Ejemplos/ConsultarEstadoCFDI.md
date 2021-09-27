# Ejemplo de consulta de estado de un CFDI

Para este ejemplo partiremos de que tenemos un CFDI en el archivo `cfdi.xml`.
Y que los datos son: RFC emisor `EKU9003173C9`, RFC receptor `JES900109Q90`,
total `12345.67` y UUID `11111111-2222-3333-4444-000000000001`.

Nota: El servicio `get_sat_status` solo puede obtener datos de CFDI 3.3 y CFDI 3.2.

## Usando QuickFinkok con el CFDI

Los datos de RFC emisor, receptor, total y UUID se obtienen directamente del CFDI.

```php
<?php
declare(strict_types=1);

use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\QuickFinkok;

$finkok = new QuickFinkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$satStatus = $finkok->satStatusXml(file_get_contents(`cfdi.xml`));

echo $satStatus->query();           // S - Comprobante obtenido satisfactoriamente.
echo $satStatus->cfdi();            // Vigente
echo $satStatus->cancellable();     // Cancelable sin aceptación 
echo $satStatus->cancellation();    // (vacío) 
```

## Usando QuickFinkok con los datos

```php
<?php
declare(strict_types=1);

use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\QuickFinkok;

$rfcEmisor = 'EKU9003173C9';
$rfcReceptor = 'JES900109Q90';
$uuid = '11111111-2222-3333-4444-000000000001';
$total = '12345.67';

$finkok = new QuickFinkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$satStatus = $finkok->satStatus($rfcEmisor, $rfcReceptor, $uuid, $total);

echo $satStatus->query();           // S - Comprobante obtenido satisfactoriamente.
echo $satStatus->cfdi();            // Vigente
echo $satStatus->cancellable();     // Cancelable sin aceptación 
echo $satStatus->cancellation();    // (vacío) 
```

## Usando Finkok

La fachada `Finkok` es un poco más compleja de usar y funciona mejor si se está implementando un *command bus*.
Para cualquier otro caso se recomienda usar `QuickFinkok`.

```php
<?php
declare(strict_types=1);

use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\Services\Cancel\GetSatStatusCommand;

// datos de origen
$rfcEmisor = 'EKU9003173C9';
$rfcReceptor = 'JES900109Q90';
$uuid = '11111111-2222-3333-4444-000000000001';
$total = '12345.67';

// comando
$cmdGetSatStatus = new GetSatStatusCommand($rfcEmisor, $rfcReceptor, $uuid, $total);

// ejecución del comando
$finkok = new Finkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$satStatus = $finkok->getSatStatus($cmdGetSatStatus);
```

