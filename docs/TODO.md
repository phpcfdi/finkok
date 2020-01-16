# phpcfdi/finkok To Do List

- Revisar el estado del ticket https://support.finkok.com/support/tickets/41435 y modificar el test
  `CancelServicesTest::testCreateCfdiThenGetSatStatusThenCancelSignatureThenGetReceipt`.

- Agregar la integración de CFDI de retenciones y pagos

- Los reportes que devuelven una cuenta deberían retornar un entero

- La forma en que están hechos los objetos result es mezclada, algunas propiedades las obtiene cuando se solicitan
  y otras propiedades las obtiene en la creación del objeto. El problema es que se guarda la referencia al objeto
  stdClass de entrada, por lo que podría ser manipulado externamente y devolver resultados diferentes.
  Esto al fin de cuentas es un error de consistencia, pues o bien todas las propiedades se deben establecer en
  el constructor o bien las propiedades deben consultarse al momento de leerlas.
  La primera opción genera duplicidad de memoria (los valores están en el objeto result copiados del input).
  La segunda opción genera mutabilidad al poderse manipular el input.
  La tercera opción es no permitir manipular en input una vez que está dentro del resultado.

- Fortalecer los comandos como DownloadXml (get_xml) que el tipo solo puede ser I - CFDI o R - Retenciones

- Poder configurar en Travis CI la ejecución de tests de integración

- AcceptRejectSigner debería permitir aceptar y/o rechazar más de 1 solo UUID a la vez

- Agregar un caso para hacer una prueba positiva de AcceptRejectSignatureService.
  Para hacer esta prueba se requieren 2 RFC (A y B), en este momento solo tenemos 1.
  Crear un CFDI donde A es Emisor y B es Receptor que requiera autorización (por ejemplo, por monto)
  A hace la solicitud de cancelación
  B hace la consulta de pendientes y ve el UUID
  B acepta la cancelación 
  B hace la consulta de pendientes y ya no ve el UUID
  Se consulta el estado del UUID y está cancelado

## Documentación

- Servicios:
    - Servicios que reintentan por errores de Finkok
    - Parámetros added y coupon de registration add
