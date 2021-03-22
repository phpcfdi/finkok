# phpcfdi/finkok To Do List

- Agregar la ejecución de test de integración al flujo de trabajo `.github/workflows/build.yml`;
  es necesario entender cómo funcionan los secretos para poder crear un archivo de entorno seguro.

- Investigar cómo validar firma en acuses y respuestas del SAT

- Crear un namespace común porque hay clases que están interrelacionadas entre el estampado y cancelación
  de cfdi y de retenciones. Así como las clases abstractas de colecciones y resultados.
  Esto creará una incompatilidad con versiones previas.

- Agregar la integración de CFDI de retenciones y pagos

- Poder transformar un objeto de tipo `GetSatStatusResult` a `PhpCfdi\SatEstadoCfdi\CfdiStatus`.

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

- Las pruebas de servicios no están verificando a dónde se está enviando la solicitud, por lo que podría existir un
  error al crear el endpoint, el error saldría a la luz en pruebas de integración pero no en pruebas unitarias.
  Se puede agregar un código como el siguiente (en `...\Tests\Unit\Services\Retentions\CancelSignatureServiceTest`):
  `$this->assertStringEndsWith(Services::retentions()->value(), $soapFactory->latestWsdlLocation);`

## Documentación

- Documentar los métodos de `QuickFinkok`
- Servicios:
    - Servicios que reintentan por errores de Finkok
    - Parámetros added y coupon de registration add
