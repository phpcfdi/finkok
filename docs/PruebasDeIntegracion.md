# Pruebas de integración

Finkok requiere que tengas una cuenta con ellos. Por lo que es importante que tengas tus datos a la mano.

## Certificado de pruebas

Para las pruebas se está utilizando el certificado de pruebas que corresponde a `ESCUELA KEMPER URGATE SA DE CV`
[EKU9003173C9](https://wiki.finkok.com/lib/exe/fetch.php?media=csd_eku9003173c9_20190617131829.zip) y caduca
`2023-06-17`, antes se estaba usando TCM970625MB1, pero el SAT lo ha revocado.

Los datos se encuentran en `tests/_files/certs/`:

- `EKU9003173C9.cer` Archivo de certificado (formato DER)
- `EKU9003173C9.key` Archivo de llave privada (formato DER)
- `EKU9003173C9.password.bin` Archivo con el password del certificado

Esta información es pública, por lo tanto no hay problema en publicarla aquí.

Recuerda registrar este RFC en tu panel de <https://demo-facturacion.finkok.com/>
Si no lo haces verás errores como estos:
- `No ha registrado el RFC emisor bajo la cuenta de Finkok`
- `Sorry there was an error when validating the reseller and user`

## Archivo de entorno `.env`

Si ya registraste el RFC EKU9003173C9 en tu panel de Finkok, entonces ahora debes configurar
el archivo `test/.env` de entorno. Este tipo de archivos se usa con mucha frecuencia para configurar
entornos de ejecución. Puedes usar el archivo `test/.env-example` como base.

Una vez que lo configures te recomiendo ejecutar el test inocuo de `datetime`.

```text
php vendor/bin/phpunit --testdox tests/Integration/Services/Utilities/DatetimeServiceTest.php 
Services/Utilities/DatetimeServiceTest.php
PHPUnit 9.5.3 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.0.3
Configuration: /home/eclipxe/work/PhpCfdi/finkok/phpunit.xml.dist

Datetime Service (PhpCfdi\Finkok\Tests\Integration\Services\Utilities\DatetimeService)
 ✔ Two well known different postal codes  1173 ms
 ✔ Consume date time service  355 ms
 ✔ Consume date time service using invalid username password  314 ms

Time: 00:01.843, Memory: 6.00 MB
```

## Ejecución de pruebas

Las pruebas de integración no están incluidas en el comando `composer dev:test`. Hay que correrlas a mano ejecutando:

```shell
vendor/bin/phpunit tests/Integration --testdox
```

Lee la [guía de contribuciones](../CONTRIBUTING.md) para más información.
