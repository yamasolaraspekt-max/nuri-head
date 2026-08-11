# BEFUND — vier Dachwelten, keine Brücke

> **KEIN AUFTRAG — VORLAGE.** Yama, 10.08.2026: *„das muss alles erst mal fachlich geprüft
> validiert werden — das ist die Aufgabe von Planner und Plan-Prüfer."*
> **Ball: Planner, danach Plan-Prüfer.** Der Generator hat hier **gemessen, nicht bewertet**:
> Existenz, Umfang, Gleichheit, Fundstelle. **Ob ein Stück fachlich taugt, steht in keinem dieser
> Sätze** — auch nicht in den Reihenfolge-Vorschlägen, die Vorschläge bleiben.
> **Es wird nichts kopiert, bevor Planner und Plan-Prüfer geprüft haben.**

**Gemessen:** 10.08.2026 · **Rolle:** Generator · **Anlass:** Yamas Frage, ob die Dachplanung
seinen Playground-Bestand und den ticket-Bestand bedient — gerade bei Azimut und Dachformen.
**Ball:** Planner (Klasse SPEC). Nichts davon ist gebaut oder geändert, nur gemessen.

## 1 · Azimut existiert vierfach

| # | Ort | Konvention | dokumentiert? |
|---|---|---|---|
| 1 | `resources/planner/hausplaner/geometry/wallGeometry.ts:37` | Nord = +y, im Uhrzeigersinn, 0–359, **Normale** | **ja** — Spec ▲K2 im Dateikopf |
| 2 | `app/Services/Geometrie/SzeneProjektionService.php:257` | Nord = +y = 0°, Ost = 90° | **ja** — im Klassenkopf |
| 3 | `app/Services/Energie/PvgisErtragService.php:41` | **0 = Süd**, −90 = Ost, 90 = West | **ja** — PVGIS-Fremdkonvention |
| 4 | `app/Models/PVRoof.php:24` `roof_azimuth` (decimal:2, seit 2024) | 0=N, 90=E, 180=S, 270=W | **JA** — `database/migrations/2024_06_04_103808:67` |

1 und 2 sind konsistent. 3 ist eine Fremd-API und darf abweichen.

**BERICHTIGT am 12.08.2026 — Punkt 4 war falsch.** Der Planner hat gemessen (`3d368625`), ich habe
es nachgeprüft: **die Konvention steht da**, als Kommentar neben der Spalte —
`database/migrations/2024_06_04_103808:67` → `// 0=N, 90=E, 180=S, 270=W`.

**Warum ich sie verfehlt habe:** meine Prüfung lief mit `--include='*.php' app/`. Die Angabe liegt in
`database/`. *Ich habe am falschen Ort gesucht und aus dem leeren Ergebnis auf Abwesenheit geschlossen —
dieselbe Klasse wie „im falschen Schema gemessen".* **Ein `grep`, der den richtigen Ort nicht
einschließt, beweist nichts.**

**Was stattdessen gilt** (Messung des Planners, von mir nicht nachgemessen): fünf Stellen dokumentieren
0=Nord/0…360 und **drei Tests sichern sie zu**. Die PVGIS-Konvention ist an allen vier Stellen korrekt
als solche benannt. Der wirkliche Mangel ist kleiner und schärfer als meiner: **die Umrechnung an der
Grenze fehlt** — und sie fällt nicht auf, weil **0…180 in beiden Systemen gültig ist und das Gegenteil
bedeutet**. Ein Süddach trägt im Kompass 180; unverändert an PVGIS gegeben, rechnet PVGIS ein **Norddach**.
*Der größtmögliche Fehler, und nichts schlägt an.*

## 2 · Es gibt keine Brücke vom Planer in die PV-Rechnung

| gemessen | Ergebnis |
|---|---|
| `firstAzimutGrad` in `app/` + `database/` | **0 Treffer** |
| `PVRoof` in `app/Domain/Hausplaner/` | **0 Treffer** |
| Woher PVGIS sein `aspect` bekommt | `$request->get('aspect')` — **Nutzereingabe** |

**Der im Planer gezeichnete Dachazimut erreicht die Ertragsrechnung nicht.** Er wird von Hand
noch einmal eingetippt. Kein falscher Umrechnungsfehler — sondern gar keine Verbindung.

## 3 · Der Playground-Bestand ist NICHT übernommen

Der Playground hat ein eigenes, ausgebautes Dachmodell:

| Baustein im Playground | in ticket |
|---|---|
| `energie_roof_models` (Migration, mit `dachform` + `standard_dachneigung_grad`) | **0** |
| `EnergieRoofModel` | **0** |
| `RoofModelController` | **0** |
| `PlanungskontextController` | **0** |

**Die Spalte `dachform` existiert im Playground und nirgends in ticket.** Deshalb hängt die
Dachplanung: *A-05 hat gemessen, dass `roof.anbau` vier Maße fehlen und kein Code eine Dachform aus
einer Kontur ableitet — das Stück, das genau das könnte, liegt im Playground.*

## 4 · Yamas eigener Dachbestand ist erfasst, aber nicht eingearbeitet

`docs/rollenkette/werkbank/05-MATERIALQUELLEN/BESTAND-YAMA.md` (07.08.2026) führt ihn:

- **M-01 `dachdecker_pro_3d.tsx`** — dort als „der wertvollste Fund" bezeichnet
- `dachdecker_pro.tsx` — Vorgänger, **evtl. andere Dachformen**
- Pfettendach-Fachbilder, `Dach3D-1.html`, `dachplan.html`, `Frank.glb`
- Dachziegel-DB-Schema (MySQL 8), STL Frankfurter Pfanne
- sechs Screenshots „DACHDECKER PRO" als **Bedienreferenz**

Das Register sagt zu diesen Quellen wörtlich: **„noch nicht in die Werkzeugordner eingearbeitet"**.

## 5 · Was daraus folgt — zu entscheiden, nicht von mir

1. **Welche Azimut-Konvention gilt für `roof_azimuth`?** Bevor irgendwer sie liest oder schreibt.
   Fehlender Operand → keine stille Annahme (CLAUDE.md).
2. **Wird das Playground-Dachmodell nach ticket übernommen oder neu gebaut?** Die Schutzgrenze sagt:
   vorhandenes zuerst prüfen und wiederverwenden. Geprüft ist es jetzt — es ist da und es ist nicht drin.
3. **Wird M-01 die Quelle der Dachformen?** Dann ist W-07 kein Neubau, sondern ein Anschluss.

## Was ich NICHT gemessen habe

- Ob das Playground-Dachmodell fachlich taugt — ich habe seine **Existenz** gemessen, nicht seine Güte.
- Den Inhalt von `dachdecker_pro_3d.tsx` — nur den Eintrag in BESTAND-YAMA.md gelesen.
- Ob `roof_azimuth` in Produktivdaten befüllt ist. **Nicht gegen Produktivdaten gemessen.**
