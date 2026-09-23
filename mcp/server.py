import json
import logging
import os
from typing import Any

from mcp.server.context import CallNext, ServerRequestContext
from mcp.server.mcpserver import MCPServer

LOGS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "logs")

protocol_logger = logging.getLogger("mcp.protocol")
operations_logger = logging.getLogger("mcp.operations")

for _logger, _file_name in (
    (protocol_logger, "protocol.log"),
    (operations_logger, "mcp.log"),
):
    if not _logger.handlers:
        _logger.setLevel(logging.INFO)
        _logger.propagate = False
        _handler = logging.FileHandler(os.path.join(LOGS_DIR, _file_name), encoding="utf-8")
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


MOCK_HOTELS = [
    {"id": "H1", "name": "Hotel Central", "city": "Madrid"},
    {"id": "H2", "name": "Hotel Playa Dorada", "city": "Málaga"},
    {"id": "H3", "name": "Hotel Montaña Verde", "city": "Granada"},
]

MOCK_ROOM_TYPES = [
    {"id": "RT1", "name": "Single", "capacity": 1},
    {"id": "RT2", "name": "Double", "capacity": 2},
    {"id": "RT3", "name": "Family", "capacity": 4},
]

MOCK_BOOKINGS = [
    {"id": "B1", "hotel_id": "H1", "room_type_id": "RT2", "guest_name": "Ana García", "check_in": "2026-10-01", "check_out": "2026-10-03"},
    {"id": "B2", "hotel_id": "H2", "room_type_id": "RT1", "guest_name": "Luis Pérez", "check_in": "2026-10-02", "check_out": "2026-10-04"},
    {"id": "B3", "hotel_id": "H1", "room_type_id": "RT3", "guest_name": "Marta Ruiz", "check_in": "2026-10-05", "check_out": "2026-10-08"},
]


server = MCPServer(name="hotels-mcp", version="0.1.0", middleware=[ProtocolLoggingMiddleware()])


@server.tool(description="List the sample hotels available in the booking engine.")
def get_hotels() -> str:
    return json.dumps(MOCK_HOTELS, ensure_ascii=False)


@server.tool(description="List the sample room types available in the booking engine.")
def get_room_types() -> str:
    return json.dumps(MOCK_ROOM_TYPES, ensure_ascii=False)


@server.tool(
    description="List sample bookings, optionally filtered by hotel and booking status.",
)
def get_bookings(hotel_id: str | None = None, status: str | None = None) -> str:
    bookings = MOCK_BOOKINGS
    if hotel_id is not None:
        bookings = [b for b in bookings if b["hotel_id"] == hotel_id]
    if status is not None:
        bookings = [b for b in bookings if b.get("status") == status]
    return json.dumps(bookings, ensure_ascii=False)


@server.tool(description="Return simulated statistics about the sample bookings.")
def get_bookings_statistics() -> str:
    statistics = {
        "total_bookings": len(MOCK_BOOKINGS),
        "total_guests": 3,
        "bookings_by_hotel": {"H1": 2, "H2": 1},
    }
    return json.dumps(statistics, ensure_ascii=False)


if __name__ == "__main__":
    server.run()