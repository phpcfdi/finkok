# Servicios

La librería de Finkok debe cubrir la mayor cantidad de servicios ofrecidos por la API de Finkok.
Para acceder a la documentación de Finkok requieres usuario y contraseña (bad finkok, bad!).

## Implementaciones

Para cada servicio que vayamos a consumir usar el patrón de diseño **command handler** con el que:
- El *comando* es un objeto que contiene los parámetros de la acción, lo llamaremos `Command`.
- El *handler* es un objeto que realiza la acción, lo llamaremos `Service`.
- El *invoker* es quien llama a la acción (en este caso, nuestros tests).

Los parámetros de configuración de conexión con finkok deben estar en una clase especializada FinkokSettings
y esta clase debe ser inyectada al `Service`, nunca al `Command`.

Como todas las comunicaciones son usando SOAP, los `Service` también requieren de una fábrica de
objetos de tipo `\SoapClient`.

Por lo tanto, parece que tenemos dos objetos que son relevantes y parece que serán usados siempre:
`SoapClientFactory` y `FinkokSettings`.

Voy a intentar evitar los servicios que signifiquen enviar a Finkok el certificado o llave privada.
Nunca compartas tus certificados y llaves privadas, ni con tu PAC.

## Entornos de producción y pruebas

Otra característica importante es que Finkok tiene dos entornos de trabajo y cada uno tiene sus
réplica de los servicios que ofrece.

- Producción: `https://facturacion.finkok.com`
- Pruebas: `https://demo-facturacion.finkok.com`

## Documentación de servicios

Los servicios se encuentran en <https://wiki.finkok.com/doku.php#documentacion_de_web_services>

- Timbrado: <https://wiki.finkok.com/doku.php?id=wsdl_stamp>
    - [X] Stamp: <https://wiki.finkok.com/doku.php?id=stamp>
    - [X] Quick_stamp: <https://wiki.finkok.com/doku.php?id=metodo_quick_stamp>
    - [X] Stamped: <https://wiki.finkok.com/doku.php?id=stamped>
    - [X] Query_Pending: <https://wiki.finkok.com/doku.php?id=query_pending>
    - [ ] Sing_Stamp: <https://wiki.finkok.com/doku.php?id=sing_stamp>

## Timbrado

Servicios relacionados con la creación de un timbre: Stamp, Quick_stamp y Sing_Stamp.

Stamp y Quick_Stamp hacen lo mismo, con estas dos excepciones:
- Si se hace un *"doble estampado"* `Stamp` retorna (a veces) los datos del CFDI y una incidencia 307,
  mientras que `Quick_Stamp` retorna una incidencia 307.
- `Stamp` no regresa timbrado si no pudo enviarlo al SAT, mientras que `Quick_Stamp` deja el CFDI
  en la cola de envío al SAT.

No recomiendo usar `Quick_stamp` a menos que no se requiera revisar si el SAT tiene el CFDI inmediatamente.
Por ejemplo, si tu aplicación debe hacer consultas inmediatas de estado de CFDI ante el SAT.

### Sing_Stamp

A un PRECFDI sin sello le agrega el atributo `Sello` y el `TimbreFiscalDigital`,
para esto debió enviar previamente su certificado y sello.

No lo voy a implementar por inseguro.
