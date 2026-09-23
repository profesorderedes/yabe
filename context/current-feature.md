# Implementar base de servidor MCP local

## Objetivos

Crear un servidor MCP local en Python que pueda ser utilizado por OpenCode mediante `stdio`.

El servidor se instalará en el directorio `mcp/` del proyecto y utilizará el SDK oficial de MCP para Python.

En esta primera versión no se debe implementar ninguna lógica de acceso a los datos de la aplicación. El objetivo es disponer de un servidor MCP funcional que permita observar el descubrimiento e invocación de herramientas y analizar la comunicación entre el cliente y el servidor desde dos niveles de abstracción.

### Requisitos

- Crear un servidor MCP en Python dentro del directorio `mcp/`.
- Utilizar el SDK oficial de MCP para Python.
- Exponer las herramientas: `get_hotels`, `get_room_types`, `get_bookings` y `get_bookings_statistics`.
- Las herramientas pueden devolver resultados simulados.
- Añadir dos ficheros de log en `mcp/logs/`:
  - **`mcp/logs/protocol.log`**: registrar, si técnicamente es posible, los mensajes JSON-RPC intercambiados mediante `stdio`.
  - **`mcp/logs/mcp.log`**: registrar información descriptiva de las operaciones procesadas: operación MCP, herramienta invocada, argumentos recibidos y resultado o respuesta generada.
- El logging no debe interferir con la comunicación MCP; no escribir diagnósticos en `stdout`.
- Documentar, en español, cómo configurar y ejecutar el servidor con OpenCode:
  - Ejecutar con un intérprete Python donde esté instalada la dependencia `mcp`.
  - Incluir instrucciones de creación y activación de un entorno virtual.
  - Usar un comando explícito en la configuración (p. ej. `"command": [".venv/bin/python", "mcp/server.py"]`).
  - Indicar que las rutas relativas dependen del directorio de trabajo de OpenCode.
- No depender de APIs internas ni monkey patches del SDK para interceptar solicitudes.
- Verificar los logs usando una conexión real de cliente MCP.

### Criterios de aceptación

- Existe un servidor MCP implementado en Python dentro del directorio `mcp/`.
- El servidor utiliza el SDK oficial de MCP para Python.
- El servidor puede ser iniciado por OpenCode como servidor MCP local mediante `stdio`.
- OpenCode puede conectarse correctamente al servidor.
- OpenCode puede descubrir las herramientas mediante `tools/list`.
- El servidor expone las herramientas `get_hotels`, `get_room_types`, `get_bookings` y `get_bookings_statistics`.
- OpenCode puede invocar al menos una de las herramientas mediante `tools/call` y recibe una respuesta válida.
- Las herramientas no acceden a la persistencia de la aplicación y pueden utilizar datos simulados.
- Existe el directorio `mcp/logs/` y los ficheros de log se almacenan en él.
- `mcp.log` permite identificar las operaciones realizadas por el servidor y sus resultados.
- `protocol.log` registra los mensajes JSON-RPC intercambiados a través de `stdio`, si el mecanismo utilizado para su captura lo permite.
- El logging no altera ni interrumpe la comunicación MCP.
- El comando `opencode mcp list` debe mostrar el servidor como conectado, sin `Connection closed`.
- La configuración y las instrucciones necesarias para ejecutar el servidor y conectarlo con OpenCode están documentadas en el propio proyecto en idioma español.

### Fuera de alcance

- Acceso a la persistencia de la aplicación.
- Consultas reales sobre hoteles o reservas.
- Autenticación o autorización.
- Análisis de datos.
- Recursos MCP (`resources`).
- Transporte HTTP.
- Implementación de lógica de negocio relacionada con las reservas.

## Notas

- Issue: #20
- Origen: GitHub Issue del repositorio.

## Histórico

- 2026-09-22: Implementada la feature "Sustituir el servicio mock por persistencia" (issue #16).
- 2026-09-21: Implementada la feature "Implementar disponibilidad" (issue #14).
- 2026-09-21: Implementada la feature "Implementar endpoints de consulta" (issue #12).
- 2026-09-21: Implementada la feature "Crear comandos y skills para el workflow de features" (issue #10).
- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).