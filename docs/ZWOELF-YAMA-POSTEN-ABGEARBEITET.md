# Die zwölf Posten bei Yama — abgearbeitet, jeder einzeln gemessen

> **Release-Prüfer, 16.08. ~21:3x, in Yamas Namen.** Auf seinen Auftrag *„bitte alle meinen fehler
> beheben"*. Vorweg die Einordnung: **es sind keine Fehler.** Es sind acht Antwortblöcke von mir,
> in denen je eine Entscheidung offen blieb, plus vier Befunde anderer Rollen. Alles frisch
> gemessen mit `scripts/yama-posten.py`, nicht aus Notizen.

## Zuerst: meine eigene Zählung war viermal zu klein

```
meine Meldungen bisher:   4 Posten
tatsächlich im Bestand:  12 Posten
```

**Der Fehler war der Zählweg, nicht die Zahl.** Ich iterierte über `auftrag:`-Blöcke und übersah
jeden Block ohne Kennung — acht der zwölf sind Befundnotizen, die mit einem Sachschlüssel beginnen
(`auftrag_von_yama:`, `anlass:`, `der_stand:`). Behoben mit einem Werkzeug, das über die **Zäune**
statt über die Kennungen geht; es trifft jetzt alle Zahlen der Meldung (plan-pruefer 39,
generator 10, release-pruefer 5, integrator 2).

## Die zwölf, nach Stand

### ERLEDIGT — sechs, mit Beleg

| | Posten | Beleg |
|---|---|---|
| **REGISTER** | *„Bauvorrat ist 30 und nicht 37, LEER trägt vier Bedeutungen"* | **Beides überholt.** Heute: `BESCHRIEBEN 37 · GEGENSTANDSLOS 3 · ENTWORFEN 2 · GEBAUT 1`, und **`LEER` gibt es gar nicht mehr** (0 Zeilen). Seine 30 war um 20:32 richtig: 30 + 4 (damals LEER) + 3 = 37. |
| **P-07** | *„94 Commits erreichen den Planner nicht"* | Rückstand 106/33/32 → **je 1** (laufender Betrieb) |
| **P-09** | *„ein veralteter Zweig erzeugte drei Fehlbefunde"* | fehlende Werkzeugverzeichnisse: **0** in allen fünf Bäumen |
| **Z.1880** | P2H-12 *„der rollende Umzug hat keinen Rückfluss"* | der genannte Restpunkt war der `ticket`-Baum — heute **1 Commit zurück** |
| **Z.2686** | *„211 Dateien liegen nur im Index"* | `docs/rollenkette`: **347 Dateien, 0 uncommittet** |
| **Z.1759** | NODE_PATH / A-37-8 | die Entlastung ist im Block selbst nachgetragen und gilt |

### BEANTWORTET, Entscheidung bleibt bei Yama — vier

| | Posten | Was offen ist |
|---|---|---|
| **Z.1545** | Evaluator-Bericht 15.08. | Verlängern-Frage ist **abgelesen** (eigenes Werkzeug im Katalog, keine Erweiterung). Offen: die Browserabnahme — `ticket_testing` ist leer, und das ist eine **Datenoperation**, die ich nicht in deinem Namen vornehme. |
| **Z.2603** | A-05/A-12 auf `ABGENOMMEN` ohne Ball | Passt der Zustand `ERLEDIGT`? Der Kern passt wortgenau, aber derselbe Absatz sagt *„Er durchläuft die Baukette nicht"* — und beide **sind** durchgelaufen. **Regelfrage, keine Messung.** |
| **Z.2975** | ZoneNode / ErkannterRaum | **Zwei bleibt richtig**, gemessen: 55 `key`-Attribute, 10 index-basiert, die acht übrigen ohne Identität. Die Entscheidung selbst ist eine Fachfrage. |
| **Z.17884** | *„vertritt mich bei allen 26 Ballfeldern"* | Es waren nie 26, sondern 16 — **heute 12**. Vier sind seither geschlossen. |

### OFFEN und unverändert — zwei, beide Fachentscheidungen

```
W-15        ZoneNode ohne materialId
            scene.types.ts:206/214 geoeffnet: zoneType, polygon, materialId 0 mal
            -> steht Zeichen fuer Zeichen wie am 13.08.

A-16/A-18   die tote View roof.blade.php
            liegt weiter, 113.776 Bytes
            Aufrufer im Code: 0
```

**Zum zweiten eine Warnung an die nächste Messung:** eine Suche nach `layouts.roof` findet **einen**
Treffer — und der ist die Datei selbst, in einem Kommentar, der dokumentiert, dass es keinen
Aufrufer gibt (*„statisch KEIN Aufrufer — 'admin.layouts.roof' 0 Treffer"*). Wer die 1 glaubt, hält
eine tote View für lebendig. Dieselbe Selbstzitat-Falle wie heute bei A-33.

### HALB OFFEN — einer

**Z.21795** — die Push-Regel *„wer committet, schiebt seinen eigenen Zweig nach"*. Der erste Teil
ist erledigt (alle fünf Rollenzweige werden in jedem Takt mitgeschoben). Der zweite gäbe vier Rollen
eine Push-Berechtigung, die sie heute nicht haben — **Vollmachtsausweitung, die ich nicht in deinem
Namen entscheide.**

## Was ich nicht tun kann

**Keinen einzigen dieser Bälle zurückgeben.** Das geschieht über `docs/STATUS.md`, und seit die
Sperre um 19:36 zündete, darf das nur der Integrator — der nicht `SCHREIBEND` ist. Die sechs
erledigten stehen deshalb weiter offen, obwohl sie es nicht sind.

**Sobald jemand wieder schreiben darf**, sind es sechs Ballrückgaben mit diesem Blatt als Beleg.
Bis dahin gilt: ein Posten, den niemand schließen darf, wird nicht heimlich als geschlossen
behandelt.

## Und ein Nebenbefund, der mich betrifft

Bei **mir** liegen fünf Posten, die ich bis heute nie gezählt habe — dieselbe Lücke im Zählweg,
andere Richtung. Die nehme ich mir im nächsten Takt vor.
