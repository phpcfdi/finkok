
## Descripción

Se espera que al consumir el servicio de estampado `stamp` con un mismo precfdi
se devuelva el CFDI timbrado previamente (mismo uuid).

Tal como dice la documentación en <https://wiki.finkok.com/doku.php?id=stamp#descripcion>:

> Este método recupera un XML que fue timbrado con anterioridad,
> cuando se presenta la incidencia “307 El CFDI Contiene un timbre previo”. 

Y en <https://wiki.finkok.com/doku.php?id=stamp#ejemplo_de_una_respuesta_con_error_307_timbre_previo>

> Cuando ocurre esta incidencia el web sevices de forma automática recupera el XML.

## Error encontrado

La respuesta no contiene la información del UUID timbrado previamente.

En su lugar contiene un `stampResult` con `Incidencia:CodigoError` `307`, el `xml` está vacío.

```json
{
    "stampResult": {
        "xml": "",
        "Incidencias": {
            "Incidencia": [
                {
                    "IdIncidencia": "ID_incidencia",
                    "Uuid": "",
                    "CodigoError": "307",
                    "WorkProcessId": "WorkProcessId",
                    "MensajeIncidencia": "El CFDI contiene un timbre previo",
                    "ExtraInfo": "",
                    "NoCertificadoPac": "",
                    "FechaRegistro": "2019-03-31T15:50:14"
                },
                {
                    "IdIncidencia": "ID_incidencia",
                    "Uuid": "",
                    "CodigoError": "307",
                    "WorkProcessId": "WorkProcessId",
                    "MensajeIncidencia": "El CFDI contiene un timbre previo",
                    "ExtraInfo": "",
                    "NoCertificadoPac": "",
                    "FechaRegistro": "2019-03-31T15:50:14"
                }
            ]
        }
    }
}
```

El servicio `stamped` tampoco puede encontrar el primer CFDI recién estampado.
Debe reintentarlo aproximadamente 4 segundos hasta que lo recupera correctamente.
Mientras tanto devuelve: `603: El CFDI no contiene un timbre previo`.

Esto parece dar más claridad al error:

- Se genera el estampado del PRECFDI e inmediatamente se llama a:
    - `stamped`: El CFDI no contiene un timbre previo
    - `stamp`: El CFDI contiene un timbre previo
- ...después de algunos segundos:
    - `stamped`: Lo encuentra y lo devuelve
    - `stamp`: Lo encuentra y lo devuelve

Por lo tanto, parece que en realidad el problema consiste en que internamente Finkok sí reporta que el CFDI fue creado
pero no lo pone a disposición para poderlo recuperar.

Los test se pueden ver en 


## Reporte

2019-03-31 16:10 <https://support.finkok.com/support/tickets/17287>
