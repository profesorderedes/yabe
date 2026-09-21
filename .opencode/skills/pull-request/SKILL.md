---
name: pull-request
description: Define las reglas y comprobaciones para preparar y publicar un Pull Request a partir de los cambios de una feature. Usar con el comando /create-pr o al publicar cambios de la rama de una feature.
---

# Publicación de Pull Requests

Reglas y comprobaciones para preparar y publicar un Pull Request de una feature.

## Reglas generales

- El Pull Request se crea contra la rama base `main`.
- El Pull Request se relaciona con el issue mediante `Closes #<issue-number>` en su cuerpo.
- No se fusiona manualmente el Pull Request ni se cierra manualmente el issue.
- Las operaciones externas (push, creación del Pull Request, modificación de labels) requieren confirmación explícita del usuario.
- La publicación del Pull Request es deliberadamente temprana: la rama puede recibir nuevos commits después de su creación.

## Comprobaciones previas

- Identificar los archivos modificados por la tarea.
- Evitar incluir cambios ajenos a la tarea.
- Revisar el estado, diff e historial de Git (`git status`, `git diff`, `git log`).
- Verificar los cambios antes de publicarlos (por ejemplo, ejecutar la suite de tests).
- No trabajar directamente sobre `main`.

## Publicación de cambios

- Utilizar rutas explícitas al hacer `git add`; no usar `git add .`.
- Hacer `commit` y `push` de la rama de la feature.
- Si la rama ya tiene commits publicados, el nuevo `push` los actualiza sin necesidad de una acción adicional.

## Creación del Pull Request

- Si ya existe un Pull Request para la rama, no crear otro: informar de la URL del existente.
- Si no existe, crear el Pull Request contra `main` con:
  - un título descriptivo de la feature;
  - el cuerpo con `Closes #<issue-number>`.
- Informar de la URL del Pull Request.