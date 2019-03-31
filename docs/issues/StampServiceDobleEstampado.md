
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


