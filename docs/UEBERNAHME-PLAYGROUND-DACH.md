# ÜBERNAHME Playground → ticket, Dachplanung

**Auftrag von Yama, 10.08.2026:** alles aus dem Playground-PV-Dachplaner, was für das 3D-Haus und
den Dachplan sinnvoll ist, wird übernommen und hier eingefügt. **Das „ob" ist entschieden.**
**Rolle:** Generator · **Zustand:** gemessen, **nichts kopiert** · **Ball:** Planner (Auftrag schneiden) + eine Frage an Yama.

## 0 · Was ich zuerst falsch vermutet hatte

Ich bin von „der Playground hat das Dach, ticket nicht" ausgegangen. **Gemessen stimmt das nicht.**

| Baustein | Playground | ticket | |
|---|---|---|---|
| `dachformVorlagen.ts` | 2399 Z | **2399 Z** | **identisch** |
| `schifterListe.ts` | 152 Z | **152 Z** | **identisch** |
| `dachUForm.ts` | 126 Z | **126 Z** | **identisch** |
| `dachVerschneidung.ts` | 135 Z | **205 Z** | ticket ist **weiter** |
| `src/hausplaner/**` | 30 Dateien | **325 Dateien** | ticket ist in **allen 15** abweichenden Dateien größer, **0 verlorene Ausfuhren** |

**Die Geometrie ist längst hier.** Wer sie nochmal kopiert, baut eine zweite Wahrheit.
*Auch ticket kennt zwölf Dachformen: flachdach, krueppelwalm, l-shape, mansard, mansarddach,
mansardwalm, pultdach, satteldach, schleppdach, u-shape, walmdach, zeltdach.*

## 1 · Was WIRKLICH fehlt — sieben Dateien plus ein Schema

| # | Datei im Playground | Z | Was sie leistet | Konflikt? |
|---|---|---|---|---|
| Ü-1 | `src/stores/roofTypes.ts` | 111 | Typen des Dach-Zustands | nein |
| Ü-2 | `src/stores/roofVocab.ts` | 101 | Vokabular (Deckung, Hindernisarten) | nein |
| Ü-3 | `src/stores/roofConfigStore.ts` | 344 | **der Dach-Zustand** — hält `build`, `additionalRoofs`, `cover`, `obstacles`, `modules` | nein |
| Ü-4 | `backend-laravel/app/Services/Energie/RoofTemplateFeatureExtractor.php` | 120 | leitet **Merkmalsspalten aus dem rohen Planer-State** ab, „die EINZIGE Wahrheit, kein Frontend-Input" | nein |
| Ü-5 | `backend-laravel/app/Services/Energie/PvBelegungExtractor.php` | 95 | **Modulanzahl, kWp, Belegung je Dachfläche** aus der tatsächlichen Platzierung × Modulleistung; „keine erfundene Leistung" bei unbekanntem `watts` | nein |
| Ü-6 | `src/pages/energie/DachplanerProPage.tsx` | 3786 | die Bedienoberfläche des PV-Dachplaners | **Design** |
| Ü-7 | `backend-laravel/app/Models/EnergieRoofModel.php` + `energie_roof_models` (Migration, Spalten u. a. **`dachform`**, `standard_dachneigung_grad`) | 30 + Schema | **Dach-Vorlagen persistent** | **JA — Datenbank** |

**Ü-4 und Ü-5 sind die wertvollsten Stücke.** Beide tragen dasselbe Prinzip, das hier fehlt:
*aus dem rohen Zustand ableiten, nie einen vorberechneten Frontend-Wert übernehmen.* Genau daran
hängt der A-05-Befund — es gibt hier keine Stelle, die aus einer Kontur eine Dachform ableitet.

## 2 · Zwei Konflikte, die ich NICHT still auflöse

### K-1 · `energie_roof_models` gegen `p_v_roofs` — **das ist Yamas Entscheidung**

ticket hat seit 2024 `p_v_roofs` (`PVRoof`, mit `roof_orientation` und `roof_azimuth`) und
`p_v_roof_plans`. Der Playground bringt `energie_roof_models` mit. **Beides nebeneinander wäre eine
zweite Dach-Wahrheit** — genau das, was die Schutzgrenze in `CLAUDE.md` untersagt.

Drei Wege, keiner davon von mir zu wählen:
1. `energie_roof_models` übernehmen, `p_v_roofs` bleibt für die Bestandserfassung — **getrennte Zwecke sauber benannt**
2. die Spalten (`dachform`, `standard_dachneigung_grad`) additiv an `p_v_roofs` anhängen — **eine Tabelle**
3. Vorlagen gar nicht persistieren, nur Ü-4/Ü-5 als Ableitung übernehmen — **kleinster Schnitt**

### K-2 · `DachplanerProPage.tsx` (Ü-6) kommt aus fremdem Design

3786 Zeilen Oberfläche aus dem Playground. Die Merkregel im Bestand sagt: **der Playground ist keine
Design-Vorlage**, Ansichten werden im ticket-Design gebaut. **Die Logik der Seite ist wertvoll, ihr
Aussehen nicht übernehmbar.** Vorschlag: Ü-6 nicht kopieren, sondern **auswerten** — was sie kann,
in ein Werkbank-Blatt, und die Oberfläche hier neu.

## 3 · Vorgeschlagene Reihenfolge

1. **Ü-1, Ü-2, Ü-3** — reine Typen und Zustand, kein Konflikt, kein Schema. *Fängt die Kette an.*
2. **Ü-4, Ü-5** — die zwei Extraktoren. Brauchen Ü-3 als Eingabeform. **Hier liegt der A-05-Schlüssel.**
3. **Ü-7** — erst nach K-1.
4. **Ü-6** — als Auswertung, nicht als Kopie; braucht Browserabnahme.

## 4 · Was ich NICHT gemessen habe

- **Ob die sieben Dateien fachlich richtig rechnen.** Ich habe Existenz, Umfang und ihre eigenen
  Zusagen im Dateikopf gelesen — nicht ihre Ergebnisse nachgerechnet.
- **Ob sie gegen ticket überhaupt übersetzen.** Der Playground hat eigene Nachbarmodule; welche
  davon mitkommen müssten, ist ungemessen.
- **Die Testabdeckung drüben.** `dachformVorlagen.test.ts` hat 1410 Zeilen — aber die Datei ist
  hier bereits identisch vorhanden, also kein Übernahmefall.
- **`~/Desktop/…/dachdecker_pro_3d.tsx` (M-01).** Steht in `BESTAND-YAMA.md` als „wertvollster
  Fund" und ist **nicht Teil dieser Messung** — eigener Durchgang.

## Nachtrag zu W-02/1 (nach CODE_FERTIG, für den Evaluator)

Mein `7-GRENZEN`-Blatt sagt „F-004 ist im Code nicht angebunden". **Beim Messen hier gefunden:**
`resources/planner/hausplaner/geometry/dachVerschneidung.ts` (205 Zeilen) berechnet Kehl- und
Gratlinien. Die Aussage im Blatt bleibt richtig — die Datei nennt **F-004 null mal**, und
`wallGeometry.ts` ruft sie **null mal** auf, es geht dort um Dach- und nicht um Wandecken. **Ich
melde es, damit der Evaluator es nicht selbst suchen muss, und ändere das abgegebene Blatt nicht.**
