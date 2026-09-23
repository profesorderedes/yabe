# Servidor MCP local

Servidor MCP local implementado en Python que OpenCode puede iniciar mediante `stdio`.

## Requisitos

- Python 3.12 o superior.
- En Debian/Ubuntu puede ser necesario instalar el paquete `python3-venv`:

  ```bash
  sudo apt install python3.12-venv
  ```

## Entorno virtual

Crear y activar el entorno virtual e instalar las dependencias:

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r mcp/requirements.txt
```

Para desactivar el entorno: `deactivate`.

## Configuración en OpenCode

OpenCode debe ejecutar el servidor con un intérprete Python donde esté instalada la dependencia `mcp`. Este proyecto incluye la configuración en `opencode.json`:

```json
{
  "mcp": {
    "hotels-mcp": {
      "type": "local",
      "command": [".venv/bin/python", "mcp/server.py"],
      "enabled": true
    }
  }
}
```

Notas:

- El comando usa una ruta relativa al directorio de trabajo de OpenCode (`<cwd>/.venv/bin/python`). Si se ejecuta OpenCode desde otra ubicación, ajusta la ruta en consecuencia.
- Después de cambiar la configuración, reinicia OpenCode para que la cargue.

### Verificación de la conexión

```bash
opencode mcp list
```

El servidor `hotels-mcp` debe aparecer conectado, sin el estado `Connection closed`.

## Herramientas expuestas

| Herramienta                 | Descripción                                            |
| --------------------------- | ------------------------------------------------------ |
| `get_hotels`                | Hoteles de la aplicación con sus tipos de habitación.  |
| `get_room_types`            | Tipos de habitación de la aplicación.                  |
| `get_bookings`              | Reservas de la aplicación, filtrables por código de hotel y estado. |
| `get_bookings_statistics`   | Estadísticas calculadas sobre las reservas reales.     |

Las herramientas consultan directamente la persistencia local de la aplicación y no utilizan datos simulados.

## Acceso a la persistencia

El servidor lee la configuración de persistencia del `.env` de la aplicación (en la raíz del proyecto) y abre en modo lectura la misma base de datos que utiliza la aplicación en el entorno local:

- `DB_CONNECTION=sqlite`: configuración soportada.
- `DB_DATABASE`: ruta de la base de datos; si no se define, se utiliza `database/database.sqlite`.

Antes de usar el servidor asegúrate de que la base de datos exista y tenga datos (p. ej. `php artisan migrate --seed`). Si no se encuentra o la conexión no es `sqlite`, las herramientas devuelven un error al cliente sin terminar el servidor.

## Logs

Los logs se almacenan en `mcp/logs/`:

- **`protocol.log`**: mensajes JSON-RPC intercambiados entre el cliente y el servidor, reconstruidos a partir de las solicitudes y respuestas observadas en el punto de intercepción del SDK.
- **`mcp.log`**: información descriptiva de las operaciones procesadas: operación MCP, herramienta invocada, argumentos recibidos y resultado generado.

La comunicación MCP utiliza `stdout` exclusivamente; los logs se escriben en ficheros y los diagnósticos propios del SDK van a `stderr`, sin interferir con el protocolo.