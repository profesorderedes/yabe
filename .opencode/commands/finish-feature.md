---
description: Ejecuta la fase de finalización de la feature de la rama actual y actualiza su contexto y estado.
---

# /finish-feature

Ejecuta la fase de Finalización de la feature de la rama actual.

1. Obtén el número del issue desde `$ARGUMENTS` o desde `context/current-feature.md`.
2. Carga el skill `feature-workflow`.
3. Comprueba que el issue corresponde a la feature actual.
4. Comprueba que la implementación está verificada y cumple los criterios de aceptación.
5. Comprueba que no estás trabajando directamente sobre `main`.
6. Revisa el estado, diff e historial de Git.
7. Actualiza `context/current-feature.md` siguiendo `context/current-feature-file-spec.md`:
   - cambia el encabezado principal a `# Feature actual`;
   - vacía `## Objetivos` y `## Notas`;
   - añade al principio de `## Histórico` una entrada de una línea que resuma el trabajo realizado;
   - conserva los encabezados y el histórico anterior.
8. Solicita confirmación explícita antes de publicar los cambios.
9. Crea el commit de finalización con `git add` de rutas explícitas y `git commit`.
10. Haz `push` de la rama actual. Si ya existe un Pull Request, este push lo actualiza; no crees uno nuevo.
11. Quita `in-progress` y añade `done` al issue.
12. Detente. No fusiones el Pull Request ni cierres el issue manualmente.