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
    - Stamp: <https://wiki.finkok.com/doku.php?id=stamp>
    - Quick_stamp: <https://wiki.finkok.com/doku.php?id=metodo_quick_stamp>
    - Stamped: <https://wiki.finkok.com/doku.php?id=stamped>
    - Query_Pending: <https://wiki.finkok.com/doku.php?id=query_pending>
    - Sing_Stamp: <https://wiki.finkok.com/doku.php?id=sing_stamp>

## Timbrado

Servicios relacionados con la creación de un timbre: Stamp, Quick_stamp y Sing_Stamp.

No recomiendo usar Quick_stamp a menos que se esté haciendo un proceso controlado donde tu garantices que
nunca ejecutarás dos veces Quick_stamp. Este servicio podría generar duplicados.

Como Stamp y Quick_stamp hacen lo mismo deberían devolver la misma respuesta, cierto?
 

### Stamp

Resultado: A un PRECFDI le agrega el TimbreFiscalDigital

Proceso: si se timbró previamente devuelve el timbre anterior, valida, timbra y encola.

Retorno: cfdi timbrado o errores.

### Quick_stamp

Resultado: A un PRECFDI le agrega el TimbreFiscalDigital

Proceso: valida, timbra, encola en segundo plano.

### Sing_Stamp

A un PRECFDI sin sello le agrega el TimbreFiscalDigital, para esto debió enviar previamente su certificado y sello.

No lo voy a implementar por inseguro.
