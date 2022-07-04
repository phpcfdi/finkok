# CHANGELOG

Nos apegamos a [SEMVER](SEMVER.md), revisa la información para entender mejor el control de versiones.

## Versión 0.4.5 2022-07-02

Se agregan pruebas unitarias para CFDI 4.0.

## Versión 0.4.4 2022-06-29

El servidor de producción de Quadrum (para firmar manifiestos) es más estricto que el servidor de pruebas
y no acepta la URL `https://manifiesto.cfdiquadrum.com.mx//servicios/soap/firmar.wsdl`.
En esta versión se elimina la doble diagonal.

## Versión 0.4.3 2022-06-27

Se agrega CFDI 4.0 al extractor de información `GetSatStatusExtractor`.
Con este cambio el servicio `https://wiki.finkok.com/doku.php?id=get_sat_status` ya soporta CFDI 4.0.

Se actualiza [`phpcfdi/cfdi-expresiones`](https://github.com/phpcfdi/cfdi-expresiones/) a la versión 3.2.0.

## Versión 0.4.2 2022-05-30

Se hacen cambios menores y de mantenimiento:

- Se corrige `Finkok::checkCommand` pues podría llamar a la función `is_a` con un parámetro que no es un objeto.
- Se actualizan las versiones de herramientas de desarrollo `phpstan` y `php-cs-fixer`.
- Correcciones al proceso de integración continua `build`:
  - Los trabajos se ejecutan en PHP 8.1.
  - Se agrega PHP 8.1 a la matriz de pruebas.
  - `phpcs` usa los directorios configurados en `phpcs.xml.dist`.
  - Las acciones de github se actualizan a la versión 3.

## Versión 0.4.1 2022-01-11

Se hacen cambios menores y de mantenimiento:

- Se remueven conversiones de tipos innecesarios.
- Se corrige el ejemplo de cancelación usando `QuickFinkok`.
- Se actualizan los tests para un mejor entendimiento y ya no se usan métodos deprecados.
- Se corrige el nombre del grupo mantenedor de PhpCfdi.
- Se cambia el flujo de integración continua de pasos en el trabajo a trabajos separados.

## Versión 0.4.0 2022-01-09

Se actualiza a [`phpcfdi/xml-cancelacion`](https://github.com/phpcfdi/xml-cancelacion) que incluye los
formatos a utilizar para la cancelación 2022 según la nueva especificación del SAT.
Esto provoca cambios importantes en todos los métodos relacionados con la cancelación.

Se elimina `CancelledDocument::cancellationSatatus()`. Se debe usar `CancelledDocument::cancellationStatus()`.

Se actualiza la licencia a 2022. ¡Feliz año!

Se hacen varios cambios al entorno de desarrollo:

- Se agregan nuevos casos para el error de estampado `707`.
- Se cambian las dependencias de desarrollo a `phive`.
- Ya no se usa `\getenv`, en su lugar se pone una función segura en `TestCase::getenv`.
- Se corrigen las incidencias encontradas por PHPStan 1.3.3.

Se incluyen los cambios previos no liberados en una versión:

- 2021-09-26: Fix broken CI. PHPUnit 9.5.10 does not convert deprecations to exceptions by default.

## Versión 0.3.2 2021-05-21

Se renombra la propiedad `CancelledDocument::cancellationSatatus()` en favor de `CancelledDocument::cancellationStatus()`
que no tiene el error de ortografía. Será removida en la versión 0.4.0.

Los siguientes son cambios en desarrollo y no tienen afectación en el código productivo:

- Se actualiza `php-cs-fixer: ^3.0`.
- Se corrigen las extensiones en la configuración de `shivammathur/setup-php` en la acción de construcción.
- Se agrega la construcción de la rama principal en la acción de construcción.
- Se actualiza la configuración de PHPUnit.
- Se cambia la generación de la cobertura de código a la acción de pruebas funciones y se sube a Scrutinizer.

## Versión 0.3.1 2021-03-21

En la versión 0.3.0 se mencionó que la fachada `Finkok::datetime()` podía seguir existiendo, pero es incorrecto.
Es necesario que se le entregue el comando `DatetimeCommand`.
Con este cambio, se elimina el método `datetimePostalCode`.

Cambios en integración contínua:

- Se deja de usar Travis-CI en favor de GitHub Actions.
- Se actualizan los archivos a su versión en español: Código de conducta, contribuciones.
- Se documenta mejor el entorno de pruebas de integración.

Cambios en archivos de proyecto:

- Se actualiza el año de licencia.

## Versión 0.3.0 2021-03-21

- Se elimina el soporte y dependencia de PHP 7.2.
- Se agrega el soporte de PHP 7.3.
- Se actualiza PHPUnit de 8.5 a 9.5.
- `PhpCfdi\Finkok\Services\Utilities\DatetimeService::datetime(DatetimeCommand $command = null)`
  no debe usar la opción de nulo, fue puesta para compatibilidad con versiones previas a `0.2.2`,
  no es así en las fachadas `Finkok` y `QuickFinkok`.
- Limpieza de código:
  - Remover variables locales innecesarias.
  - Remover código innecesario (inicializaciones a null, variables privadas sin uso).
  - Múltiples anotaciones para evitar alertas de PHPStorm.

Cambios en entorno de desarrollo:

- Se corrigen las pruebas de integración porque el sistema de pruebas del SAT reporta errores de sincronización.
- En desarrollo se depende ahora de `eclipxe/cfdiutils` compatible con PHP 8.0.
- Se corrigieron los scripts de `composer.json`.
- Los casos de pruebas son clases finales.
- Corrección de Travis-CI, estaba usando `phpcbf` en lugar de `phpcs`.
- Se agrega PHP 8.0 a la matriz de pruebas de Travis-CI.
- Se elimina la actualización de composer en Scrutinizer, el sistema es de solo lectura.

Cambios en el entorno de pruebas (2020-10-14). Solo se afecta la rama principal, no se libera una nueva versión.

- El build estaba roto por un problema de tipos detectado por PHPStan debido a que a partir de la versión `0.12.54`
  ya detecta las estructuras de control de flujo de PHPUnit.
- Se reportó a Finkok la mala configuración de `manifiesto.cfdiquadrum.com.mx` al no incluir los
  certificados intermedios, lo solucionaron de inmediato: <https://support.finkok.com/support/tickets/46648>.
- Cambios menores en las pruebas.

Cambios en el entorno de pruebas (2020-09-18). Solo se afecta la rama principal, no se libera una nueva versión.

- El build estaba roto por un problema de tipos detectado por `phpstan` debido a un "soft breaking compatibility change"
  introducido por `symfony/dotenv:5.1`, se corrige el problema en `tests/bootstrap.php`.
- Se crea `Finkok\Tests\LoggerPrinter` para facilitar la escritura de los volcados de comunicación.
- Se agrega `tests/stamp-precfdi-devenv.php` para estampar un precfdi usando la configuración del entorno de desarrollo.
- Cambios menores en las pruebas.

## Versión 0.2.7 2020-02-10

- En las pruebas de integración del servicio `get_related_signature` el SAT tarda en vincular los CFDI recientemente
  creados, lo que ocasiona que la prueba falle invariablemente al detectar el error
  `2001 - No Existen cfdi relacionados al folio fiscal.`.
  Se ha modificado la prueba para que, si encuentra dicho error no rompa el ciclo de testeo y lo vuelva a intentar.

## Versión 0.2.6 2020-01-24

- Documentar la solución del problema de acuse recibido al cancelar y al solicitar. Finkok ticket: `#41435`.
- Se agrega el método `StampingAlert::extraInfo()` para obtener la respuesta de la incidencia en `ExtraInfo`.
- Se agrega el método `StampingResult::faultCode()` para obtener la respuesta en `faultcode`.
- Se renombra el método `StampingResult::faultstring()` a `StampingResult::faultString()`.
- Se agrega el servicio de retenciones (para CFDI de retenciones e información de pagos).
- Se crean fábricas básicas de CFDI RET para poder testear.
- Se agregan métodos de retenciones (comando, resultado, servicio y método en `QuickFinkok`):
    - Timbrado de retención: La respuesta tiene campos idénticos al timbrado de CFDI.
    - Cancelación de retención: La respuesta tiene campos idénticos y adicionales al timbrado de CFDI.
    - Para poder cancelar un RET, fue necesario actualizar a `phpcfdi/xml-cancelacion: ^1.1.0`.
- El objeto `GetSatStatusExtractor` podía procesar CFDI 3.2, CFDI 3.3 y RET 1.0, sin embargo, el método `get_sat_status`
  solo puede trabajar con CFDI 3.2, CFDI 3.3, se hacen las adecuaciones correspondientes.
- Desarrollo:
    - Se crearon pruebas unitarias para `QueryPendingCommand` y `QueryPendingResult`.
    - Se reconstruye `createGetSatStatusCommandFromCfdiContents` para que use el helper `GetSatStatusExtractor`.
    - Se mejoran las acciones `resetCustomerAccountToOnDemand` y `resetCustomerAccountToPrepaidWithZeroCredits`.
    - Se actualiza de `phpstan/phpstan-shim: ^0.11` a `phpstan/phpstan: ^0.12`.
    - Se actualiza a `phpunit/phpunit: ^8.5` porque el XSD de la versión previa no está disponible.
    - Se crean nuevas tareas de desarrollo y mejora.
- Issues: A pesar de haber impementado la Cancelación de retención, el test de integración está fallando por un
  error en el servicio de pruebas del SAT.

## Versión 0.2.5 2020-01-14

- Se actualiza el año de licencia 2020.
- Finkok implementó en el registro de clientes el método `switch`, para cambiar al cliente de `Prepago`
  a `Ondemand` y viceversa. Se ha creado el método y se incluyó en los helpers `Finkok` y `QuickFinkok`.
- Se documentó el ticket de Finkok `#41435`: El acuse recibido en el método `get_reciept` es diferente que el
  obtenido en la respuesta de cancelación.
- Se agregó un workaround de test de integración al ticket de Finkok `#41438`: El xml devuelto por `get_xml` no
  contiene la cabecera de xml `<?xml version="1.0" encoding="UTF-8"?>`.
- Cambios de desarrollo y pruebas:
    - Se cambió de `phpstan/phpstan-shim` a `phpstan/phpstan`.
    - Se cambió la versión de `phpstan` de `^0.11` a `^0.12`. Con esto se agregaron muchas definiciones de tipos
      de datos en los bloques de phpdoc.

## Versión 0.2.4 2019-12-05

- Se modifica el test porque en diciembre no se podía crear el escenario para validar la excepción en
  la creación del comando `ReportTotalCommand`. De todas formas se lanzaba una excepción, pero no la esperada.
- Se dotó a `ReportTotalCommand` de un método para devolver la fecha actual, para poder testear.
- `ReportTotalCommand` usa `DateTimeImmutable` en lugar de `DateTime` (cambio interno).
- Se cambia la dependencia de desarrollo para usar `symfony/dotenv` versión `^5.0`.
- Se modificó la carga del archivo de environment porque `symfony/dotenv:5.x` no usa `putenv` por defecto.
- Travis-CI: Se cambió la versión de PHP `7.4snapshot` a `7.4`.
- Scrutinizer-CI: Se agregaron los comandos para actualizar composer e instalar paquetes en lugar de ser inferidos.

## Versión 0.2.3 2019-11-05

- El método `QuickFinkok::customerSignAndSendContracts` no estaba funcionando correctamente porque asumía
  que el contenido de obtener los contratos estaba en texto plano, pero estaba codificado en `base64`.
- La respuesta de obtener contratos automáticamente se decodifica de `base64` a texto plano.
    - Esto afecta a los métodos `GetContractsResult::contract()` y `GetContractsResult::privacy()`
- Se agrega el soporte del servicio para obtener el manifiesto previamente firmado a partir del SNID y el RFC.
    - Servicio: `PhpCfdi\Finkok\Services\Manifest\GetSignedContractsService`
    - Helper `QuickFinkok`: `QuickFinkok::customerGetSignedContracts()`
    - Helper `Finkok`: `Finkok::getSignedContracts()`
- Se actualiza `robrichards/xmlseclibs` a la versión 3.0.4 por el problema de seguridad CVE-2019-3465,
  que aunque no se usa para este propósito se evita depender de esta versión.

## Versión 0.2.2 2019-11-02

- Se agrega el soporte del servicio que obtiene la hora de los servidores de Finkok usando un código postal.
  Si no se especifica un código postal entonces se utiliza el predeterminado que corresponde a la zona horaria
  de `America/Mexico_City`. La hora devuelta no tiene especificación de zona horaria, es decir, no especifica
  cuánto tiempo hay de diferencia entre la hora devuelta y GMT.
- Se utiliza PHPUnit versión `8.4`, desde `8.4.2` las clases de testeo que se extienden sean abstractas.
- Se elimina la dependencia de `overtrue/phplint`.

## Versión 0.2.1 2019-10-25

- Se mejora la experiencia de uso de la librería con la clase `QuickFinkok`, contiene una serie de métodos
  que crean el *comando*, el *servicio*, ejecutan el *servicio* y retornan el *resultado*.
- Todos los métodos de `QuickFinkok` tienen bloques de ayuda con ligas a la documentación oficial de Finkok.
- Se agrega un objeto de ayuda `GetSatStatusExtractor` que utiliza `phpcfdi/cfdi-expresiones` para poder obtener
  los datos necesarios para consultar el estado SAT de un CFDI 3.3, CFDI 3.2 o RET 1.0.

## Versión 0.2.0 2019-10-02

- Implementación del servicio `get_related_signature` que obtiene los UUID relacionados (descendientes y ascendentes)
  de un determinado UUID sin compartir la llave privada.
- Implementación del servicio `accept_reject_signature` que acepta o rechaza la solicitud de cancelación de un UUID
  sin compartir la llave privada.
- Se crean objetos de ayuda para generar las firmas que se requieren para el SAT.
- Se depende ahora de [`phpcfdi/xml-cancelacion:^1.0.1`](https://github.com/phpcfdi/xml-cancelacion) y
  [`phpcfdi/credentials:^1.0.1`](https://github.com/phpcfdi/credentials).
- Se empieza a usar `eclipxe/micro-catalog` para los mensajes conocidos del SAT relacionados con un mensaje
  de aceptación o rechazo de solicitud de cancelación.

BC Changes:

- Default parameter value for parameter `$waitSeconds` of `GetSatStatusService#queryUntilFoundOrTime()`
  changed from `60` to `120`.

## Versión 0.1.1 2019-09-04

- Los nombres de los métodos en `Finkok` algunas veces son los mismos que en los servicios, pero en otras cambia,
  en lugar de cambiar este helper, se le puso la definición correcta de nombres para que invoque el nombre
  correcto en el servicio. Se crearon las pruebas correspondientes para validar que genera un error si el nombre
  no existe y que todos los métodos de invocación existen en sus respectivos servicios.

## Versión 0.1.0 2019-09-04

- Primera versión.
