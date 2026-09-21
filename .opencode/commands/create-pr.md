---
description: Prepara y crea el Pull Request de la feature asociada a un GitHub Issue.
---

# /create-pr

Crea el Pull Request de la feature asociada a un GitHub Issue.

Recibes el número del issue en `$ARGUMENTS`.

1. Carga los skills `feature-workflow` y `pull-request`.
2. Comprueba la rama actual y el estado del repositorio. Trabaja sobre la rama de la feature, nunca directamente sobre `main`.
3. Verifica que el issue corresponde a la feature actual de `context/current-feature.md`.
4. Identifica los archivos modificados por la tarea y evita incluir cambios ajenos.
5. Ejecuta las verificaciones necesarias.
6. Muestra los cambios que se van a publicar.
7. Solicita confirmación explícita antes de publicar.
8. Si hay cambios sin publicar, créalos con `git add` de rutas explícitas y `git commit`.
9. Haz `push` de la rama actual.
10. Si ya existe un Pull Request para la rama, no crees otro: informa de su URL.
11. Si no existe, crea el Pull Request contra `main` e incluye `Closes #<número>` en el cuerpo.
12. Informa de la URL del Pull Request y detente. No fusiones el Pull Request ni cierres el issue.