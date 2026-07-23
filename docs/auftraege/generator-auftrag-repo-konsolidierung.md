# Generator-Auftrag — Repo-Konsolidierung (nurihead + nuri-head aus der Welt)

**Rolle:** Generator (Claude Code in VS Code, native git + Netz). **Heimat-App:** `ticket`.
**Ausgestellt von:** Planner (Cowork), 2026-07-23. **Nur auf Yamas Wort ausführen** (Remote-Pushes).

## Ziel
**`nuri-head` (yamasolaraspekt-max/nuri-head, remote `fork`) wird das eine kanonische Repo.** Es bekommt
ALLES: den vollständigen lokalen Stand **und** die zwei einzigartigen Stränge aus `nurihead`. Danach ist
`nurihead` (yamasolaraspekt-max/nurihead, remote `backup-private`) ein eingefrorenes Archiv.

## Befund (Planner, read-only gemessen)
- **Vollständigste Linie: `auto/hausplaner-w3b` @ `e9334bb`** (W-3b Stufe 1 auf gefixtem W-3a) — enthält
  UI-2/W-1/W-2/UI-3a/UI-3b/W-3a/**W-3a-fix `0c33755`**/**W-3b Stufe 1**. Lineare Historie, kein Konflikt.
- `nuri-head` (fork): **nichts Einzigartiges** (sein `auto/hausplaner-ui-3a` = alte, ersetzte UI-3a-Version).
- `nurihead` (backup-private): **einzig** `strang/accounting` (`28b074b`) + `strang/formulare` (`f796b42`).
- `origin` (raminsadid2021): leer (Initial-Commit). **Nicht Ziel dieses Auftrags** (Trunk-Frage separat).

## VORBEDINGUNG
**Nicht ausführen, bevor der Evaluator W-3b Stufe 1 (`e9334bb`) GRÜN gibt** und der Live-Gate am Tip
belegt ist. Erst den verifizierten Tip pushen — keine ungeprüften Stände ins Backup.

## Schritt 0 — Bundle-Gegenprüfung (nativ, VOR dem Push)  [Evaluator-Auflage, environmental]
Das kanonische Artefakt darf kein driftendes Bundle enthalten. Auf der ARM-Geräte-VM ist der Build nicht
fahrbar (`@rollup/rollup-linux-arm64-gnu`-Bug); **auf dem Mac nativ schon**:
- `npm run build:hausplaner` → muss **Exit 0** sein (nativ).
- `git status --porcelain public/hausplaner/hausplaner.js` prüfen:
  - **unverändert** ⇒ das committete Bundle ist aktuell (keine Drift) → weiter mit Schritt 1.
  - **verändert** ⇒ das committete Bundle war veraltet → den **frischen Bundle als eigenen Commit** oben
    drauf (`git add public/hausplaner/hausplaner.js && git commit -m "W-3b: Bundle-Rebuild (nativ)"`); der
    **neue Tip** ist dann das, was gepusht wird (Schritt 3 entsprechend anpassen).
Damit ist auch das 4. Gate (build) geschlossen und der gepushte Stand trägt ein deckungsgleiches Bundle.

## Schritte
1. **Aktuellen Stand holen** (bestätigt, dass sich auf den Remotes seit letztem Fetch nichts geändert hat):
   `git fetch --all --prune`
2. **Gegenprüfen**, dass die zwei Stränge weiterhin nur auf `nurihead` einzigartig sind:
   `git rev-list --count b7f83f0..backup-private/strang/accounting` → erwartet 1
   `git rev-list --count b7f83f0..backup-private/strang/formulare` → erwartet 1
   (Andere Zahl ⇒ STOPP, an Planner zurück — es hat sich etwas geändert.)
3. **Vollständige Linie nach nuri-head** (Sicherung + kanonisch):
   `git push fork auto/hausplaner-w3b`  (verifizierter Tip `e9334bb`; enthält die ganze Linie inkl. W-3a-fix + W-3b Stufe 1)
   Zusätzlich den Meilenstein-Branch mitsichern (optional, benannter Backup):
   `git push fork auto/hausplaner-w3a`  (`0c33755`, W-3a-fix)
4. **Die zwei Stränge nach nuri-head spiegeln** (direkt von den remote-tracking-Refs):
   `git push fork refs/remotes/backup-private/strang/accounting:refs/heads/strang/accounting`
   `git push fork refs/remotes/backup-private/strang/formulare:refs/heads/strang/formulare`
5. **Verifizieren**, dass nuri-head jetzt alles hat:
   `git ls-remote fork | grep -E "auto/hausplaner-w3a|strang/accounting|strang/formulare"`
   → alle drei vorhanden, Tips == `b7f83f0` / `28b074b` / `f796b42`.

