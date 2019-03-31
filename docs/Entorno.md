# Entorno de pruebas

Finkok requiere que tengas una cuenta con ellos. Por lo que es importante que tewngas tus datos a la mano.

## Pruebas

Para las pruebas se está utilizando el certificado de pruebas que corresponde a `EJIDO RODRIGUEZ PUEBLA SA DE CV`
[TCM970625MB1][https://wiki.finkok.com/lib/exe/fetch.php?media=certificadodepruebas5.zip] y caduca `2020-10-21`.

Los datos se encuentran en `tests/_files/certs/`:

- `TCM970625MB1.cer` Archivo de certificado (formato DER)
- `TCM970625MB1.key` Archivo de llave privada (formato DER)
- `TCM970625MB1.password.bin` Archivo con el password del certificado

Esta información es pública, por lo tanto no hay problema en publicarla aquí.

Recuerda registrar este RFC en tu panel de <https://demo-facturacion.finkok.com/>
Si no lo haces verás errores como estos:
- `No ha registrado el RFC emisor bajo la cuenta de Finkok`
- `Sorry there was an error when validating the reseller and user`
