# Pruebas de integración contínua

Se ha configurado este proyecto para correr las pruebas de integración contínua utilizando la plataforma
de GitHub Actions.

Hay dos trabajos de ejecución que ejecutan al hacer un push o un pull request sobre la rama principal:

- Prueba general de contrucción `build.yml`.
- Pruebas funcionales `functional-test.yml`.

## Pruebas generales

Las pruebas generales que se ejecutan tienen que ver con el estilo de código, pruebas unitarias (no funcionales),
y anásis estático de código. Estas pruebas son las que están vinculadas con el estado de la contrucción
en el *badge* *build*.

Adicionalmente, se ejecutan estas pruebas todos los domingos a las 16:00 horas.

## Pruebas funcionales

Las pruebas funcionales consisten en la ejecución de todas las pruebas con la excepción de aquellas que estén
marcadas con la etiqueta `@group large`.

Este tipo de pruebas hace contacto con la plataforma de pruebas de Finkok, por lo que es necesario contar
con una cuenta y configurar correctamente el archivo de configuración de entorno `.env`.
Lee el archivo de [Pruebas de Integracion](PruebasDeIntegracion.md) para más información.

### Protección de los datos de configuración de entorno

Las pruebas funcionales dependen del archivo de configuración de entorno `tests/.env`, el cual contiene
información sensible como la credencial de Finkok. Para protegerlo se utiliza el almacenamiento de secretos
de GitHub <https://docs.github.com/es/actions/reference/encrypted-secrets> y GPG para encriptar y desencriptar
el archivo de configuración de entorno.

El secreto en cuestión está almacenado en `secrets.ENV_GPG_SECRET` a nivel repositorio.

Para encriptar el archivo de configuración `tests/.env -> tests/.env-testing.enc`, al ejecutar el comando
se solicitará la frase de contraseña, que es lo que se debe almacenar en el secreto.

```shell
gpg --no-symkey-cache --symmetric --cipher-algo AES256 --output tests/.env-testing.enc tests/.env
```

Para desencriptar el archivo de configuración `tests/.env-testing.enc -> tests/.env` se puede usar el siguiente
comando. Esta operación es la que se ejecuta en `functional-test.yml` usando el secreto `secrets.ENV_GPG_SECRET`.

```shell
gpg --quiet --batch --yes --decrypt --output decoded tests/.env-testing.enc
```

### Cobertura de código

Las pruebas de funcionales son las que establecen la mayor cobertura de código, entonces, en su ejecución
se genera el archivo de cobertura y se publica en Scrutinizer.
