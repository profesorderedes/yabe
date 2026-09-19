# Especificación del descriptor de feature actual

## Estructura

- El descriptor es `@context/current-feature.md`.
- El encabezado de primer nivel contiene el nombre de la feature actual, que coincide con el título de su GitHub Issue (p. ej. `# Inicializar el proyecto Laravel`). Al finalizar una feature se utiliza `# Feature actual`.
- Contiene una sección `## Objetivos` que puede incluir requisitos, requisitos técnicos, criterios de aceptación y otras subsecciones convenientes.
- Contiene una sección `## Notas` que puede incluir información adicional de la feature y, mientras haya una feature activa, debe contener `- Issue: #<number>`.
- Contiene una sección `## Histórico` con una lista de entradas ordenadas de la más reciente a la más antigua.
- No contiene ninguna sección `## Referencia` ni otras secciones de nivel superior no especificadas.

## Restricciones

- El histórico no se eliminará nunca. La única modificación posible es añadir entradas.
- El encabezado de primer nivel representa el nombre de la feature: se establece como `# <título del issue>` al iniciar una nueva feature y se restaura a `# Feature actual` al finalizarla o reiniciar el descriptor.
- La feature activa, su GitHub Issue y este descriptor deben referirse al mismo issue.
- Los estados del issue se representan con los labels mutuamente excluyentes `in-progress` y `done`.
- Al iniciar una feature se añade `in-progress`; al finalizarla se elimina `in-progress` y se añade `done`.
- No se añadirá una sección `## Referencia` al descriptor.

## Acciones previstas

- Cuando el usuario pida actualizar el contenido a partir de información de una fuente para iniciar el desarrollo de una feature, se actualizará el encabezado de primer nivel con `# <título del issue>` (nombre de la feature) y las secciones `## Objetivos` y `## Notas`. No se añadirá una sección `## Referencia` ni una entrada a `## Histórico` indicando el inicio del desarrollo.
- Cuando el usuario pida añadir contenido al histórico, se añadirá una entrada, preservando el orden previsto, con un resumen de una línea del trabajo realizado en la sesión.
- Cuando el usuario pida limpiar o reiniciar este descriptor, se cambiará el encabezado de primer nivel a `# Feature actual`, se borrará el contenido de `## Objetivos` y `## Notas`, y se eliminarán las secciones no especificadas, preservando los encabezados requeridos.
- Al finalizar una feature se añadirá al principio de `## Histórico` una entrada de una línea que resuma el trabajo realizado.
- El issue no se cerrará manualmente; el Pull Request utilizará `Closes #<issue-number>` para cerrarlo al fusionarse.
