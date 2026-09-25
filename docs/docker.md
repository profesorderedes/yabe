# Ejecución local con Docker

La aplicación puede ejecutarse completa mediante Docker Compose, sin necesidad de instalar PHP, Composer, Node.js ni las dependencias de la aplicación en el sistema anfitrión.

## Requisitos previos

- Docker Engine con Docker Compose v2 (incluido por defecto en Docker Desktop y en las distribuciones recientes de Docker Engine).
- Puertos libres en el host: `8000`.

## Construir y arrancar

Desde la raíz del repositorio:

```bash
docker compose up --build
```

El primer arranque construye la imagen (dependencias PHP, dependencias JavaScript y assets de Vite) y levanta el contenedor. Con una imagen ya construida basta con:

```bash
docker compose up
```

## Acceder a la aplicación

La aplicación queda disponible en:

```text
http://localhost:8000
```

El endpoint de salud de Laravel está en:

```text
http://localhost:8000/up
```

## Detener el entorno

Detiene el contenedor conservando los datos de la base de datos, de forma que el siguiente arranque reutiliza la misma información:

```bash
docker compose down
```

## Eliminar el entorno

Elimina también el contenedor y el volumen con los datos de la base de datos (`-v`):

```bash
docker compose down -v
```

Al volver a levantarlo, la aplicación recrea la estructura de la base de datos y los datos iniciales automáticamente.

## Reconstruir la imagen

Para reconstruir la imagen cuando sea necesario (p. ej., tras actualizar las dependencias PHP o JavaScript):

```bash
docker compose build --no-cache
# o, construir y arrancar de nuevo directamente:
docker compose up --build
```

## Persistencia de SQLite

La base de datos SQLite (`database.sqlite`) se guarda en el directorio `data/` de la aplicación, dentro del contenedor, que se persiste mediante el volumen nombrado `yabe_sqlite-data`. Esta decisión tiene las siguientes consecuencias:

- El fichero de base de datos **no** se almacena en el sistema de archivos del host ni se versiona en el repositorio.
- `docker compose down` conserva los datos; `docker compose down -v` los elimina.
- Al arrancar, el contenedor ejecuta automáticamente las migraciones (`migrate`) y el seed (`db:seed`). Ambas operaciones son idempotentes, por lo que pueden ejecutarse en cada arranque sin duplicar datos.
- La ruta se configura mediante `DB_DATABASE=/var/www/html/data/database.sqlite`, que el entrypoint añade al `.env` del contenedor en cada arranque. Se guarda en `.env` (y no solo como variable de entorno) porque el servidor integrado de PHP (`php artisan serve`) filtra las variables de entorno del proceso que atiende las peticiones HTTP.

## Notas técnicas

- La imagen es de varias etapas (`multi-stage`): las dependencias de Composer y los assets de Vite se construyen en etapas intermedias y solo se copia el resultado a la imagen final.
- El contenedor ejecuta Laravel con el servidor integrado de PHP escuchando en `0.0.0.0:8000` (`php artisan serve --host=0.0.0.0 --port=8000`).
- El usuario de la imagen (no `root`) es `www-data`; el entrypoint prepara los permisos de los directorios de escritura y genera la `APP_KEY` en el primer arranque si no existe.
- Esta configuración está pensada para ejecución local y sirve como base para un futuro despliegue en un VPS, pero el despliegue en producción queda fuera del alcance de esta documentación.