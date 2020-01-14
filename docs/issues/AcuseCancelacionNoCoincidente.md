# El acuse de cancelación no coincide

Cuando se realiza una cancelación (vía `cancel_signature`) una de las respuestas es el acuse de cancelación
entregado por el SAT. Dicho *acuse* se puede consultar en la respuesta de cancelación como el *voucher*.

Finkok tiene también el método `get_receipt`, el problema es que el resultado devuelto no coincide
con el del servicio `cancel_signature`, cuando deberían ser idénticos.

Específicamente la diferencia se encuentra en el atributo `CancelaCFDResponse/CancelaCFDResult@Fecha` donde,
como ejemplo, el método `get_receipt` devuelve *"2020-01-13·14:11:05"*
y `cancelSignature` devuelve *"2020-01-13T14:11:05.3366563"*.

Existe otra diferencia en el órden de los atributos del nodo `CancelaCFDResponse/CancelaCFDResult/Signature`,
sin embargo no lo considero relevante para efectos de validación.

Esto es importante porque altera el valor de la firma (la respuesta es un mensaje firmado), y deja la duda de que
Finkok no solo está almacenando la respuesta, si no además la está alterando.

A modo personal, creo que es importante que no se alteren los mensajes de respuestas del SAT, pero por otro lado
aconsejo no almacenar este *acuse*, dado que se malentiende generalmente su uso. El acuse no significa que el
CFDI fuese cancelado, el acuse contiene solamente la respuesta a la petición presentada ante el SAT.

## Actualizaciones

2019-01-14: Se creó el ticket #41435 <https://support.finkok.com/support/tickets/41435> documentando esta situación
y se publicará la actualización a pesar de este error, si Finkok decidiera no actualizar su servicio entonces
se eliminará la comprobación en el test de integración.
