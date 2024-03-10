# Ejemplo de timbrado

Para este ejemplo se asume que ya existe un PreCFDI (CFDI sin timbre fiscal digital) en `$precfdi`.

El resultado del firmado está en `$result` que es de tipo `PhpCfdi\Finkok\Services\Stamping\StampingResult`
y se pueden extraer diferentes propiedades de este firmado como el xml firmado o el listado de alertas.

```php
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\QuickFinkok;

/**
 * @var string $precfdi Para este ejemplo esta variable contiene el CFDI sellado sin el Timbre Fiscal Digital 
 */

$finkok = new QuickFinkok(new FinkokSettings('finkok-usuario', 'finkok-password', FinkokEnvironment::makeProduction()));
$result = $finkok->stamp($precfdi);

echo $result->xml(), PHP_EOL; // precfdi firmado

foreach ($result->alerts() as $alert) {
    echo $alert->errorCode(), ': ', $alert->message(), PHP_EOL; // mensaje de incidencia
}
```
