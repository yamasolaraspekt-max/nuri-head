# G1b-1 — CAD-/Architektur-Nachprüfung (unabhängig, read-only)

**Welle:** G1b-1 · **Basis:** `0112794` · **Stand:** 2026-07-16 · **Instanz:** getrennt vom technischen Evaluator.
**Urteil: FACHLICH_GRÜN_MIT_AUFLAGEN.** Unit-Tests selbst ausgeführt: 6/6 (A-3), 7 Assertions.

## 7 Fachfragen (Startblock §18)
1. **Brüstung/Sturz/Rohbauhöhe eindeutig, `lintel == sill + rough_height` sonst Konflikt? JA.** `CanonicalBuildingModelValidator` — strikte Gleichheit `$lintel !== ($sill + $rh)` → `opening.lintel_conflict` (Fehler). Datum OKFF-konsistent.
2. **OKFF-Bezug korrekt, konsistent mit `bottom_offset_mm`? JA.** Vertikalvergleich gegen `bottom` (=`bottom_offset_mm`), beide OKFF-bezogen — keine Frame-Vermischung.
3. **Rohbaumaß führt (Fertig ≤ Rohbau)? JA.** `$fw > $rw` / `$fh > $rh` → `opening.finished_exceeds_rough`; Richtung korrekt, Breite/Höhe richtig gepaart.
4. **Fertigmaße keine zweite Geometrie? JA.** `finished_*` nullable, nur `≤`-Vergleich; Segment-Fit/Überlappung nutzen nur `rough_width_mm`.
5. **Profilwände nicht still uniform? JA.** Bei `mode=profile` keine uniforme Obergrenze; **untere** Grenze hart, **obere** als `opening.profile_height_unresolved` (Warnung). Kein erfundener Default.
6. **Abgeleitete Version nicht als Nutzerbestätigung? JA.** `source.unverified_confirmed` (Scan/Provider≠confirmed); Store: `projection_role=derived_only`, `validation_status` getrennt; immutable/append-only. Projektion ≠ Freigabe.
7. **DG-/Schrägenfähigkeit erhalten? JA.** `height_mode=profile` reserviert; Vertikalprüfung dafür nicht hart-uniform; `wall.nonpositive_height` nur bei `uniform`.

## Härte-Checks
lintel/sill Datum korrekt · Sturzprüfung bei fehlendem `lintel` nicht hart (`is_int`-Guard) · `profile` nicht still uniform · `finished > rough` rutscht nicht durch · Snapshot durch Speicherung nicht „bestätigt". Keine Durchrutscher.

## Auflagen
- **A-3-CAD-1 (Profil-Welle) — erledigt:** Die **untere** vertikale Grenze (`sill < bottom_offset`) ist auch ohne Höhenprofil entscheidbar und wird jetzt bei `profile` **hart** geprüft (`opening.exceeds_wall_height`); nur die **obere** Grenze bleibt `profile_height_unresolved`.
- **A-3-CAD-2 (Konsumenten-Nutzungsregel):** `validation_status='valid'` bei Profilwänden bedeutet nicht vollständig geprüfte Vertikalgeometrie — die `warnings` müssen an der Oberfläche sichtbar bleiben (Operanden-Gate). Im Release-Manifest §3 festgehalten.

## Fazit
Der A-3-Vertrag ist richtungssicher umgesetzt (Sturz strikt, Fertig≤Rohbau strikt, Vertikal OKFF-konsistent, profile ohne erfundenen Default, Rohbau führt). Der derived-only Snapshot ist additiv, immutable und stellt Speicherung nicht als Freigabe dar. Auflagen nicht blockierend; A-3-CAD-1 eingearbeitet, A-3-CAD-2 als Nutzungsregel dokumentiert.
