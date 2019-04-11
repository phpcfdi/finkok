
## Descripción

El servicio [`Cancel_Signature`](https://wiki.finkok.com/doku.php?id=cancelsigned_method)
*realiza la cancelación de un comprobante CFDI por medio de un XML desarrollado como la firma*.

Al llamar a la cancelación con una firma válida para un CFDI recién creado,
con el el parámetro `store_pending` en `false`,
se encuentra con una respuesta `205` en lugar de la esperada `201`.
No importa si se creó el timbre usando `quick_stamp` o `stamp`.

Valores documentados de la validación de la cancelación del CFDI
  <https://wiki.finkok.com/doku.php?id=tipificacion#validacion_de_la_cancelacion_del_cfdi>

* 201 - Petición de cancelación realizada exitosamente
* 202 - Petición de cancelación realizada Previamente
* 203 - No corresponde el RFC del Emisor y de quien solicita la cancelación
* 205 - UUID No encontrado
* no_cancelable - El UUID contiene CFDI relacionados

Es importante destacar que, si se consulta el método `get_sat_status` la respuesta que obtenemos es:

- EsCancelable: Cancelable sin aceptación
- CodigoEstatus: S - Comprobante obtenido satisfactoriamente.
- Estado: Vigente

* Request headers

```text
POST /servicios/soap/cancel HTTP/1.1
Host: demo-facturacion.finkok.com
Connection: Keep-Alive
User-Agent: PHP-SOAP/7.3.3-1
Content-Type: text/xml; charset=utf-8
SOAPAction: "cancel_signature"
Content-Length: 5869
```

* Request body (formateo de espacios agregados, original sin whitespace)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://facturacion.finkok.com/cancel">
    <SOAP-ENV:Body>
        <ns1:cancel_signature>
            <ns1:xml>PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPENhbmNlbGFjaW9uIHhtbG5zPSJodHRwOi8vY2FuY2VsYWNmZC5zYXQuZ29iLm14IiB4bWxuczp4c2Q9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1hIiB4bWxuczp4c2k9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1hLWluc3RhbmNlIiBSZmNFbWlzb3I9IlRDTTk3MDYyNU1CMSIgRmVjaGE9IjIwMTktMDQtMTBUMTE6MTA6MzUiPjxGb2xpb3M+PFVVSUQ+QTJENTMwRDUtODZFRS00RUQ2LUFGQjctMUM5NjVENzJBM0UyPC9VVUlEPjwvRm9saW9zPjxTaWduYXR1cmUgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyMiPjxTaWduZWRJbmZvPjxDYW5vbmljYWxpemF0aW9uTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvVFIvMjAwMS9SRUMteG1sLWMxNG4tMjAwMTAzMTUiLz48U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3JzYS1zaGExIi8+PFJlZmVyZW5jZSBVUkk9IiI+PFRyYW5zZm9ybXM+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0dXJlIi8+PC9UcmFuc2Zvcm1zPjxEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjc2hhMSIvPjxEaWdlc3RWYWx1ZT5GS3Rpc2N6d0RDUDJmbzluRWhWeENMT3AwNEE9PC9EaWdlc3RWYWx1ZT48L1JlZmVyZW5jZT48L1NpZ25lZEluZm8+PFNpZ25hdHVyZVZhbHVlPlV1dllpdFFmM3J4Q0cyaER5RTZaRTkzOE54bVBkRHZCNUlPVTZzVSt0REZRUk1neDJPa2YyM1FFYzNETGFxb04yejcyazZhaWN0ZWhGOUZObzdzb09vbW94enpmazJyakhZUVV0TXJ4NXFZQWYvMVA0cXdmL2JsVEdITDlFOEFnTlFiczI2R21UL1RNVlRmVisrcHZkRXZWSHZTWk0zUWFtLytoS1RFL0VpdVBkK3ZzS3pXQUhnZldXSkkyT2lUdCsxUEpiYlJISFZSR25GbVVMMUNERGdPQ2R6OEJ0c2JmYTFFZnBaZjdLR0RtdkFEZTBGZDFPUmVYVllrTW13YW44dE9NQm5NSW1wT1IvcklhNUtzYVgrTWpSdTVZQXZIWXhzYUc3Rzh0RTRNakdNcWRzcEpHbE9LM09KUTRLb3FXSVpCSDVaZ2FkSHRnczBKVFlmb0hIQT09PC9TaWduYXR1cmVWYWx1ZT48S2V5SW5mbz48WDUwOURhdGE+PFg1MDlJc3N1ZXJTZXJpYWw+PFg1MDlJc3N1ZXJOYW1lPi9DTj1FSklETyBST0RSSUdVRVogUFVFQkxBIFNBIERFIENWL25hbWU9RUpJRE8gUk9EUklHVUVaIFBVRUJMQSBTQSBERSBDVi9PPUVKSURPIFJPRFJJR1VFWiBQVUVCTEEgU0EgREUgQ1YveDUwMFVuaXF1ZUlkZW50aWZpZXI9VENNOTcwNjI1TUIxIC8gSEVHVDc2MTAwMzRTMi9zZXJpYWxOdW1iZXI9IC8gSEVHVDc2MTAwM01ERlJOTjA5L09VPVBydWViYXNfQ0ZESTwvWDUwOUlzc3Vlck5hbWU+PFg1MDlTZXJpYWxOdW1iZXI+MjAwMDEwMDAwMDAzMDAwMjI3NjI8L1g1MDlTZXJpYWxOdW1iZXI+PC9YNTA5SXNzdWVyU2VyaWFsPjxYNTA5Q2VydGlmaWNhdGU+TUlJRjhEQ0NBOWlnQXdJQkFnSVVNakF3TURFd01EQXdNREF6TURBd01qSTNOakl3RFFZSktvWklodmNOQVFFTEJRQXdnZ0ZtTVNBd0hnWURWUVFEREJkQkxrTXVJRElnWkdVZ2NISjFaV0poY3lnME1EazJLVEV2TUMwR0ExVUVDZ3dtVTJWeWRtbGphVzhnWkdVZ1FXUnRhVzVwYzNSeVlXTnB3N051SUZSeWFXSjFkR0Z5YVdFeE9EQTJCZ05WQkFzTUwwRmtiV2x1YVhOMGNtRmphY096YmlCa1pTQlRaV2QxY21sa1lXUWdaR1VnYkdFZ1NXNW1iM0p0WVdOcHc3TnVNU2t3SndZSktvWklodmNOQVFrQkZocGhjMmx6Ym1WMFFIQnlkV1ZpWVhNdWMyRjBMbWR2WWk1dGVERW1NQ1FHQTFVRUNRd2RRWFl1SUVocFpHRnNaMjhnTnpjc0lFTnZiQzRnUjNWbGNuSmxjbTh4RGpBTUJnTlZCQkVNQlRBMk16QXdNUXN3Q1FZRFZRUUdFd0pOV0RFWk1CY0dBMVVFQ0F3UVJHbHpkSEpwZEc4Z1JtVmtaWEpoYkRFU01CQUdBMVVFQnd3SlEyOTViMkZqdzZGdU1SVXdFd1lEVlFRdEV3eFRRVlE1TnpBM01ERk9Uak14SVRBZkJna3Foa2lHOXcwQkNRSU1FbEpsYzNCdmJuTmhZbXhsT2lCQlEwUk5RVEFlRncweE5qRXdNakV5TURRM05EVmFGdzB5TURFd01qRXlNRFEzTkRWYU1JSGNNU2d3SmdZRFZRUURFeDlGU2tsRVR5QlNUMFJTU1VkVlJWb2dVRlZGUWt4QklGTkJJRVJGSUVOV01TZ3dKZ1lEVlFRcEV4OUZTa2xFVHlCU1QwUlNTVWRWUlZvZ1VGVkZRa3hCSUZOQklFUkZJRU5XTVNnd0pnWURWUVFLRXg5RlNrbEVUeUJTVDBSU1NVZFZSVm9nVUZWRlFreEJJRk5CSUVSRklFTldNU1V3SXdZRFZRUXRFeHhVUTAwNU56QTJNalZOUWpFZ0x5QklSVWRVTnpZeE1EQXpORk15TVI0d0hBWURWUVFGRXhVZ0x5QklSVWRVTnpZeE1EQXpUVVJHVWs1T01Ea3hGVEFUQmdOVkJBc1VERkJ5ZFdWaVlYTmZRMFpFU1RDQ0FTSXdEUVlKS29aSWh2Y05BUUVCQlFBRGdnRVBBRENDQVFvQ2dnRUJBS0F6Q3NlaWtaWGtheVZpeEVsNDlYRkduOTBxWTZFc1Y3cWJpN01mNndKdmZvRWNNL2F6dUJ2YWd5OUtGZS8vTHFJbmQ0QTRLL0p3YmJTaVZpSmNKMWUwUExPSmhPd2I4bDdIdWUvblh0bTNiUFpLTDkrUTg3UEFGQjgyL0N3Wi9xTjFSS0FBQjFFOEd5UTA1eUltdzcxZ043VmJJMGkrOVltMUxUTG90VjV2UlNJTUpIRk53YzFkZDZRNHk4MlMvQ2JaZURRV0lhY0N0L2M1QXNsTDBwU3Y4RjZYemRmZXRHYmVsM1ZvaWZzQTNxTkUxcS9IZVB1YS9IMU9KdXB5R085aktKY09rV0VoNXB3aWMzMUZEVkVNeVJlRjJUQ3FZTFBBSDVsVTUyNVNKb1FPb3VPRUd1dFcybm5Pa1R0OHhPa1JkOTlKZlRKdk0vM1k5WmIwRFZrQ0F3RUFBYU1kTUJzd0RBWURWUjBUQVFIL0JBSXdBREFMQmdOVkhROEVCQU1DQnNBd0RRWUpLb1pJaHZjTkFRRUxCUUFEZ2dJQkFBK29rQ3JzWWYyUGw2cGhGd0xGdW9Odk80emNHUENRc1JybDg5WmJERGdkVGhMM2lBb2kwd2JET2w5K0VjSmlKVEVmRGRvOHNhNmMzWTV1YmZaOHpvZzNTZGxndUwrRmI1Qno3QjFzajJoZFFGRHR2Wmw1Z2tFM3RkaWY0T1NNaExRSW9sQnN2NDc0NkRNN2R0T1RLY2ozSGl3TzZLYkJQcUlGeGY2Qi96eTc0R2FmZzRyNkRvaVNucDEydlRoNTNmREtPaktCN0VJWDkrTWJ1V2Z3bnF0ZzBaTXZrbk9wWWtMQ2ZESlRJWEROaGdrNnlrd3ZhYVB4aWxNTWR2SlNSdXRXQnByS0VaUzVHMjZ3U0xubkloVzZKOFhtNzl6OG53UVlyR3Q2VGZiakN2Rk43S2JGYVYxYzZoTHY1Y1hpbDJrZGlyZjBDcFpXdkRFSTJaZlFLajJVUDBBczd6N2VJbDdWblk4bGJJZy9KTkFwT2ltWitmTGdtaWtIc1NmcUU5NFl6alRCM0xMSVlzYWNMQThwT1dxbS90d2tVa0NGSUM3eCtXWkl5Q3RseWVnelFkdjFJKzk1UXM1LzNSS2I5SjY1TFBsdk1KZ1BIVlBSR1NJT2JETGlza3FHSU5ObWFVTEIzcEFCcXhQOVhrU3pwUFFJNE1FOUphY3pUTjkvbUFFb3lwcjdEQlJQMlpwZUpNdXNJVnZjODhJaDJMaEJlb256YTdNaVA4dUJSVk1MU2ZHVXUrQW50ZGdrM0F6NXEvM1F6KzRDdkVleDl2TkwyNGJNWFNmTTdtSytZYWx3NkxlS3ZEVzRTTXQrSkhRNWZwM2NCVnlVYldnbG1qalN0MmVoWURqUjJ0K2VJdXhxeXlzaHk3aUoyUWxlTTBmdUhFMEwyR0IzQzhSdzwvWDUwOUNlcnRpZmljYXRlPjwvWDUwOURhdGE+PEtleVZhbHVlPjxSU0FLZXlWYWx1ZT48TW9kdWx1cz5vRE1LeDZLUmxlUnJKV0xFU1hqMWNVYWYzU3Bqb1N4WHVwdUxzeC9yQW05K2dSd3o5ck80RzlxREwwb1Y3Lzh1b2lkM2dEZ3I4bkJ0dEtKV0lsd25WN1E4czRtRTdCdnlYc2U1NytkZTJiZHM5a292MzVEenM4QVVIemI4TEJuK28zVkVvQUFIVVR3YkpEVG5JaWJEdldBM3RWc2pTTDcxaWJVdE11aTFYbTlGSWd3a2NVM0J6VjEzcERqTHpaTDhKdGw0TkJZaHB3SzM5emtDeVV2U2xLL3dYcGZOMTk2MFp0NlhkV2lKK3dEZW8wVFdyOGQ0KzVyOGZVNG02bklZNzJNb2x3NlJZU0htbkNKemZVVU5VUXpKRjRYWk1LcGdzOEFmbVZUbmJsSW1oQTZpNDRRYTYxYmFlYzZSTzN6RTZSRjMzMGw5TW04ei9kajFsdlFOV1E9PTwvTW9kdWx1cz48RXhwb25lbnQ+QVFBQjwvRXhwb25lbnQ+PC9SU0FLZXlWYWx1ZT48L0tleVZhbHVlPjwvS2V5SW5mbz48L1NpZ25hdHVyZT48L0NhbmNlbGFjaW9uPgo=</ns1:xml>
            <ns1:username>user@example.com</ns1:username>
            <ns1:password>secret</ns1:password>
            <ns1:store_pending>false</ns1:store_pending>
        </ns1:cancel_signature>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

* Contenido del elemento `xml` (formateo de espacios agregados, original sin whitespace)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<Cancelacion xmlns="http://cancelacfd.sat.gob.mx" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        RfcEmisor="TCM970625MB1" Fecha="2019-04-10T11:10:35">
    <Folios>
        <UUID>A2D530D5-86EE-4ED6-AFB7-1C965D72A3E2</UUID>
    </Folios>
    <Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
        <SignedInfo>
            <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
            <SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
            <Reference URI="">
                <Transforms>
                    <Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                </Transforms>
                <DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
                <DigestValue>FKtisczwDCP2fo9nEhVxCLOp04A=</DigestValue>
            </Reference>
        </SignedInfo>
        <SignatureValue>UuvYitQf3rxCG2hDyE6ZE938NxmPdDvB5IOU6sU+tDFQRMgx2Okf23QEc3DLaqoN2z72k6aictehF9FNo7soOomoxzzfk2rjHYQUtMrx5qYAf/1P4qwf/blTGHL9E8AgNQbs26GmT/TMVTfV++pvdEvVHvSZM3Qam/+hKTE/EiuPd+vsKzWAHgfWWJI2OiTt+1PJbbRHHVRGnFmUL1CDDgOCdz8Btsbfa1EfpZf7KGDmvADe0Fd1OReXVYkMmwan8tOMBnMImpOR/rIa5KsaX+MjRu5YAvHYxsaG7G8tE4MjGMqdspJGlOK3OJQ4KoqWIZBH5ZgadHtgs0JTYfoHHA==</SignatureValue>
        <KeyInfo>
            <X509Data>
                <X509IssuerSerial>
                    <X509IssuerName>/CN=EJIDO RODRIGUEZ PUEBLA SA DE CV/name=EJIDO RODRIGUEZ PUEBLA SA DE CV/O=EJIDO RODRIGUEZ PUEBLA SA DE CV/x500UniqueIdentifier=TCM970625MB1 / HEGT7610034S2/serialNumber= / HEGT761003MDFRNN09/OU=Pruebas_CFDI</X509IssuerName>
                    <X509SerialNumber>20001000000300022762</X509SerialNumber>
                </X509IssuerSerial>
                <X509Certificate>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</X509Certificate>
            </X509Data>
            <KeyValue>
                <RSAKeyValue>
                    <Modulus>oDMKx6KRleRrJWLESXj1cUaf3SpjoSxXupuLsx/rAm9+gRwz9rO4G9qDL0oV7/8uoid3gDgr8nBttKJWIlwnV7Q8s4mE7BvyXse57+de2bds9kov35Dzs8AUHzb8LBn+o3VEoAAHUTwbJDTnIibDvWA3tVsjSL71ibUtMui1Xm9FIgwkcU3BzV13pDjLzZL8Jtl4NBYhpwK39zkCyUvSlK/wXpfN1960Zt6XdWiJ+wDeo0TWr8d4+5r8fU4m6nIY72Molw6RYSHmnCJzfUUNUQzJF4XZMKpgs8AfmVTnblImhA6i44Qa61baec6RO3zE6RF330l9Mm8z/dj1lvQNWQ==</Modulus>
                    <Exponent>AQAB</Exponent>
                </RSAKeyValue>
            </KeyValue>
        </KeyInfo>
    </Signature>
</Cancelacion>
```

* Response headers

```text
HTTP/1.1 200 OK
Server: nginx/1.10.2
Date: Wed, 10 Apr 2019 16:10:39 GMT
Content-Type: text/xml; charset=utf-8
Content-Length: 3068
Connection: close
x-xss-protection: 1; mode=block
x-content-type-options: nosniff
Vary: Cookie
x-frame-options: DENY
Set-Cookie: sessionid=4711e676ea90df9c680a1a66b79bc273; httponly; Path=/; secure
Strict-Transport-Security: max-age=63072000; includeSubdomains
X-Content-Type-Options: nosniff
```

* Response body (formateo de espacios agregados, original sin whitespace)

```xml
<?xml version='1.0' encoding='UTF-8'?>
<senv:Envelope xmlns:wsa="http://schemas.xmlsoap.org/ws/2003/03/addressing"
               xmlns:tns="http://facturacion.finkok.com/cancel"
               xmlns:plink="http://schemas.xmlsoap.org/ws/2003/05/partner-link/"
               xmlns:xop="http://www.w3.org/2004/08/xop/include" xmlns:senc="http://schemas.xmlsoap.org/soap/encoding/"
               xmlns:s1="http://facturacion.finkok.com/cancellation" xmlns:s0="apps.services.soap.core.views"
               xmlns:s12env="http://www.w3.org/2003/05/soap-envelope/"
               xmlns:s12enc="http://www.w3.org/2003/05/soap-encoding/" xmlns:xs="http://www.w3.org/2001/XMLSchema"
               xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xmlns:senv="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/">
    <senv:Body>
        <tns:cancel_signatureResponse>
            <tns:cancel_signatureResult>
                <s0:Folios>
                    <s0:Folio>
                        <s0:UUID>A2D530D5-86EE-4ED6-AFB7-1C965D72A3E2</s0:UUID>
                        <s0:EstatusUUID>205</s0:EstatusUUID>
                        <s0:EstatusCancelacion></s0:EstatusCancelacion>
                    </s0:Folio>
                </s0:Folios>
                <s0:Acuse>&lt;s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"&gt;&lt;s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"&gt;&lt;CancelaCFDResponse xmlns="http://cancelacfd.sat.gob.mx"&gt;&lt;CancelaCFDResult Fecha="2019-04-10T11:10:38.8006999" RfcEmisor="TCM970625MB1"&gt;&lt;Folios&gt;&lt;UUID&gt;A2D530D5-86EE-4ED6-AFB7-1C965D72A3E2&lt;/UUID&gt;&lt;EstatusUUID&gt;205&lt;/EstatusUUID&gt;&lt;/Folios&gt;&lt;Signature Id="SelloSAT" xmlns="http://www.w3.org/2000/09/xmldsig#"&gt;&lt;SignedInfo&gt;&lt;CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/&gt;&lt;SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#hmac-sha512"/&gt;&lt;Reference URI=""&gt;&lt;Transforms&gt;&lt;Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116"&gt;&lt;XPath&gt;not(ancestor-or-self::*[local-name()='Signature'])&lt;/XPath&gt;&lt;/Transform&gt;&lt;/Transforms&gt;&lt;DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha512"/&gt;&lt;DigestValue&gt;mhUNI5/BiA8/CFjmzZbvJfdZryC00fUb7Zn84KkGqk8uC91BDrE41/OmwHjcX8yoh2BfeeT+3mi+o6Row7nQxg==&lt;/DigestValue&gt;&lt;/Reference&gt;&lt;/SignedInfo&gt;&lt;SignatureValue&gt;L29OH8JvLpsrDiydgAB3e9ETNKpRRa9AC2YF5jf/gSJFjh0g+9mhdGT81OiZl89dGl3FVLYQZaPiU+fogtlOYg==&lt;/SignatureValue&gt;&lt;KeyInfo&gt;&lt;KeyName&gt;BF66E582888CC845&lt;/KeyName&gt;&lt;KeyValue&gt;&lt;RSAKeyValue&gt;&lt;Modulus&gt;n5YsGT0w5Z70ONPbqszhExfJU+KY3Bscftc2jxUn4wxpSjEUhnCuTd88OK5QbDW3Mupoc61jr83lRhUCjchFAmCigpC10rEntTfEU+7qtX8ud/jJJDB1a9lTIB6bhBN//X8IQDjhmHrfKvfen3p7RxLrFoxzWgpwKriuGI5wUlU=&lt;/Modulus&gt;&lt;Exponent&gt;AQAB&lt;/Exponent&gt;&lt;/RSAKeyValue&gt;&lt;/KeyValue&gt;&lt;/KeyInfo&gt;&lt;/Signature&gt;&lt;/CancelaCFDResult&gt;&lt;/CancelaCFDResponse&gt;&lt;/s:Body&gt;&lt;/s:Envelope&gt;</s0:Acuse>
                <s0:Fecha>2019-04-10T11:10:38.8006999</s0:Fecha>
                <s0:RfcEmisor>TCM970625MB1</s0:RfcEmisor>
            </tns:cancel_signatureResult>
        </tns:cancel_signatureResponse>
    </senv:Body>
</senv:Envelope>
```

* Contenido del elemento `Acuse` (formateo de espacios agregados, original sin whitespace)

```xml
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
    <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <CancelaCFDResponse xmlns="http://cancelacfd.sat.gob.mx">
            <CancelaCFDResult Fecha="2019-04-10T11:10:38.8006999" RfcEmisor="TCM970625MB1">
                <Folios>
                    <UUID>A2D530D5-86EE-4ED6-AFB7-1C965D72A3E2</UUID>
                    <EstatusUUID>205</EstatusUUID>
                </Folios>
                <Signature Id="SelloSAT" xmlns="http://www.w3.org/2000/09/xmldsig#">
                    <SignedInfo>
                        <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
                        <SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#hmac-sha512"/>
                        <Reference URI="">
                            <Transforms>
                                <Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
                                    <XPath>not(ancestor-or-self::*[local-name()='Signature'])</XPath>
                                </Transform>
                            </Transforms>
                            <DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha512"/>
                            <DigestValue>mhUNI5/BiA8/CFjmzZbvJfdZryC00fUb7Zn84KkGqk8uC91BDrE41/OmwHjcX8yoh2BfeeT+3mi+o6Row7nQxg==</DigestValue>
                        </Reference>
                    </SignedInfo>
                    <SignatureValue>L29OH8JvLpsrDiydgAB3e9ETNKpRRa9AC2YF5jf/gSJFjh0g+9mhdGT81OiZl89dGl3FVLYQZaPiU+fogtlOYg==</SignatureValue>
                    <KeyInfo>
                        <KeyName>BF66E582888CC845</KeyName>
                        <KeyValue>
                            <RSAKeyValue>
                                <Modulus>n5YsGT0w5Z70ONPbqszhExfJU+KY3Bscftc2jxUn4wxpSjEUhnCuTd88OK5QbDW3Mupoc61jr83lRhUCjchFAmCigpC10rEntTfEU+7qtX8ud/jJJDB1a9lTIB6bhBN//X8IQDjhmHrfKvfen3p7RxLrFoxzWgpwKriuGI5wUlU=</Modulus>
                                <Exponent>AQAB</Exponent>
                            </RSAKeyValue>
                        </KeyValue>
                    </KeyInfo>
                </Signature>
            </CancelaCFDResult>
        </CancelaCFDResponse>
    </s:Body>
</s:Envelope>
```

## Reporte

2019-04-10 16:50 https://support.finkok.com/support/tickets/17700

Para no esperar el tiempo de respuesta habitual he marcado me han brindado excelente soporte técnico.
Resultado: El error no es de Finkok, es del SAT.

El servicio de cancelación funciona como un puente con el SAT, entre otras cosas, porque el SAT no tiene
abierto el servicio al público en general, solo a los PAC.

El error 205 no lo reporta Finkok, lo reporta el SAT (se puede ver en el acuse). Lo que lleva a preguntarnos:
¿Porqué entonces el SAT responde con Vigente/Cancelable en el servicio de consulta, y luego response con 205?
Porque es el SAT. Lo más probable es que por un lado almacene los CFDI y los ingrese en varios repositorios de
información, y por otros lugares consume esos repositorios. Luego entonces, el servicio de estado podría
retornar que sí existe y está vigente mientras que el servicio de cancelación podría decir que no existe.

## Solución

No existe solución.

Solo se puede reconocer el error e intentar más tarde.

El SAT no tiene una forma para poder advertir si se presentará un error 205.
