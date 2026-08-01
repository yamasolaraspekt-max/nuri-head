# Der Planner arbeitet das Prüfer-Register ab — 01.08.2026, 11:0x

**Anlass:** Yama, 01.08.: *„bevor wir Schritt 8 vornehmen, bitte erst mal das hier erledigen."*
Der Merge steht seit Schritt 7 still; das Rückweg-Etikett `vor-merge-2026-08-01-1045` ist gesetzt,
sonst ist nichts angefasst. **Anhalten ist an dieser Stelle gefahrlos.**

## Zuerst: die Zahl stimmt nicht ganz — es sind 20, nicht 21

```text
grep -cE '^\| PB-[0-9]+' docs/planner/PRUEFER-BEFUNDE.md   ->  48 Registerzeilen
davon mit "offen" in der Votum-Spalte                      ->  21
   ab PB-042  Ballbesitz Yama (Takt)                        -1
   ab PB-048  ausdruecklich "Hinweis (kein Mangel)"         -1
   ab PB-043  steht als offen, ist aber ERLEDIGT: Teil 2
              gebaut 9e294323, abgenommen 085f285c          -1
                                                          ----
wirklich beim Planner                                        18
   + AUF-86 (56 px unerreichbare 3D-Flaeche)                +1
   + fehlende .env.testing.example                          +1
                                                          ----
SUMME                                                        20
```

**PB-043 als „offen" zu führen ist genau die Klasse, die der Prüfer selbst anmahnt** — das Register
hinkt der Wirklichkeit hinterher. Ich zähle es als erledigt, weil zwei unabhängige Messungen es
belegen; die Zeile im Register korrigiert der Prüfer, nicht ich (sie gehört ihm).

## Erledigt in dieser Runde — fünf Posten, jeder mit Messung

| Befund | Was falsch war | Was jetzt dort steht |
|---|---|---|
| **PB-020** | `AUFTRAGSSCHEMA.md` nannte `./scripts/zaehle-statische-stile.sh` — **die Datei gibt es nicht** | `node scripts/statische-inline-stile.mjs …`, **gefahren**: „5 Stellen insgesamt, davon 0 offen." Beide Vorkommen ersetzt |
| **PB-025** | *„schützt `partials/sa-ui.blade.php` — die Datei gibt es nicht"* | **Der Befund ist falsch. Die Datei existiert**: `resources/views/admin/layouts/partials/sa-ui.blade.php`, 855 Byte, 24.07. Richtig war der abgekürzte Pfad-Vorwurf — er steht jetzt vollständig da |
| **PB-026** | `kern.md` Z1 „NACHRANGIG", Z11 „werden IMMER geladen" | Ein Satz: gelesen ja, vorrangig nein — mit Verweis auf den Befund |
| **PB-034** | `bauordnung.md`: „Nur 96/387 Controller klammern" | **Nachgemessen 01.08.: 75 von 406.** Die Zahl des Prüfers bestätigt. Der Beleg nennt jetzt beide Befehle und sagt, dass die Lage sich **verschlechtert** hat |
| **PB-035** | `fahrplan-dashboard-versionen.md`: UI-9 = „❌ 0 Dateien" | **`app/dashboard/palette.ts`, 191 Zeilen, 2 Testdateien, 3 Konsumenten.** Zeile auf „gebaut" mit den Zahlen |

**Zwei Vorkommen des toten Skriptnamens bleiben stehen** — in `handoff-status.md` (Historie) und im
Register des Prüfers. *Historie wird nicht korrigiert, und fremde Dateien fasse ich nicht an.*

## Was NICHT beim Planner liegt — Mehr-App-Regel

| Befund | Ort | Warum nicht ich |
|---|---|---|
| **PB-029** | `~/wissensregister/register.md` | Andere App. Lesen frei, schreiben nur in der Heimat-App |
| **PB-030** | `~/wissensregister/kategorien/` | dito |
| **PB-036** | Skill-Ordner `ticket-code-reuse/` | dito — gehört dem Skill-Besitzer |

**Diese drei gehen an Yama zurück**, nicht weil sie unwichtig sind, sondern weil ein Quer-Hineinschreiben
genau das ist, was die Mehr-App-Regel verhindert.

## Was ein Blatt braucht, weil es Code ist — nicht Papier

| Befund | Kern | Wohin |
|---|---|---|
| **PB-019** | Validator **benennt** `KEIN KOPF`, gibt aber `exit 0` — sechs aktive Blätter kämen durch | Generator, kleines Blatt |
| **PB-023** | 175 `hp-`-Klassen, **0 im Styleguide** — keine Regressionsfläche | Generator, echtes Blatt |
| **PB-024** | Insel-Palette: 42 Hexwerte, 0 Verweise auf `--sa-` | Generator, hängt an PB-023 |
| **AUF-86** | 56 px unerreichbare 3D-Fläche | Blatt liegt, Bau offen |
| **.env.testing.example** | fehlt — die DB-Barriere wirkt nur auf diesem Rechner | Generator, eine Datei |

## Rest — Papier, in meiner Schreib-Heimat, noch offen

`PB-011` · `PB-012` (FEHLERKLASSEN.md) · `PB-021` (CLAUDE.md) · `PB-022` (arbeitskompass) ·
`PB-027` · `PB-028` (Blatt-Status gegen Tafel) · `PB-031` (68 von 923 Code-Pfaden) —
**sieben Posten.** Sie kosten Aufmerksamkeit, aber nichts läuft deshalb falsch.

**Und ein Wort dazu, warum ich sie nicht alle in einem Rutsch erledige:** PB-042 sagt
*„109 Commits heute, 2 davon Produktivcode, docs/Code 7:1"*. **Eine Aufräumrunde, die zwanzig
Papierposten in zwanzig Commits abarbeitet, macht genau dieses Verhältnis schlimmer.** Ich nehme
sie mit, wenn ich die Datei ohnehin anfasse — und schneide dazwischen Blätter, damit der Generator
nicht steht.
