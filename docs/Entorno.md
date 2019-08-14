# Entorno de pruebas

Finkok requiere que tengas una cuenta con ellos. Por lo que es importante que tewngas tus datos a la mano.

## Pruebas

Para las pruebas se está utilizando el certificado de pruebas que corresponde a `ESCUELA KEMPER URGATE SA DE CV`
[EKU9003173C9][https://wiki.finkok.com/lib/exe/fetch.php?media=csd_eku9003173c9_20190617131829.zip] y caduca
`2023-06-17`, antes se estaba usando TCM970625MB1 pero el SAT lo ha revocado.

Los datos se encuentran en `tests/_files/certs/`:

- `EKU9003173C9.cer` Archivo de certificado (formato DER)
- `EKU9003173C9.key` Archivo de llave privada (formato DER)
- `EKU9003173C9.password.bin` Archivo con el password del certificado

Esta información es pública, por lo tanto no hay problema en publicarla aquí.

Recuerda registrar este RFC en tu panel de <https://demo-facturacion.finkok.com/>
Si no lo haces verás errores como estos:
- `No ha registrado el RFC emisor bajo la cuenta de Finkok`
- `Sorry there was an error when validating the reseller and user`
