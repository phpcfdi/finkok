# CHANGELOG

:us: changes are documented in spanish as it help intented audience to follow changes

:mexico: los cambios están documentados en español para mejor entendimiento

Nos apegamos a [SEMVER](SEMVER.md), revisa la información para entender mejor el control de versiones.

## Version 0.2.1 2019-10-25

- Se mejora la experiencia de uso de la librería con la clase `QuickFinkok`, contiene una serie de métodos
  que crean el *comando*, el *servicio*, ejecutan el *servicio* y retornan el *resultado*.
- Todos los métodos de `QuickFinkok` tienen bloques de ayuda con ligas a la documentación oficial de Finkok.
- Se agrega un objeto de ayuda `GetSatStatusExtractor` que utiliza `phpcfdi/cfdi-expresiones` para poder obtener
  los datos necesarios para consultar el estado SAT de de un CFDI 3.3, CFDI 3.2 o RET 1.0.

## Version 0.2.0 2019-10-02

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

- Default parameter value for for parameter `$waitSeconds` of `GetSatStatusService#queryUntilFoundOrTime()`
  changed from `60` to `120`. 

## Version 0.1.1 2019-09-04

- Los nombres de los métodos en `Finkok` algunas veces son los mismos que en los servicios, pero en otras cambia,
  en lugar de cambiar este helper, se le puso la definición correcta de nombres para que invoque el nombre
  correcto en el servicio. Se crearon los test correspondientes para validar que genera un error si el nombre
  no existe y que todos los métodos de invocación existen en sus respectivos servicios. 

## Version 0.1.0 2019-09-04

- Primer versión
