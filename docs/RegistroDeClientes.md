# Manejo de clientes de Finkok

El manejo de clientes en Finkok no es nada parecido a una implementación limpia como uno podría esperar
de otros servicios u otras API como las de tipo REST.

Los métodos son: get (implementado como obtain), add, edit y assign.

## Método add

Existen dos parámetros que no tienen uso real: added y coupon. Al momento no se piensa implementarlos.
Estos campos no se muestran en la interfaz del portal ni se pueden obtener por el método get.
Fueron creados para un cliente de Finkok y por lo visto no piensan documentar claramente
su uso o su omisión (ticket #19340).

No está documentado lo que devuelve por respuesta, solo dice que devuelve success y message.
Cuando se agrega un cliente que ya existe previamente, en lugar de devolver FALSE en success, devuelve TRUE.
Por lo que podría entenderse success como que el cliente existe o no.

## Método get (implementado como obtain)

Según la documentación el parámetro taxpayer_id es opcional, pero no lo es.
En su lugar puede estar vacío (que no es lo mismo que opcional).
Cuando se manda vacío significa que se desea obtener todo el listado de clientes registrados.

## Método assign

Este método tiene el propósito de incrementar o decrementar créditos a cuentas de tipo prepago.

Tome en cuenta las siguientes consideraciones:

- Para agregar créditos use un número positivo, para reducir use un número negativo.
- En caso de usarlo con una cuenta ilimitada (on-demand) devuelve un estado de error.
- En caso de decrementar a un número negativo devuelve un error y se conserva la cantidad de créditos previos.

## Método switch

Este método tiene el propósito de cambiar el tipo de cliente de prepago (prepaid) a iliminado (on-demand).

No genera error si se intenta cambiar al estado actual (ilimitado a ilimitado, o prepago a prepago).

## Parámetros username/password

Finkok considera una buena idea que para los métodos add, edit y get los parámetros de usuario y contraseña
no son username/password como los demás. Los parámetros en estos casos son username_reseller/password_reseller.

Pero, para los métodos assign y switch, los parámetros sí son username/password.

## Eliminar un cliente

No existe método para eliminar un cliente (Ticket #19372)

No funciona la eliminación de un cliente en el portal de finkok (Ticket #19524)

Por las respuestas en los tickets, no hay una explicación de porqué no se puede eliminar
(la única razón es "nuestras políticas internas de diseño y desarrollo de Finkok")
y confirman que no se puede (ni se podrá) eliminar un cliente, ni por webservice ni por el portal.

La única opción ofrecida es enviar un correo a soporte solicitando remover los clientes.
Supongo que no les importa mucho la automatización de pruebas.

## Tipo de cliente

En la creación de un cliente, no se puede especificar el crédito, sin embargo sí se puede establecer
si la cuenta es de prepago (prepaid) o ilimitada (ondemand).

Con el método switch es con el que se puede cambiar el tipo de cuenta (prepaid/ondemand).
