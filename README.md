# phpcfdi/finkok

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

> Librería para conectar con la API de servicios de FINKOK (México)

:us: The documentation of this project is in spanish as this is the natural language for intented audience.

## Acerca de phpcfdi/finkok

Esta librería es un esfuerzo de la comunidad de <https://www.phpcfdi.com/> para tener un cliente que explote
las funcionalidades ofrecidas por el integrador <https://www.finkok.com/>.

No está relacionado con Finkok y Finkok es una marca registrada de FINKOK, SAPI DE CV.

## Instalación

Usa [composer](https://getcomposer.org/)

```shell
composer require phpcfdi/finkok
```

## Ejemplo básico de uso

```php
<?php

use PhpCfdi\Finkok\Finkok;
use PhpCfdi\Finkok\FinkokEnvironment;
use PhpCfdi\Finkok\FinkokSettings;
use PhpCfdi\Finkok\Services\Stamping\StampingCommand;

$settings = new FinkokSettings('user@host.com', 'secret', FinkokEnvironment::makeProduction());
$finkok = new Finkok($settings);

// el PreCFDI a firmar, podría venir de CfdiUtils ;) $creator->asXml()
$precfdi = file_get_contents('precfdi-to-sign.xml');

$stampResult = $finkok->stamp(new StampingCommand($precfdi)); // <- aquí contactamos a Finkok

if ($stampResult->hasAlerts()) { // stamp es un objeto con propiedades nombradas
    foreach ($stampResult->alerts() as $alert) {
        echo $alert->id() . ' - ' . $alert->message() . PHP_EOL;
    }
} else {
    file_put_contents($stampResult->uuid() . '.xml', $stampResult->xml()); // CFDI firmado
}
```

Y también hay otros ejemplos explicados:

- [Cancelación de un UUID firmada](docs/Ejemplos/CancelacionFirmada.md)

Y todos los test de integración, donde se prueba la comunicación y respuestas contra la plataforma de pruebas.

## Métodos implementados

La librería utiliza un modelo basado en comando, servicio y resultado.
El *comando* es la definición de la acción que queremos realizar, contiene todos los parámetros necesarios.
El *servicio* es el encargado de usar ese comando como entrada, ejecutarlo en Finkok (vía SOAP) y
construir un resultado a partir de la respuesta.
El *resultado* son los datos que representa la respuesta. 

No hemos implementado intencionalmente los comandos que requieren transmitir la llave privada de un
CSD (Certificado de Sello Digital) o de la eFirma/FIEL (Firma electrónica).
No creemos que vayamos a implementarlos porque a) No es necesario y b) Es inseguro.

### Servicios de estampado

Finkok tiene dos métodos de firmado: `stamp` y `quickstamp`.

* `stamp(Stamping\StampingCommand $command): Stamping\StampingResult`
* `quickstamp(Stamping\StampingCommand $command): Stamping\StampingResult`

`stamped` para revisar si previamente se generó un cfdi

* `stamped(Stamping\StampingCommand $command): Stamping\StampingResult`

y `stampQueryPending` por si estás usando el `pending buffer` (que te recomiendo no hacerlo).

* `stampQueryPending(Stamping\QueryPendingCommand $command): Stamping\QueryPendingResult`

### Servicios de cancelación

Solo se pueden cancelar cfdi con esta librería usando `cancelSignature` porque es el único
donde no tienes que transmitir información confidencial.

* `cancelSignature(Cancel\CancelSignatureCommand $command): Cancel\CancelSignatureResult`

Puedes consultar el estado de un CFDI usando `getSatStatus` (antes o después de cancelarlo)

* `getSatStatus(Cancel\GetSatStatusCommand $command): Cancel\GetSatStatusResult`

Y obtener el último acuse de recibo del SAT a una solicitud de cancelación con `getCancelReceipt`.
Aunque recuerda que tener un acuse no significa que se haya cancelado, el acuse lo único que
contiene es la respuesta de cancelación presentada al SAT. 

* `getCancelReceipt(Cancel\GetReceiptResult $command): Cancel\GetReceiptResult`

Gracias a `getPendingToCancel` se puede obtener el listado de CFDI pendientes por cancelar por
parte de un receptor. Próximanente implementaremos los métodos para aceptar y rechazar una solicitud.

* `getPendingToCancel(Cancel\GetPendingCommand $command): Cancel\GetPendingResult`

### Utilerías y manejo de clientes

Obtener la hora de Finkok (por si estás teniendo problemas de CFDI fuera de tiempo):

* `datetime(): Utilities\DatetimeResult`

