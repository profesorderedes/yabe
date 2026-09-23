import json
import logging
import os
import sqlite3
from pathlib import Path
from typing import Any

from mcp.server.context import CallNext, ServerRequestContext
from mcp.server.mcpserver import MCPServer

PROJECT_ROOT = Path(__file__).resolve().parent.parent
LOGS_DIR = Path(__file__).resolve().parent / "logs"
LOGS_DIR.mkdir(parents=True, exist_ok=True)

protocol_logger = logging.getLogger("mcp.protocol")
operations_logger = logging.getLogger("mcp.operations")

for _logger, _file_name in (
    (protocol_logger, "protocol.log"),
    (operations_logger, "mcp.log"),
):
    if not _logger.handlers:
        _logger.setLevel(logging.INFO)
        _logger.propagate = False
        _handler = logging.FileHandler(LOGS_DIR / _file_name, encoding="utf-8")
        _handler.setFormatter(logging.Formatter("%(asctime)s %(message)s"))
        _logger.addHandler(_handler)

JSONRPC_VERSION = "2.0"


def _jsonable(obj: Any) -> Any:
    model_dump = getattr(obj, "model_dump", None)
    if callable(model_dump):
        return model_dump(by_alias=True, exclude_unset=True)
    return str(obj)


def _dumps(value: Any) -> str:
    return json.dumps(value, ensure_ascii=False, default=_jsonable)


def _log_jsonrpc(direction: str, message: dict[str, Any]) -> None:
    protocol_logger.info('%s %s', direction, _dumps({"message": message}))


class ProtocolLoggingMiddleware:
    async def __call__(self, ctx: ServerRequestContext, call_next: CallNext) -> Any:
        is_request = ctx.request_id is not None
        inbound: dict[str, Any] = {"jsonrpc": JSONRPC_VERSION, "method": ctx.method}
        if ctx.params is not None:
            inbound["params"] = ctx.params
        if is_request:
            inbound["id"] = ctx.request_id
        _log_jsonrpc("in", inbound)

        operations_entry: dict[str, Any] = {"operation": ctx.method}
        if ctx.method == "tools/call":
            params = ctx.params if isinstance(ctx.params, dict) else {}
            operations_entry["tool"] = params.get("name")
            operations_entry["arguments"] = params.get("arguments")

        try:
            result = await call_next(ctx)
            operations_entry["result"] = result
        except Exception as exc:
            operations_entry["result"] = {"error": str(exc)}
            if is_request:
                error: dict[str, Any] = {"code": getattr(exc, "code", -32000), "message": getattr(exc, "message", str(exc))}
                _log_jsonrpc(
                    "out",
                    {"jsonrpc": JSONRPC_VERSION, "id": ctx.request_id, "error": error},
                )
            operations_logger.info(_dumps(operations_entry))
            raise

        operations_logger.info(_dumps(operations_entry))
        if is_request:
            _log_jsonrpc(
                "out",
                {"jsonrpc": JSONRPC_VERSION, "id": ctx.request_id, "result": result},
            )
        return result


def _parse_env_file(path: Path) -> dict[str, str]:
    variables: dict[str, str] = {}
    if not path.is_file():
        return variables
    for raw_line in path.read_text(encoding="utf-8").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("#"):
            continue
        if line.startswith("export "):
            line = line[len("export "):].lstrip()
        key, separator, value = line.partition("=")
        if not separator:
            continue
        variables[key.strip()] = _unquote(_strip_inline_comment(value.strip()))
    return variables


def _strip_inline_comment(value: str) -> str:
    quote: str | None = None
    for index, char in enumerate(value):
        if char in ("'", '"'):
            if quote is None:
                quote = char
            elif char == quote:
                quote = None
        elif char == "#" and quote is None and (index == 0 or value[index - 1].isspace()):
            return value[:index].rstrip()
    return value


def _unquote(value: str) -> str:
    if len(value) >= 2 and value[0] == value[-1] and value[0] in ("'", '"'):
        return value[1:-1]
    return value


