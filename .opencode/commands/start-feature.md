---
description: Inicia una feature a partir de un GitHub Issue y prepara su contexto.
---

# /start-feature

Inicia el desarrollo de una feature a partir de un GitHub Issue.

Recibes el número del issue en `$ARGUMENTS`.

1. Carga el skill `feature-workflow` y los ficheros de contexto que indique.
2. Consulta el issue con `gh` (título, descripción, labels, comentarios y criterios de aceptación).
3. Antes de cualquier operación externa sobre el issue, muestra el estado y solicita confirmación explícita.
4. Actualiza `context/current-feature.md` siguiendo `context/current-feature-file-spec.md`:
   - encabezado de primer nivel con el título del issue;
   - `## Objetivos` con los requisitos y criterios de aceptación del issue;
   - `## Notas` con `- Issue: #<número>` y el origen del issue;
   - preserva `## Histórico` sin añadir entradas.
5. Marca el issue como `in-progress`.
6. Detente. No implementes la feature, no crees commits ni Pull Requests.