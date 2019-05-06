
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

- [ ] `accept_reject`: permite al receptor de una factura Aceptar o Rechazar una determinada cancelación.
- [ ] `get_pending`: consultar cuantas solicitudes de cancelación tiene pendientes un receptor.
- [ ] `get_related`: obtener una lista de los UUIDs relacionados del CFDI que se está intentando cancelar.

Servicios para trabajar cancelaciones con CFDI de otro PAC

- [ ] `get_out_pending`
- [ ] `get_out_related`
- [ ] `get_out_sat_status`
- [ ] `out_accept_reject`
- [ ] `out_cancel`

## Utilerías

- [X] `datetime`: Obtiene la hora de los servidores de Finkok
- [X] `get_xml`: Obtiene un CFDI firmado usando su UUID (de los últimos 3 meses)
- [ ] `report_credit`: Obtener un reporte por RFC de los créditos añadidos
- [ ] `report_total`: Obtener un reporte por RFC del total de timbres consumidos por fechas
- [ ] `report_uuid`: Obtener un reporte de UUID con fecha de emisión por RFC

## Registro de clientes

- [ ] `add`
- [ ] `edit`
- [ ] `assign`
- [ ] `get`

## Tokens

- [ ] `add_token`: crea un token (usuario)
- [ ] `reset_token`: cambia el passphrase token
- [ ] `update_token`: cambia el estado del token a activo o inactivo

## Retenciones

- [ ] `Stamp`: Firma un CFDI RET, si fue firmado previamente retorna (a veces) el timbrado previo.
- [ ] `Stamped`:  Regresa la información de un XML timbrado previamente.
- [ ] `cancel_signature`: (implementado en entorno demo)
- [ ] `get_receipt`: Devuelve el acuse de recibo asociado a un UUID.

## Manifiesto de Finkok

- [ ] `get_contracts`: Obtiene los textos para ser firmados
- [ ] `sign_contract`: Envía los textos firmados con la FIEL

## Servicios que no se implementarán

- `Sing_Stamp` (timbrado cfdi): Crea el sello y firma un CFDI, llave privada y contraseña compartida.
- `cancel` (cancelación cfdi): Manda cancelar pero requiere envío del certificado, llave privada y contraseña compartida.
- `cancel` (retenciones): Manda cancelar pero requiere envío del certificado, llave privada y contraseña compartida.
- `sign_cancel` (cancelación cfdi): Manda cancelar pero requiere certificado, llave privada y contraseña compartida.