class ApplicationData:
    """Read access to the application persistence using its local configuration."""

    def __init__(self, project_root: Path = PROJECT_ROOT) -> None:
        self.project_root = project_root
        self._env = _parse_env_file(project_root / ".env")

    def database_path(self) -> Path:
        connection = self._env.get("DB_CONNECTION", "sqlite")
        if connection != "sqlite":
            raise RuntimeError(
                f"Unsupported DB_CONNECTION '{connection}': the MCP server only supports 'sqlite'."
            )
        database = self._env.get("DB_DATABASE", "database/database.sqlite")
        path = Path(database)
        if not path.is_absolute():
            path = self.project_root / path
        return path

    def _connect(self) -> sqlite3.Connection:
        path = self.database_path()
        if not path.is_file():
            raise RuntimeError(
                f"Application database not found at '{path}'. Create it with 'php artisan migrate --seed' before using the MCP server."
            )
        connection = sqlite3.connect(path, timeout=5)
        connection.row_factory = sqlite3.Row
        return connection

    def hotels(self) -> list[dict[str, Any]]:
        connection = self._connect()
        try:
            hotel_rows = connection.execute(
                "SELECT id, name, code FROM hotels ORDER BY id"
            ).fetchall()
            room_type_rows = connection.execute(
                """
                SELECT hrt.hotel_id, hrt.quantity, hrt.price,
                       rt.code, rt.name, rt.max_occupancy
                FROM hotel_room_types hrt
                JOIN room_types rt ON rt.id = hrt.room_type_id
                ORDER BY rt.id
                """
            ).fetchall()
        finally:
            connection.close()

        room_types_by_hotel: dict[int, list[dict[str, Any]]] = {}
        for row in room_type_rows:
            room_types_by_hotel.setdefault(row["hotel_id"], []).append(
                {
                    "roomType": {
                        "name": row["name"],
                        "code": row["code"],
                        "maxOccupancy": row["max_occupancy"],
                    },
                    "quantity": row["quantity"],
                    "price": row["price"],
                }
            )

        return [
            {
                "name": row["name"],
                "code": row["code"],
                "roomTypes": room_types_by_hotel.get(row["id"], []),
            }
            for row in hotel_rows
        ]

    def roomTypes(self) -> list[dict[str, Any]]:
        connection = self._connect()
        try:
            rows = connection.execute(
                "SELECT name, code, max_occupancy FROM room_types ORDER BY id"
            ).fetchall()
        finally:
            connection.close()

        return [
            {"name": row["name"], "code": row["code"], "maxOccupancy": row["max_occupancy"]}
            for row in rows
        ]

    def bookings(
        self,
        hotel: str | None = None,
        status: str | None = None,
    ) -> list[dict[str, Any]]:
        connection = self._connect()
        try:
            rows = connection.execute(
                """
                SELECT b.locator, b.paxes, b.checkin, b.checkout, b.status,
                       h.code AS hotel_code, rt.code AS room_type_code
                FROM bookings b
                JOIN hotels h ON h.id = b.hotel_id
                JOIN room_types rt ON rt.id = b.room_type_id
                WHERE (? IS NULL OR h.code = ?) AND (? IS NULL OR b.status = ?)
                ORDER BY b.checkin
                """,
                (hotel, hotel, status, status),
            ).fetchall()
        finally:
            connection.close()

        return [
            {
                "locator": row["locator"],
                "hotel": row["hotel_code"],
                "roomType": row["room_type_code"],
                "paxes": row["paxes"],
                "checkin": row["checkin"],
                "checkout": row["checkout"],
                "status": row["status"],
            }
            for row in rows
        ]

    def bookingsStatistics(self) -> dict[str, Any]:
        bookings = self.bookings()

        bookings_by_hotel: dict[str, int] = {}
        bookings_by_status: dict[str, int] = {}
        total_guests = 0

        for booking in bookings:
            bookings_by_hotel[booking["hotel"]] = bookings_by_hotel.get(booking["hotel"], 0) + 1
            bookings_by_status[booking["status"]] = bookings_by_status.get(booking["status"], 0) + 1
            total_guests += booking["paxes"]

        return {
            "total_bookings": len(bookings),
            "total_guests": total_guests,
            "bookings_by_hotel": bookings_by_hotel,
            "bookings_by_status": bookings_by_status,
        }


def _guard(operation: Any, *args: Any) -> Any:
    try:
        return operation(*args)
    except Exception as exc:
        return {"error": str(exc)}


server = MCPServer(name="hotels-mcp", version="0.2.0", middleware=[ProtocolLoggingMiddleware()])

data = ApplicationData()


@server.tool(description="List the hotels stored in the application, with their room types.")
def get_hotels() -> str:
    return json.dumps(_guard(data.hotels), ensure_ascii=False)


@server.tool(description="List the room types stored in the application.")
def get_room_types() -> str:
    return json.dumps(_guard(data.roomTypes), ensure_ascii=False)


@server.tool(
    description="List the bookings stored in the application, optionally filtered by hotel code and status.",
)
def get_bookings(hotel: str | None = None, status: str | None = None) -> str:
    return json.dumps(_guard(data.bookings, hotel, status), ensure_ascii=False)


@server.tool(description="Return statistics computed from the bookings stored in the application.")
def get_bookings_statistics() -> str:
    return json.dumps(_guard(data.bookingsStatistics), ensure_ascii=False)


if __name__ == "__main__":
    server.run()