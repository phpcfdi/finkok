# Cancelación de CFDI Finkok

Algunos de los servicios de cancelación en realidad son un puente para conectarse con el SAT.
A diferencia del timbrado que la puede hacer el PAC, la cancelación únicamente la puede hacer el SAT.

Los servicios de paso son:

- `cancel_signature`: Manda cancelar usando una solicitud de cancelación firmada.
- `get_sat_status`: Consulta el estado de un CFDI.
- `accept_reject`: permite al receptor de una factura Aceptar o Rechazar una determinada cancelación.
- `get_pending`: consultar cuantas solicitudes de cancelación tiene pendientes un receptor.
- `get_related`: obtener una lista de los UUIDs relacionados del CFDI que se está intentando cancelar.

Los servicios de ayuda son:

- `cancel`: (*no recomendado*) Manda cancelar pero requiere del certificado, llave privada y contraseña compartida.
  La cancelación firmada la elabora Finkok en tu nombre y realiza `cancel_signature`.
- `get_receipt`: Devuelve el acuse de recibo asociado a un UUID.
- `query_pending_cancellation`: Consulta el *pending buffer*.

Otros servicios:

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

### Cancelación de múltiples folios

Aunque es posible, no lo hagas. Cancela un folio a la vez.

A qué te enfrentas si cancelas múltiples folios en una sola petición:

- Pierdes el control de la cancelación de un CFDI y su acuse.
- Se desconoce qué puede ocurrir cuando se envía una solicitud con múltiples folios y
  uno es cancelable y otro es no cancelable.
- El servicio del SAT frecuentemente se cuelga con peticiones de múltiples folios.
- No existe un ahorro significativo.

### Validaciones de cancelación

Los servicios de cancelación `sign_cancel`, `cancel` y `cancel_signature` tienen una validación previa
a contactar al SAT para presentar la solicitud de cancelación:

> Se verifica el estado de todos los folios enviados, si alguno es no cancelable no presenta la solicitud.

Se le ha comentado a Finkok la posibilidad de incluir una bandera para excluir esta validación, porque se
pueden presentar casos en donde deseas volver a presentar la solicitud simplemente para obtener un acuse
de cancelación firmado por el SAT.

### Acuses

Existen dos tipos de acuses:

- Recepción: El que ocurre cuando el PAC presenta un CFDI firmado al SAT.
- Cancelación: El que ocurre cuando se presenta una solicitud de cancelación al SAT.

Estos acuses son firmados por el SAT, por lo que son inviolables, infalsificables e irrepudiables.

No encuentro el caso en donde pudiera requerir un acuse de recepción. El PAC firma el CFDI y *es su deber*
enviarlo al SAT, el SAT le responde con este acuse. Si por alguna extraña razón, el SAT diera por desconocido
el CFDI, yo muestro la firma del PAC y con eso sería suficiente. El PAC puede utilizar ese comprobante
para asegurarle al SAT que se lo entregó *y que lo recibió*.

El CFDI de cancelación representa la respuesta del SAT a una solicitud de cancelación.
Dicha solicitud no la hace el PAC (como el Timbre Fiscal Digital), esta solicitud es hecha por el contribuyente
y se firma con su llave privada e incluye el certificado y la llave pública.
Si por alguna extraña razón, el SAT diera por desconocida una cancelación o por cancelado un CFDI,
la única forma de poder argumentar contra el SAT es con el acuse.
Por lo tanto, por seguridad fiscal, sí es muy importante almacenar el acuse, y no es responsabilidad del PAC
almacenarlos por el contribuyente, es responsabilidad del contribuyente contar con ellos.

### Servicio Finkok Get_Sat_Status

Este servicio no se encuentra debidamente documentado.

Si se encuentra un error que reporta que la expresión no se encuentra bien formada puede ser porque alguno
de los componentes que conforman esta operación es incorrecto.

En una prueba, estableciendo el valor de total a un valor incorrecto, la respuesta encontrada indica:
`CodigoEstatus: N 601 - La expresión impresa proporcionada no es válida.`, `Estado: Vigente` y
`EsCancelable: Cancelable sin aceptación`. El problema es que `Estado` debería decir `No encontrado`.

Desconozco porqué en los parámetros de consulta no se solicitan los últimos 8 caracteres del sello digital
del emisor del comprobante (parte de la expresión impresa en `fe`). Esto indicaría que al PAC no le exigen
todos los datos o bien el PAC los completa con la información que tiene del CFDI, en ese caso, me queda la
duda de ¿porqué entonces no completa toda la expresión y requiere únicamente el UUID?.

### Servicio Finkok Cancel get_pending

Obtiene un listado de UUID que están pendientes por aprobar o denegar. La lista puede estar vacía.

En la documentación de Finkok <https://wiki.finkok.com/doku.php?id=get_pending> solo está documentado
el arreglo `uuids`, sin embargo, también existe la variable `error`.

Al revisar las pruebas de integración, es muy difícil crear un caso automatizado, básicamente porque
toma alrededor de 16 minutos el crear un CFDI y que este aparezca como "Cancelable con autorización".

Desde 2016-05-14 que comencé la implementación, la lista devuelve los UUID
8096FF0F-6C49-41D3-B041-940A9DBBB5F2 y 4B2430D3-9714-4ED8-8084-6347914F93D6.
Lo desconozco, pero podría ser, que esta fuera una respuesta predeterminada.

### Dudas de funcionamiento

Suponiendo que se presenta la solicitud de cancelación por dos folios (A y B),
donde A es cancelable sin autorización y B es no cancelable.

- ¿El SAT responderá con un acuse de cancelación cancelando A (201), pero rechazando B (no_cancelable)?
    R: Se desconoce, se podría hacer una prueba al respecto.
    R: En la primer prueba realizada regresó estado de 201 para ambos CFDI, se está investigando
    R: La respuesta 201 significa que la *solicitud* fue recibida, no que el CFDI fue cancelado.

Acerca del servicio `Get_Receipt`:
 
- Solo almacenan los acuses positivos o almacenan todos los acuses
    R: Finkok solo almacena los acuses positivos (estados 201 y 202).

- ¿Cuál es el acuse de tipo "R - Recepción" y "C - Cancelación"?
    R es el acuse de recepción de un CFDI timbrado.
    C es el acuse de recepción de un CFDI cancelado.

- Si se hubiera presentado la cancelación múltiples veces, se generaría un acuse de cancelación
  por cada solicitud con estados `201` y `202`.
  ¿Se devuelve sólo el último acuse con respuesta 202 o el acuse con respuesta 201 donde se canceló por primera vez?
    R: Se devuelve sólo el último

Para los servicios de pasarela, si no se pudo contactar al SAT, se devuelve `708`?
    R: No, existen varios mensajes de error e incluso excepciones.
    Finkok está analizando el tema para unificarlas.
