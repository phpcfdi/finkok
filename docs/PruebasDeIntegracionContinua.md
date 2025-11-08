# Pruebas de integración contínua

Se ha configurado este proyecto para correr las pruebas de integración contínua utilizando la plataforma
de GitHub Actions.

El flujo de trabajo `build.yml` se ejecuta en cada ocasión que se hace *push* o *pull request* sobre la rama principal
y solo realiza las pruebas generales.

El flujo de trabajo `sonarqube-cloud.yml` se ejecuta en cada ocasión que se hace *push* sobre la rama principal
y realiza pruebas extendidas que usan credenciales encriptadas de Finkok.

## Pruebas generales

Las pruebas generales que se ejecutan tienen que ver con el estilo de código, pruebas unitarias (no funcionales),
y análisis estático de código. Estas pruebas son las que están vinculadas con el estado de la construcción
en la insignia *build*.

Adicionalmente, se ejecutan estas pruebas todos los domingos a las 16:00 horas (GMT-0).

## Pruebas funcionales

Las pruebas funcionales consisten en la ejecución de todas las pruebas con la excepción de aquellas que estén
marcadas con la etiqueta `@group large`.

Este tipo de pruebas hace contacto con la plataforma de pruebas de Finkok, por lo que es necesario contar
con una cuenta y configurar correctamente el archivo de configuración de entorno `.env`.

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
comando. Esta operación es la que se ejecuta en `sonarqube-cloud.yml` usando el secreto `secrets.ENV_GPG_SECRET`.

```shell
gpg --quiet --batch --yes --decrypt --output - tests/.env-testing.enc
```

## Cobertura de código reportada

Se está utilizando la plataforma SonarQube Cloud para análisis de código y para mostrar la cobertura de código.
Para su ejecución es necesario que el repositorio esté configurado junto con el secreto `secrets.SONAR_TOKEN`.

Se ejecutan las pruebas funcionales tal como fueron descritas anteriormente,
pero no se excluyen las pruebas marcadas con la etiqueta `@group large`

