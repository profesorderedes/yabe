# Crear comandos y skills para el workflow de features

## Objetivos

### Requisitos

- Definir los comandos y skills de OpenCode necesarios para establecer un workflow reproducible para el desarrollo de features a partir de GitHub Issues.
- Evitar concentrar toda la lógica del workflow en `AGENTS.md`, utilizando instrucciones reutilizables y comandos explícitos.
- El workflow debe permitir iniciar una feature desde un issue, implementar y verificar sus cambios, publicar tempranamente un Pull Request para facilitar su revisión y, finalmente, cerrar la fase de desarrollo de la feature y actualizar su contexto.
- Crear el skill `feature-workflow` en `.opencode/skills/feature-workflow/SKILL.md`, que defina las fases del workflow, el contexto a cargar en cada fase y la relación entre una feature, su GitHub Issue y `context/current-feature.md`.
- Crear el skill `pull-request` en `.opencode/skills/pull-request/SKILL.md`, que defina las reglas y comprobaciones para preparar y publicar un Pull Request.
- Crear los comandos `/start-feature`, `/create-pr` y `/finish-feature`.
- Modificar `AGENTS.md` para activar `feature-workflow` de forma condicional para las tareas relacionadas con features.
- Mantener `context/current-feature.md` como descriptor de la feature activa y documentar su formato en `context/current-feature-file-spec.md`.
- Representar los estados de la feature con los labels mutuamente excluyentes `in-progress` y `done`.

### Criterios de aceptación

- Existe el skill `feature-workflow` en `.opencode/skills/feature-workflow/SKILL.md`.
- Existe el skill `pull-request` en `.opencode/skills/pull-request/SKILL.md`.
- Existe el comando `/start-feature`.
- Existe el comando `/create-pr`.
- Existe el comando `/finish-feature`.
- `AGENTS.md` activa `feature-workflow` para las tareas relacionadas con features.
- Existe `context/current-feature-file-spec.md` con la especificación del descriptor.
- `/start-feature` prepara `current-feature.md`, asocia el issue y lo marca como `in-progress`, deteniéndose antes de implementar.
- `/create-pr` publica los cambios y crea un Pull Request asociado al issue mediante `Closes #<issue-number>`.
- El Pull Request puede recibir nuevos commits después de su creación.
- `/finish-feature` actualiza y limpia `current-feature.md` sin eliminar su histórico.
- `/finish-feature` puede ejecutarse cuando ya existe un Pull Request para la rama.
- `/finish-feature` publica los cambios finales en la misma rama y, por tanto, actualiza el Pull Request existente.
- `/finish-feature` cambia el issue de `in-progress` a `done`.
- Ninguno de los comandos hace push directamente a `main`.
- Las operaciones externas requieren confirmación explícita.
- Los cambios ajenos a la tarea no se incorporan accidentalmente a los commits.
- El workflow completo permite pasar de un GitHub Issue a un Pull Request revisable y posteriormente a un Pull Request listo para merge.

### Fuera de alcance

- Fusionar el Pull Request.
- Cerrar el issue manualmente (GitHub lo cierra al fusionar un Pull Request con `Closes #10`).

## Notas

- Issue: #10
- Origen: https://github.com/profesorderedes/yabe/issues/10

## Histórico

- 2026-09-20: Implementada la feature "Preparar servicio de datos mock" (issue #7).
- 2026-09-20: Implementada la feature "Implementar el esqueleto del API" (issue #5).
- 2026-09-20: Implementada la feature "Inicializar el proyecto Laravel" (issue #3).