# Aufgabe für den Evaluator (VS Code, nativ) — 2026-07-23

Zwei Schritte, beide nativ (Netz + git-Zugang + Browser). Nach jedem Schritt kurzer Beleg.

## Schritt 1 — Push (Konsolidierung sichern), dann selbst gegenprüfen
Ziel-Tip: **`cff1fe5`** (rect-Fix, volle FREIGABE — schließt e9334bb als Fast-Forward ein). `4b8eb04`/Dach-UI
NICHT pushen (noch in Abnahme).
```
# Locks vorsorglich beiseite (Geräte-VM-Reste), dann nativ:
rm -f .git/index.lock .git/HEAD.lock .git/_locks_aside/* 2>/dev/null
git fetch --all --prune
git rev-list --count b7f83f0..backup-private/strang/accounting   # erwartet 1
git rev-list --count b7f83f0..backup-private/strang/formulare    # erwartet 1
git push fork cff1fe5:refs/heads/auto/hausplaner-w3b
git push fork auto/hausplaner-w3a          # 0c33755
git push fork refs/remotes/backup-private/strang/accounting:refs/heads/strang/accounting
git push fork refs/remotes/backup-private/strang/formulare:refs/heads/strang/formulare
git ls-remote fork | grep -E "auto/hausplaner-w3b|auto/hausplaner-w3a|strang/"
```
**Gegenprüfung (selbst):** fork trägt `auto/hausplaner-w3b`==`cff1fe5`, `auto/hausplaner-w3a`==`0c33755`,
`strang/accounting`==`28b074b`, `strang/formulare`==`f796b42`; alte Refs (`main`==`b477ad5`,
`ui-3a`==`f3e38d6`) unverändert; kein `--force`, kein Push nach `origin`/`main`.

## Schritt 2 — Dach-UI-Slice abnehmen + U-Optik (endlich erreichbar)
```
git checkout auto/hausplaner-dach-ui        # Tip 1d8c735
npm run build:hausplaner                     # nativ, Exit 0
```
Dann `/admin/hausplaner/studio` (Expertenmodus) hart neu laden und prüfen:
- Dachform-`<select>` bietet jetzt **alle 8 Formen** (sattel/walm/pult/flach/**rect/l-shape/t-shape/u-shape**).
- `u-shape` wählen + Anbau-Felder (length/width) setzen → **U-Dach rendert sichtbar** → die aufgeschobene
  **U-Optik** prüfen (Lage/Orientierung; Generator-Flag „Schwerpunkt-Näherung, evtl. versetzt") in **3 Viewports**.
- `rect` = flache Fläche (rect-Fix); `l/t` wählbar (Flächen leer bis Teil 3, dokumentiert).
- Token-Disziplin: kein roher Hex in den geänderten Zeilen.
**Votum** je Slice; bei rot → zurück an Generator, bei grün → an Planner (dann Freigabe für L/T-Faces Teil 3 /
Batch 1). Auftrag: `generator-auftrag-dach-ui-formen-anbau.md`.


> **ÜBERHOLT (2026-07-23):** Falsch geroutet (Push/checkout/build an Evaluator). Gültig ist `routing-dachui-und-push.md`.
