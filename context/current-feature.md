# Conectar el MCP Server con los datos reales

## Objetivos

- Completar la implementación del MCP Server desarrollado en la issue anterior para que sus herramientas consulten los datos reales de la aplicación.
- Utilizar la misma configuración de persistencia que utiliza la aplicación en el entorno local, evitando una segunda fuente de datos o una configuración independiente de la persistencia.
- Las herramientas MCP de la primera iteración deben dejar de utilizar datos simulados y obtener la información directamente de la persistencia: `get_hotels`, `get_room_types`, `get_bookings` y `get_bookings_statistics`.
- Mantener el mecanismo de logging establecido en la primera iteración, de forma que las consultas y sus resultados puedan observarse mediante `mcp.log`.

## Criterios de aceptación

- El MCP Server continúa ejecutándose localmente mediante `stdio` y puede ser utilizado por OpenCode.
- `get_hotels` devuelve los hoteles existentes en la persistencia local de la aplicación.
- `get_room_types` devuelve los tipos de habitación existentes en la persistencia local de la aplicación.
- `get_bookings` devuelve las reservas existentes en la persistencia local de la aplicación.
- `get_bookings_statistics` devuelve estadísticas calculadas a partir de los datos reales de reservas.
- Las herramientas no utilizan datos simulados.
- El MCP Server utiliza la configuración de acceso a la persistencia de la aplicación existente en el entorno local.
- Los errores producidos durante las consultas se gestionan adecuadamente y se comunican al cliente MCP sin provocar la terminación inesperada del servidor.
- Las invocaciones de las herramientas y sus resultados continúan registrándose en `mcp/logs/mcp.log`.
- Los mensajes del protocolo MCP continúan registrándose en `mcp/logs/protocol.log` cuando el mecanismo de captura implementado en la primera iteración lo permita.
- Es posible utilizar OpenCode para realizar consultas en lenguaje natural cuya respuesta requiera utilizar los datos reales de la aplicación.

## Fuera de alcance

- Incorporación de nuevas herramientas MCP distintas de las definidas en la primera iteración.
- Implementación de `resources` MCP.
- Transporte HTTP.
- Autenticación o autorización específica del MCP Server.
- Implementación de un sistema de análisis avanzado o generación de informes.
- Integración con servicios o fuentes de datos externas.

## Notas

- Issue: #21
- Origen: GitHub Issue del repositorio.

## Histórico

- 2026-09-23: Implementada la feature "Implementar base de servidor MCP local" (issue #20).
- 2026-09-22: Implementada la feature "Sustituir el servicio mock por persistencia" (issue #16).
- 2026-09-21: Implementada la feature "Implementar disponibilidad" (issue #14).
- 2026-09-21: Implementada la feature "Implementar endpoints de consulta" (issue #12).
- 2026-09-21: Implementada la feature "Crear comandos y skills para el workflow de features" (issue #10).
- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).