# Implementar disponibilidad

## Objetivos

Implementar el endpoint de consulta de disponibilidad del API:

- `POST /api/v1/availability`

El endpoint devuelve los hoteles y tipos de habitación disponibles para el número de huéspedes y el intervalo solicitado.

Requisitos técnicos:

- La lógica de disponibilidad obtiene los datos a través del servicio de datos existente (mock en memoria).
- La implementación no depende de la forma concreta de almacenamiento del mock, de modo que la fuente pueda sustituirse por persistencia sin modificar la lógica de negocio.
- Respeta el contrato de la especificación OpenAPI vigente.

Lógica de disponibilidad:

- Se considera el número de huéspedes (`paxes`) solicitado.
- Solo son disponibles los tipos de habitación con `maxOccupancy >= paxes`.
- Se considera la cantidad de unidades (`quantity`) definida en `HotelRoomType`.
- Los bookings confirmados que se solapan con el intervalo solicitado reducen el inventario disponible.
- Solapamiento: `booking.checkin < requested.checkout` y `booking.checkout > requested.checkin`.
- Los bookings cancelados no consumen disponibilidad.
- Una habitación está disponible si los bookings confirmados solapados son inferiores a la cantidad de unidades del tipo de habitación en el hotel.
- Filtros opcionales por `hotel` y `roomType` (códigos).
- El `price` se obtiene del `HotelRoomType` correspondiente; no se implementa cálculo de tarifas.

Criterios de aceptación:

- `POST /api/v1/availability` implementado.
- Acepta un `AvailabilityRequest` y devuelve un array de `Availability` conforme al contrato OpenAPI.
- `paxes` determina los tipos de habitación que pueden alojarlos (`maxOccupancy` inferior excluido).
- Disponibilidad calculada con `quantity`, bookings confirmados solapados reducen inventario, bookings no solapados y `CANCELLED` no lo reducen.
- Tipo de habitación sin unidades disponibles durante el intervalo no se devuelve; con al menos una unidad se devuelve el elemento `Availability`.
- `price` corresponde al `HotelRoomType`.
- Filtros `hotel` y `roomType` limitan resultados cuando se proporcionan; sin ellos se consulta todo.
- Feature Tests que cubren: sin bookings solapados; bookings solapados; bookings cancelados; intervalo sin disponibilidad; capacidad insuficiente; filtros por hotel y tipo de habitación; precio del `HotelRoomType`; cambio de disponibilidad según el intervalo.
- La suite completa de tests continúa pasando.

Fuera de alcance:

- Persistencia, creación/cancelación de bookings, concurrencia, cálculo dinámico de tarifas, pricing, cambios del mock salvo los estrictamente necesarios, paginación, autenticación y autorización.

## Notas

- Issue: #14

## Histórico

- 2026-09-21: Implementada la feature "Implementar endpoints de consulta" (issue #12).
- 2026-09-21: Implementada la feature "Crear comandos y skills para el workflow de features" (issue #10).
- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).