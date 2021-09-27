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

Voy a evitar los servicios que signifiquen enviar a Finkok el certificado o llave privada.
Nunca compartas tus certificados y llaves privadas, ni con tu PAC.

## Entornos de producción y pruebas

Otra característica importante es que Finkok tiene dos entornos de trabajo
y en cada uno tiene su réplica de los servicios que ofrece.

- Producción: `https://facturacion.finkok.com`
- Pruebas: `https://demo-facturacion.finkok.com`
