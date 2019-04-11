# Cancelación de CFDI Finkok

Los servicios de cancelación en realidad son un puente para conectarse con el SAT.
A diferencia del timbrado que la puede hacer el PAC, la cancelación únicamente la puede hacer el SAT.

Los servicios de paso son:

- `cancel_signature`: Manda cancelar usando una solicitud de cancelación firmada.
- `get_sat_status`: Consulta el estado de un CFDI.

Los servicios de ayuda son:

- `cancel`: Manda cancelar pero requiere del certificado, llave privada y contraseña compartida.
  La cancelación firmada la elabora Finkok en tu nombre y realiza `cancel_signature`.
- `get_receipt`: Devuelve el acuse de recibo asociado a un UUID.
- `query_pending_cancellation`: Consulta el *pending buffer*.

Otros servicios:

- `accept_reject`: permite al receptor de una factura Aceptar o Rechazar una determinada cancelación.
- `get_pending`: consultar cuantas solicitudes de cancelación tiene pendientes un receptor.
- `get_related`: obtener una lista de los UUIDs relacionados del CFDI que se está intentando cancelar.
- `sign_cancel`: (*no recomendado*) cancelar uno o varios CFDI, las credenciales se cargaron en el panel de Finkok.

Métodos especiales para trabajar con cancelaciones hechas por terceros:

- `get_out_pending`
- `get_out_related`
- `get_out_sat_status`
- `out_accept_reject`
- `out_cancel`

### Documentación

Documentación del servicio: <https://wiki.finkok.com/doku.php?id=cancel>

### Respuestas de cancelación por UUID

Estas son las respuestas que puede dar el SAT para cada uno de los UUID incluídos en la solicitud.
<https://wiki.finkok.com/doku.php?id=tipificacion#validacion_de_la_cancelacion_del_cfdi> 

* no_cancelable - El UUID contiene CFDI relacionados
* 201 - Petición de cancelación realizada exitosamente
* 202 - Petición de cancelación realizada Previamente
* 203 - No corresponde el RFC del Emisor y de quien solicita la cancelación
* 205 - UUID No encontrado

Si hubiera un problema en la solicitud, por ejemplo, un error de conexión con el SAT, devolverá
para toda la solicitud y se considera como no presentada:

* 708: No se pudo conectar al SAT (ver *pending buffer*)
* 711: Error con el certificado al cancelar

### Pending buffer

Finkok tiene una característica adicional llamado *Pending buffer*, que trataré de explicar a continuación
como una *cola de reintentos*:

Cuando se intenta hacer una cancelación, si por algún motivo no se pudieron contactar los servicios del SAT
`708: No se pudo conectar al SAT` entonces se puede almacenar la solicitud de cancelación en una
*cola de reintentos*. Esta cola de reintentos será procesada y se dejará de reintentar hasta que se deje de
presentar el error `708`.

Si deseas usar esta característica, al enviar la solicitud de cancelación debes establecer el parámetro
`store_pending` a `true` disponible en los métodos `cancel_signature` y `cancel`.

Siempre que uses el *Pending buffer* deberás utilizar el servicio `query_pending_cancellation`,
que precísamente consulta el *pending buffer* para obtener el estado de la cancelación de una
solicitud que se quedo pendiente de cancelar debido a una falla en el sistema de SAT.

### Dudas de funcionamiento

Suponiendo que se presenta la solicitud de cancelación por dos folios (A y B), donde A es cancelable sin autorización
y B es no cancelable.

- ¿El SAT responderá con un acuse de cancelación cancelando A (201), pero rechazando B (no_cancelable)?

Acerca del servicio `Get_Receipt`:
 
- ¿Es un servicio de pasarela al SAT, o es un servicio de Finkok?
- ¿Cuál es el acuse de tipo "R - Recepción"?
- Si se hubiera presentado la cancelación múltiples veces, se generaría un acuse de cancelación
  por cada solicitud con estados `201` y `202`
  ¿Se devuelve sólo el último acuse con respuesta 202 o el acuse con respuesta 201 donde se canceló por primera vez?
- Como se pueden cancelar múltiples folios y se solicitan los acuses por UUID,
  si se presenta una cancelación por diferentes folios con UUID A, B y C,
  al consultar los acuses de forma individual
  ¿se retornaría siempre el mismo acuse?
 
