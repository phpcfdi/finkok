# CHANGELOG

:us: changes are documented in spanish as it help intented audience to follow changes

:mexico: los cambios están documentados en español para mejor entendimiento

Nos apegamos a [SEMVER](SEMVER.md), revisa la información para entender mejor el control de versiones.

## Version 0.1.2 2019-09-17

- Implementación del servicio `get_related_signature` que obtiene los UUID relacionados (descendientes y ascendentes)
  de un determinado UUID.


## Version 0.1.1 2019-09-04

- Los nombres de los métodos en `Finkok` algunas veces son los mismos que en los servicios, pero en otras cambia,
  en lugar de cambiar este helper, se le puso la definición correcta de nombres para que invoque el nombre
  correcto en el servicio. Se crearon los test correspondientes para validar que genera un error si el nombre
  no existe y que todos los métodos de invocación existen en sus respectivos servicios. 

## Version 0.1.0 2019-09-04

- Primer versión
