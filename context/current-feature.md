# Preparar servicio de datos mock

## Objetivos

### Requisitos

- Implementar un pequeño servicio que proporcione datos mock en memoria para permitir la implementación y prueba de los endpoints del API sin necesidad de persistencia.
- El servicio debe encapsular los datos mock y proporcionar acceso a hoteles, tipos de habitación, la relación entre hoteles y tipos de habitación (incluyendo la cantidad de unidades disponibles) y bookings.
- Los datos deben ser coherentes entre sí y utilizar los modelos Eloquent existentes (`Hotel`, `RoomType`, `HotelRoomType` y `Booking`).
- El servicio será utilizado posteriormente por la implementación de los endpoints y debe mantener los controllers independientes de la estructura concreta de los datos mock.
- Los datos se mantienen en memoria y no requieren base de datos.
- No se implementará persistencia ni lógica de negocio en esta tarea.

### Criterios de aceptación

- Existe un servicio dedicado a proporcionar los datos mock.
- Los datos mock están encapsulados en el servicio y no están definidos directamente en los controllers.
- El servicio proporciona hoteles, room types, relaciones `HotelRoomType` y bookings.
- Los datos incluyen suficiente variedad para poder probar posteriormente los distintos endpoints del API.
- Las relaciones entre hoteles, room types y bookings son coherentes.
- Los `HotelRoomType` incluyen una cantidad de unidades para cada tipo de habitación.
- Existen bookings con distintos hoteles, room types y fechas, de forma que posteriormente puedan utilizarse para probar el cálculo de disponibilidad.
- Los datos se mantienen en memoria y no requieren base de datos.
- Los modelos Eloquent existentes se utilizan para representar los datos mock.
- La suite de tests existente continúa pasando.

### Fuera de alcance

- Persistencia en base de datos.
- Migraciones y seeders.
- Implementación de los endpoints del API.
- Cálculo de disponibilidad.
- Creación o cancelación de bookings mediante el API.
- Control de concurrencia.
- Modificación del contrato OpenAPI.

## Notas

- Issue: #7
- Origen: https://github.com/profesorderedes/yabe/issues/7

## Histórico

- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).