# Cancelacion de Retenciones con error 1308

Al momento de hacer pruebas de integración sobre el servicio de cancelación de CFDI de tipo retenciones e información
de pagos, se encuentra que al enviar la solicitud el servidor de pruebas del SAT responde en `CodEstatus` el
error `1308 - Certificado revocado o caduco`.

Se ha reportado a Finkok con el [ticket #41610](https://support.finkok.com/support/tickets/41610) donde nos pudieron
responder ciertos cuestionamientos pero el problema sigue sin resolverse:

- ¿Hay algún RFC en especial que deba utilizar para poder hacer pruebas de CFDI de retenciones y pagos?

Como tal no existe un RFC en específico, ya que se pueden realizar con los RFC que se encuentran actualmente
disponible para realizar pruebas, sin embargo en este momento el SAT no está respondiendo de manera satisfactoria
al utilizar cualquiera de los RFC que se tienen publicados en el wiki y que ellos mismos proporcionaron.

- ¿Por qué está devolviendo el código 1308?

Al parecer la incidencia se presenta por que el SAT no tiene habilitados los RFC de prueba.

- Si es un error del SAT, ¿se ha reportado?

Sí, sin embargo no hemos obtenido una respuesta favorable de su parte y en estos momentos nos encontramos
dando seguimiento al caso.

- ¿Tienen ustedes pruebas de integración que confirmen que los servicios de pruebas del SAT están funcionando?

En su momento cuando se implementó el método de cancelación de retenciones funcionaba de forma correcta sin embargo
debido a la actualización de los certificados de pruebas ya no fue posible efectuar pruebas de manera satisfactoria.
De nuestra parte también hemos efectuado pruebas sin embargo obtenemos la misma respuesta que usted obtiene.

## Actualizaciones

2020-01-24: Se encontró el problema y se obtuvo respuesta de las preguntas.

2020-01-27: El problema persiste.

2021-03-20: Las pruebas de integración ya no están marcando este problema.
