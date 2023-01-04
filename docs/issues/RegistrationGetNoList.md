# El método `Registration#Get` con `taxpayer_id` vacío no devuelve el listado de clientes

## Descripción

El método `Registration#Get` debería devolver todos los clientes relacionados con la cuenta
cuando no se envía el parámetro `taxpayer_id`.

Así está documentado en el webservice:

- https://facturacion.finkok.com/servicios/soap/registration.wsdl

> This function lists all the user of the account if no taxpayer_id is passed
> otherwise will return the taxpayer_id and status of the given user.

```xml
<wsdl:operation name="get" parameterOrder="get">
    <wsdl:documentation>
      This function lists all the user of the account if no taxpayer_id is passed otherwise will return the taxpayer_id and status of the given user.
    </wsdl:documentation>
    <wsdl:input name="get" message="tns:get"/>
    <wsdl:output name="getResponse" message="tns:getResponse"/>
</wsdl:operation>
```

Y en la documentación:

- <https://wiki.finkok.com/doku.php?id=get>

> Este método tiene como finalidad la de otorgar al socio de negocios un listado
> o el status del RFC Emisor que esté ingresando y tenga registrado en su cuenta.

Al correr pruebas de integración, hemos notado que este ya no es el caso, y en su lugar, en vez de devolver el listado,
devuelve el mensaje `RFC Invalido` (así, sin acento). Anteriormente, sí devolvía el listado de clientes.

## Reporte

Se reportó a Finkok en el [ticket #66516](https://support.finkok.com/support/tickets/66516), sin embargo,
la respuesta fue que —efectivamente— este método se comportaba de la manera documentada y esperada en el
entorno de pruebas, pero que el cambio nunca llegó a producción. Actualmente, no devuelve el listado de
clientes ni en el entorno de pruebas ni en producción.

También comentan que se agregará otro método diferente para poder obtener el listado, sin embargo,
todavía no tienen fecha estimada para la implementación.

Finkok debería considerar esto como un fallo en su aplicación, no como una nueva funcionalidad a agregar.
Y, consecuentemente, darle prioridad alta para repararlo.

### Reporte 2022-01-03

A pesar de la implementación del método `Registration#Customers`, el método `Registration#Get` sigue retornando
un conjunto de `ResellerUser`, cuando debería de regresar cero o uno. Asimismo, se cambió la documentación
para que ni diga que devuelve un listado de clientes.

Se desconoce la fecha de pase a producción de la solución.

## Solución

Finkok implementó el método `Registration#Customers` con el que se puede obtener el listado de clientes.

Al 2022-01-03 se hizo la implementación del método `Registration#Customers`.

### Implementación

Este método devuelve un **listado paginado** —cosa que no habían hecho en ningún otro método—
y se implementa de dos formas diferentes:

#### `QuickFinkok::customersObtainAll(): Customers`

Al llamarlo consume tantas páginas sea necesario y retorna el listado completo de clientes en el objeto `Customers`.
No contiene los datos originales de las consultas, a diferencia de la mayoría de los valores retornados.

#### `Finkok::registrationCustomers(ObtainCustomersCommand $command): ObtainCustomersResult`

Al llamarlo consume el servicio y obtiene la página especificada.
Este método sí contiene un objeto `ObtainCustomersResult` con toda la respuesta de la consulta.
Solo devuelve los datos de la consulta especificada.

## Actualizaciones

2022-12-20: Se reportó y documentó el problema.

2022-12-31: Se agregó el método `Registration#Customers`.

2022-01-03: Se implementó el consumo del método `Registration#Customers`.
