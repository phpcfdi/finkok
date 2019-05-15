# phpcfdi/finkok To Do List

- Poner el copyright correcto en cuanto esté el sitio de PhpCfdi
- Los reportes que devuelven una cuenta deberían retornar un entero
- Al usar get_contracts, decodificar el base64
- Poner archivo de ejemplo de configuración de entorno en pruebas

- La forma en que están hechos los objetos result es mezclada, algunas propiedades las obtiene cuando se solicitan
  y otras propiedades las obtiene en la creación del objeto. El problema es que se guarda la referencia al objeto
  stdClass de entrada, por lo que podría ser manipulado externamente y devolver resultados diferentes.
  Esto al fin de cuentas es un error de consistencia, pues o bien todas las propiedades se deben establecer en
  el constructor o bien las propiedades deben consultarse al momento de leerlas.
  La primera opción genera duplicidad de memoria (los valores están en el objeto result copiados del input).
  La segunda opción genera mutabilidad al poderse manipular el input.

## Documentación

- Servicios:
    - Servicios que reintentan por errores de Finkok
    - Parámetros added y coupon de registration add
    
- Cómo ejecutar las pruebas
    - Configuración del entorno
    - Pruebas unitarias
    - Pruebas de integración

- Cómo contribuir
