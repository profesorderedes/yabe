---
name: feature-workflow
description: Define el workflow para iniciar, implementar, verificar o finalizar una feature a partir de un GitHub Issue. Usar cuando la tarea implique un GitHub Issue, context/current-feature.md o los comandos /start-feature, /create-pr y /finish-feature.
---

# Workflow de desarrollo de features

El desarrollo de una feature se divide en tres fases:

1. **Inicio**
2. **Implementación y verificación**
3. **Finalización**

Las fases son explícitas y están dirigidas por el usuario. El agente no debe avanzar a una fase posterior sin una solicitud explícita del usuario.

## Relación feature, issue y descriptor

- Cada feature se corresponde con un GitHub Issue del repositorio.
- El descriptor de la feature activa es `context/current-feature.md`.
- La feature activa, su GitHub Issue y el descriptor deben referirse al mismo issue.
- El formato del descriptor se especifica en `context/current-feature-file-spec.md`.
- Los estados de la feature se representan con los labels mutuamente excluyentes `in-progress` y `done`.
- El issue no se cierra manualmente; el Pull Request utiliza `Closes #<issue-number>` para que GitHub lo cierre al fusionarse.

## Inicio

Contexto a cargar: `context/current-feature.md`, `context/current-feature-file-spec.md` y `context/coding-conventions.md`.

- Consultar el issue: título, descripción, labels, comentarios y criterios de aceptación.
- Actualizar `context/current-feature.md` con la información de la feature según la especificación: encabezado con el título del issue, `## Objetivos` y `## Notas` con `Issue: #<number>`, preservando `## Histórico`.
- Añadir el label `in-progress` al issue.
- Solicitar confirmación explícita antes de cualquier operación externa.
- Detenerse: no implementar la feature, no crear commits ni Pull Requests.

## Implementación y verificación

Contexto a cargar: `context/current-feature.md` y `context/coding-conventions.md`.

- Implementar la feature cumpliendo su especificación y criterios de aceptación.
- Ejecutar las verificaciones necesarias y mantener la suite de tests pasando.
- No publicar cambios sin una solicitud explícita del usuario.

## Finalización

Contexto a cargar: `context/current-feature.md` y `context/current-feature-file-spec.md`.

- Comprobar que la implementación está verificada y cumple los criterios de aceptación.
- No trabajar directamente sobre `main`.
- Actualizar y limpiar el descriptor: encabezado `# Feature actual`, vaciar `## Objetivos` y `## Notas`, añadir al principio de `## Histórico` una entrada de una línea con el trabajo realizado.
- Publicar los cambios finales en la misma rama de la feature.
- Quitar `in-progress` y añadir `done` al issue.
- No fusionar el Pull Request ni cerrar el issue manualmente.

## Regla fundamental

Después de completar cada fase, detente y espera instrucciones del usuario antes de continuar con la siguiente.