# phpcfdi/finkok To Do List

- Los reportes que devuelven una cuenta deberían retornar un entero

- Al usar get_contracts, decodificar el base64

- La forma en que están hechos los objetos result es mezclada, algunas propiedades las obtiene cuando se solicitan
  y otras propiedades las obtiene en la creación del objeto. El problema es que se guarda la referencia al objeto
  stdClass de entrada, por lo que podría ser manipulado externamente y devolver resultados diferentes.
  Esto al fin de cuentas es un error de consistencia, pues o bien todas las propiedades se deben establecer en
  el constructor o bien las propiedades deben consultarse al momento de leerlas.
  La primera opción genera duplicidad de memoria (los valores están en el objeto result copiados del input).
  La segunda opción genera mutabilidad al poderse manipular el input.
  La tercera opción es no permitir manipular en input una vez que está dentro del resultado.

- Integrar la aceptación y el rechazo de una solicitud de cancelación.

- Fortalecer los comandos como DownloadXml (get_xml) que el tipo solo puede ser I - CFDI o R - Retenciones

## Documentación

- Servicios:
    - Servicios que reintentan por errores de Finkok
    - Parámetros added y coupon de registration add
    
- Cómo ejecutar las pruebas
    - Configuración del entorno
    - Pruebas unitarias
    - Pruebas de integración
