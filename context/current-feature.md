# Crear interfaz web de consulta de disponibilidad

## Objetivos

Crear una interfaz web mínima utilizando React y Tailwind CSS que permita consultar la disponibilidad de habitaciones a través del API existente. La interfaz debe integrarse en el proyecto Laravel y no requiere modificar ni ampliar el API.

### Requisitos

- Crear una pantalla web utilizando React.
- Utilizar React con Tailwind CSS para los elementos del formulario y de la interfaz.
- La pantalla debe tener un fondo blanco y una presentación sencilla, sin necesidad de desarrollar una interfaz de reservas completa.
- El formulario debe permitir introducir: fecha de entrada, fecha de salida, número de huéspedes y hotel mediante un selector opcional.
- El selector de hoteles debe obtener sus opciones mediante `GET /api/v1/hotels`.
- El selector debe permitir realizar la consulta sin seleccionar ningún hotel.
- El formulario no debe incluir un selector de tipo de habitación.
- Al enviar el formulario se debe realizar una petición `POST /api/v1/availability` con los datos introducidos.
- La petición debe utilizar los nombres y formatos definidos en el contrato OpenAPI existente.
- Los resultados de disponibilidad deben mostrarse debajo del formulario.
- Cada resultado debe mostrar, como mínimo, el hotel, el tipo de habitación y el precio.
- La interfaz debe mostrar un estado de carga mientras se realizan las peticiones.
- La interfaz debe mostrar de forma comprensible los errores producidos al cargar los hoteles o consultar la disponibilidad.
- La interfaz debe funcionar con los datos proporcionados actualmente por el API.

### Criterios de aceptación

- La aplicación muestra la pantalla de consulta al acceder a la interfaz web.
- El selector de hoteles se carga utilizando el endpoint `/api/v1/hotels`.
- Es posible realizar una consulta sin seleccionar un hotel.
- Es posible realizar una consulta seleccionando un hotel.
- La consulta envía correctamente las fechas, el número de huéspedes y, cuando corresponde, el código del hotel a `/api/v1/availability`.
- Los resultados recibidos del API se muestran debajo del formulario.
- Los resultados muestran el hotel, el tipo de habitación y el precio.
- La interfaz proporciona información visual durante las peticiones.
- Los errores de las peticiones se muestran al usuario.
- No es necesario modificar el contrato ni la implementación de los endpoints existentes.
- La aplicación puede construirse y ejecutarse utilizando la configuración existente del proyecto.

### Fuera de alcance

- No implementar autenticación.
- No implementar la creación de reservas desde la interfaz.
- No implementar selección de tipo de habitación.
- No modificar los endpoints existentes.
- No añadir funcionalidades de administración de hoteles o habitaciones.
- No desarrollar una interfaz de usuario completa para el motor de reservas.

## Notas

- Issue: #18
- Origen: GitHub Issue del repositorio.

## Histórico

- 2026-09-22: Implementada la feature "Sustituir el servicio mock por persistencia" (issue #16).
- 2026-09-21: Implementada la feature "Implementar disponibilidad" (issue #14).
- 2026-09-21: Implementada la feature "Implementar endpoints de consulta" (issue #12).
- 2026-09-21: Implementada la feature "Crear comandos y skills para el workflow de features" (issue #10).
- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).