## Verboten (harte Guardrails)
- **KEIN** `--force` / `--force-with-lease` (nichts überschreiben; die alte `fork/auto/hausplaner-ui-3a`
  bleibt unangetastet stehen).
- **KEIN** Push nach `origin` (raminsadid2021) — die Trunk-Frage ist separat, hängt an „wer ist raminsadid2021".
- **KEIN** Merge, **KEIN** Push nach `main`.
- **`nurihead` (backup-private) NICHT löschen** — bleibt vorerst als Archiv. Löschen ist eine spätere,
  eigene Entscheidung, erst NACH bestätigter Spiegelung.

## Abnahme (Evaluator in VS Code)
1. `git ls-remote fork` zeigt `auto/hausplaner-w3b`==`e9334bb` (+ optional `auto/hausplaner-w3a`==`0c33755`),
   `strang/accounting`==`28b074b`, `strang/formulare`==`f796b42`.
2. Nichts auf `fork` überschrieben (alte Refs unverändert), `fork/main` unverändert.
3. `origin` unverändert (kein Push zu raminsadid2021).
4. `nurihead` (backup-private) unverändert (nur gelesen).
→ Ergebnis: **`nuri-head` = die eine Wahrheit** (alles drin); `nurihead` = reines Archiv, gefahrlos einfrierbar.

## Danach offen (Planner/Yama)
Trunk-Frage: welches `main` der echte Stamm wird — hängt an **„wer ist raminsadid2021"**. Erst wenn das
geklärt ist, folgt der Merge-nach-main-/Integration-Auftrag.

---

## UPDATE 2026-07-23 — Pre-Push read-only verifiziert (Planner) + fertiger Befehlsblock
**Verifiziert (read-only am Gerät):** lokale Branches korrekt — `auto/hausplaner-w3b`==`e9334bb`,
`auto/hausplaner-w3a`==`0c33755`; strang-Commits `28b074b`/`f796b42` lokal greifbar; Remotes gesetzt
(fork/backup-private/origin); **kein aktives .git-Lock** (nur harmlose `aside-…-HEAD.lock`-Restdatei).
**Wichtig:** die **Geräte-VM hat kein Netz** → Push läuft **nur nativ am Mac** (Terminal/VS Code mit Netz).

**Ziel-Tip aktualisiert:** statt `e9334bb` den vollständig freigegebenen **`cff1fe5` (rect-Fix, FREIGABE,
632/632)** sichern — schließt `e9334bb` als Fast-Forward ein. `4b8eb04` (L/T/U) NICHT pushen (U-Sicht +
Live-638 offen).

### Befehlsblock (nativ am Mac, im ticket-Repo, mit Netz)
```
# 0) Restlock (falls vorhanden) wegräumen — schadet nie:
rm -f .git/aside-*HEAD.lock .git/HEAD.lock .git/index.lock 2>/dev/null

# 1) Stand holen (bestätigt: seit letztem Fetch nichts Neues auf den Remotes):
git fetch --all --prune

# 2) Gegenprüfen, dass die zwei Stränge weiterhin nur auf nurihead (backup-private) einzigartig sind:
git rev-list --count b7f83f0..backup-private/strang/accounting   # erwartet 1
git rev-list --count b7f83f0..backup-private/strang/formulare    # erwartet 1
#   (andere Zahl ⇒ STOPP, an Planner)

# 3) Vollständige, freigegebene Linie nach nuri-head (cff1fe5 als kanonischer w3b-Tip):
git push fork cff1fe5:refs/heads/auto/hausplaner-w3b
git push fork auto/hausplaner-w3a          # 0c33755 (W-3a-fix, benannter Backup)

# 4) Die zwei Stränge spiegeln (direkt von den remote-tracking-Refs):
git push fork refs/remotes/backup-private/strang/accounting:refs/heads/strang/accounting
git push fork refs/remotes/backup-private/strang/formulare:refs/heads/strang/formulare

# 5) Verifizieren:
git ls-remote fork | grep -E "auto/hausplaner-w3b|auto/hausplaner-w3a|strang/accounting|strang/formulare"
```

### Evaluator-Gegenprüfung — aktualisierte Soll-Tips
`fork` trägt danach: `auto/hausplaner-w3b`==**`cff1fe5`** · `auto/hausplaner-w3a`==`0c33755` ·
`strang/accounting`==`28b074b` · `strang/formulare`==`f796b42`; alte Refs (`main`==`b477ad5`,
`auto/hausplaner-ui-3a`==`f3e38d6`) **unverändert**; `origin`/backup-private unberührt.
