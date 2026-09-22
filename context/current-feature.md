# Sustituir el servicio mock por persistencia

## Objetivos

Sustituir la implementación mock del servicio de datos por una implementación que obtenga los datos de la base de datos mediante Eloquent. La API debe mantener el comportamiento y el contrato actuales.

La aplicación debe utilizar SQLite como base de datos en el entorno de desarrollo local y PostgreSQL en producción. La implementación de la persistencia debe ser compatible con ambos motores y no depender de características específicas de uno de ellos.

Requisitos:

- Implementar el acceso a los datos utilizando los modelos Eloquent existentes en `app/Models`.
- Mantener el contrato definido en `app/Contracts`.
- Sustituir la implementación mock actualmente utilizada por el servicio de datos.
- Utilizar las relaciones Eloquent existentes entre los modelos cuando sea necesario.
- Crear o completar las migraciones necesarias para representar en la base de datos el modelo de datos utilizado actualmente por la aplicación.
- Crear o completar los seeders necesarios para disponer de datos de prueba reproducibles.
- Mantener el contrato de los endpoints existentes.
- Eliminar la dependencia del mock en el funcionamiento normal de la aplicación.
- Adaptar los tests existentes cuando sea necesario y añadir los tests necesarios para verificar la persistencia.

Criterios de aceptación:

- La aplicación puede ejecutarse utilizando exclusivamente los datos almacenados en la base de datos.
- Los datos utilizados por los endpoints de consulta proceden de Eloquent.
- El endpoint de disponibilidad funciona utilizando los datos persistidos.
- Los datos iniciales pueden generarse mediante los seeders de Laravel.
- Los endpoints mantienen el contrato existente.
- Los tests existentes continúan pasando.
- La implementación mock deja de utilizarse en el flujo normal de la aplicación.
- La aplicación funciona correctamente utilizando SQLite en el entorno local y PostgreSQL en producción, sin cambios en la lógica de acceso a datos.

Fuera de alcance:

- No añadir nuevos endpoints.
- No modificar el contrato de la API existente.
- No añadir nuevas funcionalidades al dominio de reservas.
- No implementar todavía nuevas operaciones de escritura sobre reservas.
- No modificar el frontend.

## Notas

- Issue: #16

## Histórico

- 2026-09-21: Implementada la feature "Implementar disponibilidad" (issue #14).
- 2026-09-21: Implementada la feature "Implementar endpoints de consulta" (issue #12).
- 2026-09-21: Implementada la feature "Crear comandos y skills para el workflow de features" (issue #10).
- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).