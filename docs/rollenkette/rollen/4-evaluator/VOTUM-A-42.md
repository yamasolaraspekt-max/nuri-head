# VOTUM A-42 — „Befundnotizen ziehen um"

**evaluator · 21.08. · Bau `26c46f31` · Prüfstand-HEAD `1cd33614` · Lease `fencing_token: 1`**

## Ergebnis: NACHBESSERN — EIN Punkt. Der Umzug selbst ist makellos.

Zehn von elf Prüfpunkten tragen, sieben davon zeichengenau. Der eine offene Punkt betrifft **nicht**
den Umzug, sondern eine fehlende Bewertung.

## Vorab: die Bau-Identität selbst geprüft, nicht übernommen

| geprüft | Ergebnis |
|---|---|
| `26c46f31` ist der A-42-Bau | **ja** — `BEFUNDNOTIZEN.md +9470` · `STATUS.md +31/−9210` · `a42-befundnotizen-umzug.py +407` |
| `0f731c22` ist der A-38-Bau | **ja** — zwei Hook-Dateien (`.githooks/commit-msg`, `post-commit`) |
| `e9e6ee5b` ist leer | **ja** — **0** geänderte Dateien |

**Eine Präzisierung zur Warnung meines Auftrags:** Sie sagt, `e9e6ee5b` nenne „als Bau `0f731c22`".
Der Betreff nennt tatsächlich **beide** SHAs (`bau 0f731c22 26c46f31`). Der Fehler ist also nicht
ein falscher SHA, sondern **zwei Kennungen und zwei Bauten in einer Meldung** — genau das, was das
Statusmuster nicht erkennt. Das entlastet den Generator in einem Punkt und ändert nichts am Rest.

## Messtisch — alle elf Punkte

| # | Prüfpunkt | Ergebnis |
|---|---|---|
| 1 | `461 = 289 + 172` | **erfüllt, exakt** — ```yaml-Zäune: vorher **461**, nachher **289** + **172** = **461** |
| 2 | alle 172 Blöcke byte-identisch | **erfüllt** — md5 je Block: **0** fehlend, **0** neu; **172 von 172** stammen byte-identisch aus vorher |
| 3 | keine Inhaltszeile verloren/hinzugefügt | **erfüllt** — **0 verloren**; 284 neue = Kopf der neuen Datei (Titel, Erklärung, Beispiel) |
| 4 | gerade Zaunbilanz in beiden Dateien | **erfüllt** — vorher 1194, nachher 850 und 346: **alle drei gerade** |
| 5 | 104 echte Auftragsdatensätze unverändert | **erfüllt** — eigenes Werkzeug an **beiden** Ständen: 104 Datensätze, 104 Tafelzeilen |
| 6 | Ballbesitz je Rolle vorher = nachher | **erfüllt** — identisch, keine Zustand- und keine Ball-Drift; `evaluator 6` deckt sich mit meinen sechs Bällen |
| 7 | zweiter Lauf idempotent | **erfüllt** — Lauf auf dem umgezogenen Stand: **beide md5 unverändert** (`ab18d347…`, `fa0a8b18…`) |
| 8 | die zwei zurückgebliebenen Überschriften bewusst bewertet | **NICHT erfüllt** — es sind **drei**, und bewertet ist keine |
| 9 | Wacheanweisungen durchsuchen beide Dateien | **teilweise** — `yama-posten.py` ja (5/4); `a26`, `a30`, `a33`, `status-erzeugen` kennen `BEFUNDNOTIZEN` **0×** |
| 10 | Zieldateischutz | **Befund bestätigt** — `BEFUNDNOTIZEN` in `rollen-tor.sh` **0×**, in `commit-pruefen.sh` **0×** |
| 11 | Votum mit Befehl, Rohausgabe, SHA | dieses Blatt |

## Der eine Punkt: A-42-1

**Drei** Überschriften haben ihren yaml-Block verloren und stehen nun ohne Inhalt in `docs/STATUS.md`:

```
## A-17 — MESSBERICHT des Planners (KEIN Zustandsdatensatz — der steht oben, Z. 2138)
## BEFUND — DIE EINE STATUSWAHRHEIT HAT GERADE ZWEI FASSUNGEN (Plan-Prüfer, 14.08. 08:23)
## BERICHTIGUNG DER CODE-ZEIGER AUS DER STATUSWAHRHEIT — Integrator, 21.08.2026
```

Gemessen als: Überschrift, der **vorher** ein ```yaml-Block folgte und **nachher** keiner mehr.
Weder das Blatt noch der Baubericht bewerten sie; der Prüfumfang erwartet die Bewertung von **zwei**.
**Zu tun:** die drei benennen und je entscheiden — mitziehen, mit Verweis versehen oder entfernen.
*Eine Überschrift ohne ihren Block ist keine Kleinigkeit: Sie verspricht Inhalt, den die Datei nicht
mehr hat.*

## Zwei Befunde ohne Abzug

**(a) Die neue Datei ist nicht geschützt.** `docs/STATUS.md` wird vom Rollen-Tor gesperrt — ich habe
das selbst erlebt (A-37-6 wies meinen Claim-Commit ab). `docs/BEFUNDNOTIZEN.md` kommt in
`rollen-tor.sh` und `commit-pruefen.sh` **0×** vor: **jede Rolle kann sie aus jedem Baum schreiben.**
Der Auftrag nennt das ausdrücklich als Befund ohne Abzug — er gehört in die A-37-Erweiterung.

**(b) Die Wächter sehen die neue Datei nicht.** `a26-ball-drift`, `a30-datensatz-paar`,
`a33-kennungen-nachziehen` und `status-erzeugen` nennen `BEFUNDNOTIZEN` **0×**. **Heute ohne
Wirkung**, denn alle drei arbeiten auf Auftragsdatensätzen und Tafelzeilen — und die sind
vollständig in `STATUS.md` geblieben (104 = 104, Punkt 5). *Die Lücke wirkt erst, wenn dort je ein
Feld mit `zustand` oder `ballbesitz` landet.* Als Vorratsposten notiert, nicht als Mangel gewertet.

## Was besonders sauber ist

Die **Idempotenz** (Punkt 7) ist der stärkste Beleg: Ein Umzugsskript, das beim zweiten Lauf nichts
mehr ändert, kann nicht doppelt umziehen — und der Baubericht nennt selbst zwei Fehlschläge, die
genau daran scheiterten. Dass **172 von 172** Blöcken byte-identisch ankommen und **0**
Inhaltszeilen verloren gingen, macht den Umzug nachprüfbar verlustfrei.

## Weitergabe

**NACHBESSERN → Generator** (A-42-1: drei zurückgebliebene Überschriften bewerten).
Befunde (a) und (b) → **Planner**. Zustand setze ich nicht — `docs/STATUS.md` ist mir gesperrt;
der Nachtrag gehört dem **Integrator**.
