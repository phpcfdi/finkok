# El valor `CodEstatus` está ausente en la cancelación de CFDI de Retenciones

**2021-06-08**: `CodEstatus` solo está presente si se envía un valor incorrecto en la petición,
o el código del SAT cuando presenta intermitencias. El problema se marca como finalizado.

Según el WebService de cancelación de retenciones (demo[1]() y producción[2]())
así como en la documentación[3]() debería retornar estructura con
`CodEstatus - El código de respuesta del SAT`.

Sin embargo, no devuelve esta respuesta.

Incluso, en el ejemplo de la documentación[3]() no aparece.

Se solicita que se corrija la documentación del método o bien se corrija la respuesta entregada.

Para probarlo se está usando el método `Cancel_Signature`.
Se muestran los encabezados y cuerpo de la solicitud y la respuesta formateados para mayor claridad.

Se ha documentado en el ticket <https://support.finkok.com/support/tickets/49417>

```text
POST /servicios/soap/retentions HTTP/1.1
Host: demo-facturacion.finkok.com
Connection: Keep-Alive
User-Agent: PHP-SOAP/7.4.15
Content-Type: text/xml; charset=utf-8
SOAPAction: "cancel_signature"
Content-Length: 5861
```

```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:ns1="http://facturacion.finkok.com/retentions">
  <SOAP-ENV:Body>
    <ns1:cancel_signature>
      <ns1:xml>
        PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPENhbmNlbGFjaW9uIHhtbG5zPSJodHRwOi8vY2FuY2VsYXJldGVuY2lvbi5zYXQuZ29iLm14IiB4bWxuczp4c2Q9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1hIiB4bWxuczp4c2k9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1hLWluc3RhbmNlIiBSZmNFbWlzb3I9IkVLVTkwMDMxNzNDOSIgRmVjaGE9IjIwMjEtMDMtMjFUMTk6NTI6MzgiPjxGb2xpb3M+PFVVSUQ+RDdCMjVCOTQtOTRCRC00ODhFLTkxMzctMkZBM0NCRDY5ODcyPC9VVUlEPjwvRm9saW9zPjxTaWduYXR1cmUgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyMiPjxTaWduZWRJbmZvPjxDYW5vbmljYWxpemF0aW9uTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvVFIvMjAwMS9SRUMteG1sLWMxNG4tMjAwMTAzMTUiLz48U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3JzYS1zaGExIi8+PFJlZmVyZW5jZSBVUkk9IiI+PFRyYW5zZm9ybXM+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0dXJlIi8+PC9UcmFuc2Zvcm1zPjxEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjc2hhMSIvPjxEaWdlc3RWYWx1ZT4zOTBQYUtrOVBXTzlEYThXYjR4QUFyOWoyOG89PC9EaWdlc3RWYWx1ZT48L1JlZmVyZW5jZT48L1NpZ25lZEluZm8+PFNpZ25hdHVyZVZhbHVlPkdLVHFhWTRWam9IWGt6M3U2MGp6OXJwdTNvTjliMUVlbzR1N2RKN3Jzb1kyQ2FwcTRyUDNEenI1ZCs2RG4xMEtEMnMweW1Yb2RpaXNSYlJYc3RLS3hPZG42d1BGcGJMTUdyYU85M1daYjNCeDJld3Y1NWV5Y2Y4SThvdis4MHlHVFJ4M3lWbFlveUF1RjhJMkFtSlpSeXhlUXZOdm52VW01aWdlNENJcmJYY08zeTczbVc0TFlpcDlKYjU5b2dTMlNhdUlLbXpMSzNGb3JKWVA2SU9NcUxhQ2pQS0pWOGluaDkyOHJUeWVoMFhQN1VBSi85bndCVm9YSzdWZzBmMFlPeTZoUjBZTVF2K0c4NHhxc3dEMjZRWDl5dDAvUU5TT2FRaXE0bENLSUZOd2NDNFlwMUU4eVhTdHdjKzBUZU1yd2VRMjJIeitFN3locmZ4T0RyTGVIUT09PC9TaWduYXR1cmVWYWx1ZT48S2V5SW5mbz48WDUwOURhdGE+PFg1MDlJc3N1ZXJTZXJpYWw+PFg1MDlJc3N1ZXJOYW1lPkNOPUFDIFVBVCxPPVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUEsT1U9U0FULUlFUyBBdXRob3JpdHksZW1haWxBZGRyZXNzPW9zY2FyLm1hcnRpbmV6QHNhdC5nb2IubXgsc3RyZWV0PTNyYSBjZXJyYWRhIGRlIGNhZGl6LHBvc3RhbENvZGU9MDYzNzAsQz1NWCxTVD1DSVVEQUQgREUgTUVYSUNPLEw9Q09ZT0FDQU4seDUwMFVuaXF1ZUlkZW50aWZpZXI9Mi41LjQuNDUsdW5zdHJ1Y3R1cmVkTmFtZT1yZXNwb25zYWJsZTogQUNETUEtU0FUPC9YNTA5SXNzdWVyTmFtZT48WDUwOVNlcmlhbE51bWJlcj4zMDAwMTAwMDAwMDQwMDAwMjQzNDwvWDUwOVNlcmlhbE51bWJlcj48L1g1MDlJc3N1ZXJTZXJpYWw+PFg1MDlDZXJ0aWZpY2F0ZT5NSUlGdXpDQ0E2T2dBd0lCQWdJVU16QXdNREV3TURBd01EQTBNREF3TURJME16UXdEUVlKS29aSWh2Y05BUUVMQlFBd2dnRXJNUTh3RFFZRFZRUUREQVpCUXlCVlFWUXhMakFzQmdOVkJBb01KVk5GVWxaSlEwbFBJRVJGSUVGRVRVbE9TVk5VVWtGRFNVOU9JRlJTU1VKVlZFRlNTVUV4R2pBWUJnTlZCQXNNRVZOQlZDMUpSVk1nUVhWMGFHOXlhWFI1TVNnd0pnWUpLb1pJaHZjTkFRa0JGaGx2YzJOaGNpNXRZWEowYVc1bGVrQnpZWFF1WjI5aUxtMTRNUjB3R3dZRFZRUUpEQlF6Y21FZ1kyVnljbUZrWVNCa1pTQmpZV1JwZWpFT01Bd0dBMVVFRVF3Rk1EWXpOekF4Q3pBSkJnTlZCQVlUQWsxWU1Sa3dGd1lEVlFRSURCQkRTVlZFUVVRZ1JFVWdUVVZZU1VOUE1SRXdEd1lEVlFRSERBaERUMWxQUVVOQlRqRVJNQThHQTFVRUxSTUlNaTQxTGpRdU5EVXhKVEFqQmdrcWhraUc5dzBCQ1FJVEZuSmxjM0J2Ym5OaFlteGxPaUJCUTBSTlFTMVRRVlF3SGhjTk1Ua3dOakUzTVRrME5ERTBXaGNOTWpNd05qRTNNVGswTkRFMFdqQ0I0akVuTUNVR0ExVUVBeE1lUlZORFZVVk1RU0JMUlUxUVJWSWdWVkpIUVZSRklGTkJJRVJGSUVOV01TY3dKUVlEVlFRcEV4NUZVME5WUlV4QklFdEZUVkJGVWlCVlVrZEJWRVVnVTBFZ1JFVWdRMVl4SnpBbEJnTlZCQW9USGtWVFExVkZURUVnUzBWTlVFVlNJRlZTUjBGVVJTQlRRU0JFUlNCRFZqRWxNQ01HQTFVRUxSTWNSVXRWT1RBd016RTNNME01SUM4Z1dFbFJRamc1TVRFeE5sRkZOREVlTUJ3R0ExVUVCUk1WSUM4Z1dFbFJRamc1TVRFeE5rMUhVazFhVWpBMU1SNHdIQVlEVlFRTEV4VkZjMk4xWld4aElFdGxiWEJsY2lCVmNtZGhkR1V3Z2dFaU1BMEdDU3FHU0liM0RRRUJBUVVBQTRJQkR3QXdnZ0VLQW9JQkFRQ04wcGVLcGdmT0w3NWlZUnYxZnFxK29WWXNMUFZVUi9HaWJZbUdLYzlJbkhGeTVsWUY2T1RZam5JSXZta09kUm9iYkdsQ1V4T1JYL3RMc2w4WWE5Z202WW83aEhuT0RSQklEdXAzR0lTRnpCLzk2UjlLL016WVFPY3NjTUlvQkRBUmF5Y25Mdnk3RmxNdk83L3JsVm5zU0FSeFpSTzhLejhaa2tzajJ6cGVZcGpaSXlhLzM2OStvR3FRazFjVFJrSG81OUp2SjRUZmJrLzNpSXlmNEgvSW5pOW5CZTljWVdvME1uS29iN0REdC92c2RpNXRBOG1NdEE5NTNMYXBOeUNaSURDUlFRbFVHTmdEcVk5LzhGNW1VdlZna2NjenNJZ0dkdmY5dk1RUFNmM2pqQ2lLajdqNnVjeGwxK0Z3SldtYnZnTm1pYVVSLzBxNG0ycm03OGxGQWdNQkFBR2pIVEFiTUF3R0ExVWRFd0VCL3dRQ01BQXdDd1lEVlIwUEJBUURBZ2JBTUEwR0NTcUdTSWIzRFFFQkN3VUFBNElDQVFCY3BqMVRqVDRqaWluSXVqSWRBbEZ6RTZrUndZSkNuREcwOHpTcDRrU25TaGp4QURHRVhIMmNoZWhLTVYwRlk3YzRuakE1ZURHZEEvRzJPQ1RQdkY1cnBlQ1pQNUR3NTA0UlprWURsMnN1Unord2Exc05CVnBibkJKRUswZlFjTjNJZnRCd3NnTkZkRmhVdEN5dzNsdXMxU1NKYlB4akxIUzZGY1paNTFZU2VJZmNOWE9BdVRxZGltdXNhWHExNUdyU3JDT2tNNm4yamZqMnNNSllNMkhYYVhKNnJHVEVnWW1oWWR3eFd0aWw2UmZaQitmR1EvSDlJOVdMbmw0S1RaVVM2QzkrTkxIaDRGUERoU2sxOWZwUzJTLzU2YXFnRm9HQWtYQVl0OUZ5NUVDYVBjVUxJZkoxREVic1hLeVJkQ3YzSlk4OSswTU5rT2RhRG5zZW1TMm81R2wwOHpJNGlZdHQzTDQwZ0FaNjBOUGgzMWtWTG5ZTnNtdmZOeFl5S3ArQWVKdERIeVc5dzdmdE0wSG9pK0J1Um1jQVFTS0ZWM3BrOGo1MWxhK2pyUkJyQVV2OGJsYlJjUTVCaVpVd0p6SEZFS0l3VHNSR29SeUV4OTZzTm5CMDNuNkdUd2pJR3o5MlNtTGRObDk1cjlya3ZwKzJtNFM2cTFsUHVYYUZnN0RHQnJYV0M4aXlxZVdFMmlvYmR3SUl1WFBUTVZxUWIxMm0xZEFrSlZSTzVOZEhuUC9NcHFPdk9nTHFvWkJOSEd5Qmc0R3FtNHNDSkhDeEExYzhFbGZhMlJRVENrMHRBemxsTDR2T25JMUdIa0dKbjY1eG9rR3NhVTRCNEQzNnhoN2VXcmZqNC9wZ1dIbXRvREFZYTh3elN3bzJHVkNaT3MrbXRFZ09RQjkxL2c9PTwvWDUwOUNlcnRpZmljYXRlPjwvWDUwOURhdGE+PEtleVZhbHVlPjxSU0FLZXlWYWx1ZT48TW9kdWx1cz5qZEtYaXFZSHppKytZbUViOVg2cXZxRldMQ3oxVkVmeG9tMkpoaW5QU0p4eGN1WldCZWprMkk1eUNMNXBEblVhRzJ4cFFsTVRrVi83UzdKZkdHdllKdW1LTzRSNXpnMFFTQTdxZHhpRWhjd2YvZWtmU3Z6TTJFRG5MSERDS0FRd0VXc25KeTc4dXhaVEx6dS82NVZaN0VnRWNXVVR2Q3MvR1pKTEk5czZYbUtZMlNNbXY5K3ZmcUJxa0pOWEUwWkI2T2ZTYnllRTMyNVA5NGlNbitCL3lKNHZad1h2WEdGcU5ESnlxRyt3dzdmNzdIWXViUVBKakxRUGVkeTJxVGNnbVNBd2tVRUpWQmpZQTZtUGYvQmVabEwxWUpISE03Q0lCbmIzL2J6RUQwbjk0NHdvaW8rNCtybk1aZGZoY0NWcG03NERab21sRWY5S3VKdHE1dS9KUlE9PTwvTW9kdWx1cz48RXhwb25lbnQ+QVFBQjwvRXhwb25lbnQ+PC9SU0FLZXlWYWx1ZT48L0tleVZhbHVlPjwvS2V5SW5mbz48L1NpZ25hdHVyZT48L0NhbmNlbGFjaW9uPgo=
      </ns1:xml>
      <ns1:username>****</ns1:username>
      <ns1:password>****</ns1:password>
      <ns1:store_pending>false</ns1:store_pending>
    </ns1:cancel_signature>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

```text
HTTP/1.1 200 OK
Server: nginx/1.16.1
Date: Mon, 22 Mar 2021 01:52:39 GMT
Content-Type: text/xml; charset=utf-8
Content-Length: 2982
Connection: close
x-xss-protection: 1; mode=block
x-content-type-options: nosniff
Vary: Cookie
x-frame-options: DENY
Set-Cookie: sessionid=1610c4e4a25d8d2d13fa52f89432532c; httponly; Path=/; secure
Strict-Transport-Security: max-age=63072000; includeSubdomains
X-Content-Type-Options: nosniff
```

```xml
<?xml version='1.0' encoding='UTF-8'?>
<senv:Envelope xmlns:wsa="http://schemas.xmlsoap.org/ws/2003/03/addressing"
    xmlns:tns="http://facturacion.finkok.com/retentions"
    xmlns:plink="http://schemas.xmlsoap.org/ws/2003/05/partner-link/" xmlns:xop="http://www.w3.org/2004/08/xop/include"
    xmlns:senc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:s1="http://facturacion.finkok.com/cancellation"
    xmlns:s0="apps.services.soap.core.views" xmlns:s12env="http://www.w3.org/2003/05/soap-envelope/"
    xmlns:s12enc="http://www.w3.org/2003/05/soap-encoding/" xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:senv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/">
  <senv:Body>
    <tns:cancel_signatureResponse>
      <tns:cancel_signatureResult>
        <s0:Folios>
          <s0:Folio>
            <s0:UUID>D7B25B94-94BD-488E-9137-2FA3CBD69872</s0:UUID>
            <s0:EstatusUUID>1201</s0:EstatusUUID>
            <s0:EstatusCancelacion></s0:EstatusCancelacion>
          </s0:Folio>
        </s0:Folios>
        <s0:Acuse>&lt;?xml version="1.0"?&gt;&lt;Acuse xmlns:xsd="http://www.w3.org/2001/XMLSchema"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Fecha="2021-03-21T19:52:39.2804339"
          RfcEmisor="EKU9003173C9" WorkProcessId="36b7e380-b508-4fa8-8c01-053c28f1e2a6"
          xmlns="http://www.sat.gob.mx/esquemas/retencionpago/1"&gt;&lt;Folios&gt;&lt;UUID&gt;D7B25B94-94BD-488E-9137-2FA3CBD69872&lt;/UUID&gt;&lt;EstatusUUID&gt;1201&lt;/EstatusUUID&gt;&lt;/Folios&gt;&lt;Signature
          Id="SelloSAT" xmlns="http://www.w3.org/2000/09/xmldsig#"&gt;&lt;SignedInfo&gt;&lt;CanonicalizationMethod
          Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" /&gt;&lt;SignatureMethod
          Algorithm="http://www.w3.org/2001/04/xmldsig-more#hmac-sha512" /&gt;&lt;Reference URI=""&gt;&lt;Transforms&gt;&lt;Transform
          Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116"&gt;&lt;XPath&gt;not(ancestor-or-self::*[local-name()='Signature'])&lt;/XPath&gt;&lt;/Transform&gt;&lt;/Transforms&gt;&lt;DigestMethod
          Algorithm="http://www.w3.org/2001/04/xmlenc#sha512" /&gt;&lt;DigestValue&gt;eJeUPDI7YAMdtXTejG5lb2vVWv0bSLOAmvV0eS1PDlSAWrtqhn439gFjc/lJyMQ7d3A07lDLzlxvgz4VQ6kPUA==&lt;/DigestValue&gt;&lt;/Reference&gt;&lt;/SignedInfo&gt;&lt;SignatureValue&gt;1sEGkf3R/Az3KDwijEWv1onuJ4B3cuicMx6XLPRei4XvFRRN+f0LyXdqzad5afSc89C63LN8JIRFHRh5LxBiTQ==&lt;/SignatureValue&gt;&lt;KeyInfo&gt;&lt;KeyName&gt;BF66E582888CC845&lt;/KeyName&gt;&lt;KeyValue&gt;&lt;RSAKeyValue&gt;&lt;Modulus&gt;n5YsGT0w5Z70ONPbqszhExfJU+KY3Bscftc2jxUn4wxpSjEUhnCuTd88OK5QbDW3Mupoc61jr83lRhUCjchFAmCigpC10rEntTfEU+7qtX8ud/jJJDB1a9lTIB6bhBN//X8IQDjhmHrfKvfen3p7RxLrFoxzWgpwKriuGI5wUlU=&lt;/Modulus&gt;&lt;Exponent&gt;AQAB&lt;/Exponent&gt;&lt;/RSAKeyValue&gt;&lt;/KeyValue&gt;&lt;/KeyInfo&gt;&lt;/Signature&gt;&lt;/Acuse&gt;
        </s0:Acuse>
        <s0:Fecha>2021-03-21T19:52:39.2804339</s0:Fecha>
        <s0:RfcEmisor>EKU9003173C9</s0:RfcEmisor>
      </tns:cancel_signatureResult>
    </tns:cancel_signatureResponse>
  </senv:Body>
</senv:Envelope>
```

[1] https://demo-facturacion.finkok.com/servicios/soap/retentions.wsdl
[2] https://facturacion.finkok.com/servicios/soap/retentions.wsdl
[3] https://wiki.finkok.com/doku.php?id=cancel_signature_method_retentions

## Respuesta

Se corrigió la documentación del servicio.

- El campo de respuesta `CodEstatus` solo está presente si se envía un valor incorrecto en la petición,
  o el código del SAT cuando presenta intermitencias.
- El campo de respuesta `SeguimientoCancelacion` solo está presente si se envía más de un UUID a cancelar.
