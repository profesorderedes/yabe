# Dockerizar aplicación para ejecución local

## Objetivos

Preparar la aplicación para poder ejecutarla mediante Docker Compose, sin necesidad de instalar PHP, Composer, Node.js ni las dependencias de la aplicación en el sistema anfitrión. La configuración debe servir como base para un futuro despliegue de la misma imagen Docker en un VPS.

Requisitos:

- Crear un `Dockerfile` para construir la imagen de la aplicación.
- Instalar las dependencias PHP mediante `composer install`.
- Instalar las dependencias JavaScript mediante `npm ci`.
- Construir los assets de React/Vite mediante `npm run build`.
- Utilizar SQLite como base de datos.
- La aplicación debe poder inicializar su base de datos mediante las migraciones y el seed de Laravel.
- Crear un `compose.yaml` que permita levantar la aplicación con un único comando.
- La aplicación debe quedar accesible desde el host mediante el puerto `8000`.
- El contenedor debe ejecutar Laravel escuchando en una interfaz accesible desde fuera del contenedor.
- La configuración debe permitir eliminar y recrear el contenedor sin necesidad de reconstruir manualmente el entorno.
- Los datos necesarios para la base de datos SQLite deben gestionarse de forma coherente con este objetivo.

Criterios de aceptación:

- Desde una instalación limpia, debe ser posible ejecutar `docker compose up --build` y acceder a la aplicación en `http://localhost:8000`.
- La aplicación debe arrancar correctamente y disponer de la estructura de base de datos y los datos iniciales proporcionados por las migraciones y el seed de Laravel.
- Debe ser posible detener y eliminar el entorno y volver a levantarlo siguiendo las instrucciones documentadas.

Documentación (en español):

- Construir y arrancar la aplicación.
- Acceder a ella.
- Detener el entorno.
- Eliminar el entorno.
- Reconstruir la imagen cuando sea necesario.
- Explica cualquier decisión relevante sobre la persistencia de SQLite.

Fuera del alcance: despliegue en un VPS, SSH, GitHub Actions, configuración de producción, HTTPS, PostgreSQL y servidor web externo como Nginx.

## Notas

- Issue: #24
- Origen: GitHub Issue del repositorio.

## Histórico

- 2026-09-23: Implementada la feature "Conectar el MCP Server con los datos reales" (issue #21).
- 2026-09-23: Implementada la feature "Implementar base de servidor MCP local" (issue #20).
- 2026-09-22: Implementada la feature "Sustituir el servicio mock por persistencia" (issue #16).
- 2026-09-21: Implementada la feature "Implementar disponibilidad" (issue #14).
- 2026-09-21: Implementada la feature "Implementar endpoints de consulta" (issue #12).
- 2026-09-21: Implementada la feature "Crear comandos y skills para el workflow de features" (issue #10).
- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).