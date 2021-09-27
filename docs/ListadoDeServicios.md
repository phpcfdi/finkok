# Listado de servicios de Finkok

## Timbrado de CFDI

Servicios implementados de estampado:

- [X] `Stamp`: Firma un CFDI, si fue firmado previamente retorna (a veces) el timbrado previo
- [X] `QuickStamp`: Firma un CFDI, si fue firmado previamente retorna un error
- [X] `QueryPending`: Consultar el estatus de una factura que se quedo pendiente de enviar al SAT (falla o quickstamp)
- [X] `Stamped`: Regresa la información de un XML timbrado previamente

## Cancelación

Servicios implementados de cancelación:

- [X] `cancel_signature`: Manda cancelar usando una solicitud de cancelación firmada.
- [X] `get_receipt`: Devuelve el acuse de recibo asociado a un UUID.
- [X] `get_sat_status`: Consulta el estado de un CFDI.

Servicios para trabajar con solicitudes de cancelación:

- [X] `accept_reject_signature`: permite al receptor de una factura Aceptar o Rechazar una determinada cancelación.
- [X] `get_pending`: consultar la lista de los UUID pendientes por cancelar que tiene el receptor.
- [X] `get_related_signature`: obtener una lista de los UUID relacionados del CFDI que se está intentando cancelar.

Servicios para trabajar cancelaciones con CFDI de otro PAC

- [ ] `get_out_pending`
- [ ] `get_out_related`
- [ ] `get_out_sat_status`
- [ ] `out_accept_reject`
- [ ] `out_cancel`

## Utilerías

- [X] `datetime`: Obtiene la hora de los servidores de Finkok
- [X] `get_xml`: Obtiene un CFDI firmado usando su UUID (de los últimos 3 meses)
- [X] `report_credit`: Obtener un reporte por RFC de los créditos añadidos
- [X] `report_total`: Obtener un reporte por RFC del total de timbres consumidos por fechas
- [X] `report_uuid`: Obtener un reporte de UUID con fecha de emisión por RFC

## Registro de clientes

- [X] `assign`: Asignar créditos a un cliente que va a timbrar bajo la cuenta de un socio de negocios de Finkok
- [X] `add`: Agregar un cliente que va a timbrar bajo la cuenta de un socio de negocios de Finkok
- [X] `edit`: Editar el estatus de un cliente, como lo es suspender o activar
- [X] `get`: Listado o el status del RFC Emisor que esté ingresando y tenga registrado en su cuenta
- [X] `switch`: Cambia el tipo de cliente de prepago a ilimitado y viceversa

## Tokens

- [ ] `add_token`: crea un token (usuario)
- [ ] `reset_token`: cambia el passphrase token
- [ ] `update_token`: cambia el estado del token a activo o inactivo

## Retenciones

Estos servicios son de CFDI de retenciones e información de pagos (RET).

- [X] `stamp`: Firma un CFDI RET, si fue firmado previamente retorna el timbrado previo.
- [ ] `stamped`: Regresa la información de un XML timbrado previamente.
- [ ] `cancel_signature`: Cancela un CFDI RET (el modelo de cancelación es el de CFDI 3.2).
- [ ] `get_receipt`: Devuelve el acuse de recibo asociado a un UUID.

## Manifiesto de Finkok

- [X] `get_contracts`: Obtiene los textos para ser firmados
- [X] `sign_contract`: Envía los textos firmados con la FIEL

## Servicios que no se implementarán

No se implementan estos servicios porque utilizan la llave privada y contraseña de un CSD.

Se han implementado los servicios análogos que permiten realizar estas tareas enviando los XML firmados.

- `Sing_Stamp` (timbrado cfdi): Crea el sello y firma un CFDI con llave privada y contraseña compartida.
- `cancel` (cancelación cfdi): Cancelación de CFDI regular.
- `cancel` (retenciones): Cancelación de CFDI de retenciones.
- `sign_cancel` (cancelación cfdi): Cancelación de CFDI regular con llave privada y contraseña compartida.
- `accept_reject` (cancelación cfdi): Aceptar o rechazar la cancelación de un UUID.
- `get_related` (cancelación cfdi): Obtiene los UUID relacionados de un UUID.
