# Automatizar el despliegue mediante GitHub Actions

## Objetivos

### Requisitos

1. Crear un workflow en `.github/workflows/` que pueda ejecutarse manualmente mediante `workflow_dispatch`.
2. Obtener el código del repositorio.
3. Construir la imagen Docker utilizando el `Dockerfile` existente.
4. Exportar la imagen a un archivo mediante `docker save` y comprimirla si resulta conveniente para su transferencia.
5. Transferir la imagen y el `compose.yaml` de despliegue al VPS mediante SSH/SCP.
6. Conectarse al VPS mediante SSH, cargar la imagen recibida en Docker y ejecutar o actualizar la aplicación mediante Docker Compose.
7. Dejar la aplicación accesible en el puerto configurado para el despliegue.
8. Permitir la ejecución posterior del workflow para actualizar una instalación existente.
9. Utilizar la imagen construida por GitHub Actions sin ejecutar `docker build` en el VPS.
10. Mantener una configuración de Docker Compose que permita añadir otros servicios en el futuro y que haga el despliegue idempotente en la medida de lo posible.

### Seguridad

- No almacenar credenciales, claves privadas ni otros secretos en el repositorio.
- Utilizar los GitHub Secrets `VPS_HOST`, `VPS_USER` y `VPS_SSH_KEY` para los datos sensibles necesarios.

### Restricciones de ejecución

- No ejecutar el workflow de GitHub Actions.
- No realizar ningún despliegue ni conectarse mediante SSH al VPS.
- No modificar la configuración del VPS.
- No crear, modificar ni eliminar recursos fuera del repositorio.
- Limitar la validación al análisis y comprobación local de los archivos generados y, cuando sea posible, a su validación sintáctica o estática.

### Criterios de aceptación

- El workflow aparece en `.github/workflows/` y utiliza `workflow_dispatch`.
- El workflow contiene todos los pasos necesarios para construir, empaquetar, transferir y desplegar la imagen.
- El workflow utiliza GitHub Secrets para las credenciales del VPS.
- El VPS no necesita ejecutar `docker build`.
- El `compose.yaml` utilizado por el despliegue utiliza la imagen construida por el workflow.
- La configuración permite conservar los datos de SQLite mediante el volumen definido en Compose.
- El workflow puede ejecutarse nuevamente para actualizar la aplicación.
- Los archivos generados se han validado localmente en la medida de lo posible.
- El workflow no se ha ejecutado como parte de esta issue.
- No se ha realizado ningún despliegue en el VPS como parte de esta issue.
- Ningún secreto o clave privada está almacenado en el repositorio.

## Notas

- Issue: #26
- Origen: https://github.com/profesorderedes/yabe/issues/26

## Histórico

- 2026-09-25: Implementada la feature "Dockerizar aplicación para ejecución local" (issue #24).
- 2026-09-23: Implementada la feature "Conectar el MCP Server con los datos reales" (issue #21).
- 2026-09-23: Implementada la feature "Implementar base de servidor MCP local" (issue #20).
- 2026-09-22: Implementada la feature "Sustituir el servicio mock por persistencia" (issue #16).
- 2026-09-21: Implementada la feature "Implementar disponibilidad" (issue #14).
- 2026-09-21: Implementada la feature "Implementar endpoints de consulta" (issue #12).
- 2026-09-21: Implementada la feature "Crear comandos y skills para el workflow de features" (issue #10).
- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).