Obtener un CFDI firmado con Finkok en los últimos 3 meses:

* `downloadXml(Utilities\DownloadXmlCommand $command): Utilities\DownloadXmlResult`

Obtener reportes de consumo de crédito y manejo de clientes:

* `reportCredit(Utilities\ReportCreditCommand $command): Utilities\ReportCreditResult`
* `reportTotal(Utilities\ReportTotalCommand $command): Utilities\ReportTotalResult`
* `reportUuid(Utilities\ReportUuidCommand $command): Utilities\ReportUuidResult`
* `registrationAdd(Registration\AddCommand $command): Registration\AddResult`
* `registrationAssign(Registration\AssignCommand $command): Registration\AssignResult`
* `registrationEdit(Registration\EditCommand $command): Registration\EditResult`
* `registrationObtain(Registration\ObtainCommand $command): Registration\ObtainResult`

### Manifiestos y contrato

Para obtener los contratos y enviar las firmas están `getContracts` y `signContracts`
respectivamente. 

* `getContracts(Manifest\GetContractsCommand $command): Manifest\GetContractsResult`
* `signContracts(Manifest\SignContractsCommand $command): Manifest\SignContractsResult`

Y para firmar un contrato con XMLSEC conforme a Finkok tenemos la gran ayuda de la clase `DocumentSigner`.

## Notas de implementación

Durante el proceso de implementación he creado diversas notas y documentos:

- [Cancelación](docs/Cancelación.md): Información del proceso de cancelación, métodos, acuses, pending buffer, etc.

- [Servicios](docs/Servicios.md): Documentación básica de servicios.

- [Listado de servicios](docs/ListadoDeServicios.md): Listado de todos los servicios disponibles de Finkok y si están
  o no implementados, así como un listado de los servicios que no se implementarán.

- [Registro de clientes](docs/RegistroDeClientes.md): Si vas a trabajar con la capacidad de Finkok de *sub-distribuidor*
  y así poder administrar los datos de clientes.

- [Entorno de Pruebas](docs/EntornoDePruebas.md): Documentación de cómo funciona y como configurar el entorno de pruebas.

- Problemas encontrados:
    - [X] [Cancelación de un CFDI recién creado](docs/issues/CancelSignatureServiceCancelarRecienCreado.md)
    - [X] [Consumir queryPending con un CFDI recién creado](docs/issues/QueryPendingServiceUuidNoExistente.md)
    - [X] [Consumir stamp para generar un doble estampado no devuelve los datos](docs/issues/StampServiceDobleEstampado.md)
    - [ ] Falta servicio que no requiera CSD/FIEL para aceptar o rechazar una solicitud de cancelación
    - [ ] Falta servicio que no requiera CSD/FIEL para obtener los CFDI relacionados

## Compatilibilidad

Esta librería se mantendrá compatible con al menos la versión con
[soporte activo de PHP](http://php.net/supported-versions.php) más reciente.

También utilizamos [Versionado Semántico 2.0.0](docs/SEMVER.md) por lo que puedes usar esta librería
sin temor a romper tu aplicación.

## Contribuciones

Las contribuciones con bienvenidas. Por favor lee [CONTRIBUTING][] para más detalles
y recuerda revisar el archivo de tareas pendientes [TODO][] y el [CHANGELOG][].

## Copyright and License

The phpcfdi/finkok library is copyright © [PhpCfdi](https://github.com/phpcfdi)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/phpcfdi/finkok/blob/master/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/finkok/blob/master/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/finkok/blob/master/docs/TODO.md

[source]: https://github.com/phpcfdi/finkok
[release]: https://github.com/phpcfdi/finkok/releases
[license]: https://github.com/phpcfdi/finkok/blob/master/LICENSE
[build]: https://travis-ci.org/phpcfdi/finkok?branch=master
[quality]: https://scrutinizer-ci.com/g/phpcfdi/finkok/
[coverage]: https://scrutinizer-ci.com/g/phpcfdi/finkok/code-structure/master/code-coverage/src
[downloads]: https://packagist.org/packages/phpcfdi/finkok

[badge-source]: http://img.shields.io/badge/source-phpcfdi/finkok-blue?style=flat-square
[badge-release]: https://img.shields.io/github/release/phpcfdi/finkok?style=flat-square
[badge-license]: https://img.shields.io/github/license/phpcfdi/finkok?style=flat-square
[badge-build]: https://img.shields.io/travis/phpcfdi/finkok/master?style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/phpcfdi/finkok/master?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/phpcfdi/finkok/master?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/finkok?style=flat-square
