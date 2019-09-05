
## Descripción

El servicio [`Query_Pending`](https://wiki.finkok.com/doku.php?id=query_pending) *se usa para consultar el
estatus de una factura que se quedo pendiente de enviar al SAT debido a una falla en el sistema del SAT
o bien que se envió a través del método `Quick_Stamp`*.

Por lo tanto, se podría llegar a ocupar con cualquier método de estampado.

Al enviar un UUID mal formado como `"foo"` regresa el error `"UUID con formato invalido"`.
Así, con errores de ortografía.

Al enviar un UUID recién timbrado con `Quick_Stamp` regresa algunos (no todos) de los valores,
supongo que esto depende del estado en que se encuentra.

## Error encontrado

Al enviar un UUID que no se mandó timbrar, digamos, por ejemplo, timbrado con otro PAC o simplemente falso, como
`01234567-0123-0123-0123-012345678901` lo que devuelve es un error 500 de SOAP:

- Request headers

```text
POST /servicios/soap/stamp HTTP/1.1
Host: demo-facturacion.finkok.com
Connection: Keep-Alive
User-Agent: PHP-SOAP/7.3.3-1
Content-Type: text/xml; charset=utf-8
SOAPAction: "query_pending"
Content-Length: 416
```

- Request body

```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://facturacion.finkok.com/stamp">
    <SOAP-ENV:Body>
        <ns1:query_pending>
            <ns1:username>user@example.com</ns1:username>
            <ns1:password>secret</ns1:password>
            <ns1:uuid>01234567-0123-0123-0123-012345678901</ns1:uuid>
        </ns1:query_pending>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

- Response headers

```text
HTTP/1.1 500 INTERNAL SERVER ERROR
Server: nginx/1.12.2
Date: Sat, 06 Apr 2019 01:40:14 GMT
Content-Type: text/xml; charset=utf-8
Content-Length: 940
Connection: close
x-xss-protection: 1; mode=block
x-content-type-options: nosniff
Vary: Cookie
x-frame-options: DENY
Set-Cookie: sessionid=2f06b59dbf811e2b68be49a930692fe7; httponly; Path=/; secure
```

- Response body (se omiten namespaces que devuelve pero no se usan)

```xml
<?xml version='1.0' encoding='UTF-8'?>
<senv:Envelope xmlns:senv="http://schemas.xmlsoap.org/soap/envelope/">
    <senv:Body>
        <senv:Fault>
            <faultcode>senv:Server</faultcode>
            <faultstring>local variable 'invoice' referenced before assignment</faultstring>
            <faultactor/>
        </senv:Fault>
    </senv:Body>
</senv:Envelope>
```

Lo que **se espera que retorne** es un error, del tipo `"UUID no existente"`
tal como en una llamada con un UUID mal formado.

## Reporte

2019-04-05 20:10 <https://support.finkok.com/support/tickets/17626>

## Actualización 2019-04-08.1

Respondieron con una corrección al servicio en entorno de pruebas, ahora devuelve el mensaje:
`UUID 01234567-0123-0123-0123-012345678901 No Encontrado`.
