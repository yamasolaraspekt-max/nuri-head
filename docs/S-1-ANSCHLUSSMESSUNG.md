# S-1 — Anschlussmessung über die 37 `BESCHRIEBEN`-Zeilen

**Auftrag:** *„AN DEN PLANNER"*, 19.08., Schritt 1. **Kein Bau. Eine Liste.**
**Messstand:** `d0d62e49` · gemessen 19.08. abends · Rolle: Generator (Rollenwechsel auf Yamas
ausdrückliche Anweisung; die Abnahme liegt bei einer anderen Instanz).

---

## 0 · Was diese Messung beantworten kann — und was der Auftrag nicht wusste

Der Auftrag verlangt je Zeile drei Fragen (A angeschlossen · B soll es · C was fehlt). **Frage A
lässt sich nicht je Zeile beantworten, weil es keine Brücke von der Registerzeile zur
Registry-Kennung gibt.** Gemessen:

```
Registerzeilen, die eine Registry-Kennung nennen      2 von 37   (W-03 trimmen, W-11 auswahl)
Werkbank-Verzeichnisse, die 'toolRegistry' nennen    15 von 43
```

**Deshalb ist A hier über den Code beantwortet statt über die Zeile**, und zwar nach dem Maßstab,
den A-35 gesetzt hat: *hat das Modul, das die Blätter der Zeile führen, einen **Produktivaufrufer**
oder nur Tests?* `geradenGeometrie` hatte null, bis A-35 einen machte — das ist der gemessene
Unterschied zwischen „gebaut" und „angeschlossen".

**Zählbefehl statt Zahl:**

```bash
grep -rlE "from '[^']*<modul>'" --include='*.ts' --include='*.tsx' \
  resources/planner/hausplaner | grep -v __tests__ | grep -v "/<modul>\."
```

**Und eine Trennung, die der Auftrag noch nicht hatte:** *Zeile angeschlossen* und *Leitmodul
benutzt* sind zwei verschiedene Aussagen. W-10 „Decke und Boden" ist als Werkzeug erreichbar
(`decke` steht in der Registry) — **und das Modul, das seine Blätter führen, ist trotzdem tot.**
Die Tabelle unten trennt das.

---

## 1 · Frage B hat eine mechanische Antwort, und sie steht schon im Code

Der Auftrag fragt in §8, wozu der Werkzeugkasten da ist, und will es als Ordnungskriterium.
**`app/tools/faehigkeiten.ts` trägt es als getipptes Feld:**

| `FaehigkeitArt` | Bedeutung | Zahl |
|---|---|---|
| `'werkzeug'` / `'aktion'` | der Nutzer klickt es, es setzt `activeToolId` | **13**, alle erreichbar |
| `'engine'` | reine Eingang→Ergebnis-Rechnung, von einer Fläche aufgerufen | **13**, davon 8 mit Panel |

**26 Fähigkeiten, 21 `verfuegbar`, 5 `in_entwicklung`.** Die dritte Gruppe ist **absichtlich leer**
(`werkzeugKatalogFaehigkeiten = []`), mit der Begründung im Code: *„Bis I2 spiegelte die Navi eine
Teilmenge des Katalogs als `cad-*`. Das waren anklickbare Zeilen ohne Handler — falsche
Versprechen (AUF-28)."*

> **Berichtigung zur Vorlage:** sie nennt *„9 `verfuegbar` · 1 `schlaeft`"*. **`'schlaeft'` ist kein
> Wert** — `FaehigkeitZustand` kennt `verfuegbar · voraussetzung · nur_ergebnis · in_entwicklung`;
> das Wort steht nur in drei Kommentaren aus Batch 0. Und die 9 zählt Zeichenketten, nicht
> Fähigkeiten: die dreizehn Werkzeuge kommen über `TOOL_DEFINITIONS.map()` und stehen nirgends als
> Literal.

---

## 2 · Der Befund: sechs Module ohne jeden Produktivaufrufer

**Jedes einzeln gegengeprüft** — nicht nur der Import gezählt, sondern jede Nennung im Baum
angesehen:

| Zeile | Modul | Zeilen | Produktiv­aufrufer | Lage |
|---|---|---|---|---|
| **W-10** | `renderers/three-d/deckenMesh.ts` | — | **0** | **keine einzige Nennung im Baum.** `szene.ts:451-478` rendert die Decken inline über `bodenPunkteThree` aus `platzierung` |
| **W-13** | `geometry/trefferSuche.ts` | — | **0** | einzige Nennung: `werkzeugLandkarte.ts:248`, Vertrag `suche`, Marke `ohne-modell` |
| **W-20** | `geometry/holzMengen.ts` | — | **0** | als `engine-holzmengen` registriert, `zustand: 'in_entwicklung'` — sichtbar, nicht aufgerufen |
| **W-21** | `geometry/auswechslung.ts` | — | **0** | nur Kommentare in `sparrenTrennung.ts` und `dachOeffnung.ts` |
| **W-25 / W-43** | `geometry/holzBauteile.ts` | **82** | **0** | als `engine-holzbauteile` registriert, `in_entwicklung`; sonst nur Kommentare |
| **W-30** | `geometry/dachVorlage.ts` | **34** | **0** | **keine Nennung außerhalb der eigenen Datei und eines Tests** |

**Das sind die Kandidaten für den Anschlussvorrat aus Schritt 3 — gemessen, nicht geschätzt.**
Drei davon (`holzMengen`, `holzBauteile`, `dachVorlage`) gehören zum Holzbau/Dach und hängen
zusammen; `deckenMesh` ist ein Sonderfall, weil dort nicht der Anschluss fehlt, sondern **ein
zweiter Weg gebaut wurde** und der erste liegen blieb.

---

## 3 · Die übrigen 31 Zeilen: Leitmodul hat Produktivaufrufer

| Zeile | Leitmodul | Aufrufer | erste Fundstelle |
|---|---|---|---|
| W-01 | `fangKern` | 1 | `app/HausplanerApp.tsx` |
| W-02 | `wallGeometry` | **12** | `renderers/three-d/segmentierung.ts` |
| W-03 | `EigenschaftenPanel` | 1 | `app/HausplanerApp.tsx` |
| W-04 | `oeffnungsTypen` | 2 | `app/HausplanerApp.tsx` |
| W-05 | `roomDetection` | 5 | `renderers/three-d/szene.ts` |
| W-06 | `geschossStapel` | 3 | `app/HausplanerApp.tsx` |
| W-07 | `dachGeometrie` | 5 | `renderers/three-d/szene.ts` |
| W-08 | `polygonFlaeche` | 4 | `renderers/three-d/deckenMesh.ts` ⚠ *(der einzige Aufrufer ist selbst tot — siehe W-10)* |
| W-09 | `treppenBerechnung` | 5 | `app/dashboard/enginePanels.ts` |
| W-11 | `masseingabe` | 1 | `app/HausplanerApp.tsx` |
| W-12 | `szene` | 1 | `app/DreiDBereich.tsx` |
| W-14 | `editierGeometrie` | 5 | `app/sammelBefehle.ts` |
| W-16 | `kalibrierung` | 2 | `app/unterlage/UnterlagenWerkzeuge.tsx` |
| W-17 | `speicherAnzeige` | 3 | `app/HausplanerApp.tsx` |
| W-18 | `kontur` | 1 | `app/HausplanerApp.tsx` |
| W-22 | `gaubeGeometrie` | 2 | `renderers/three-d/dachAufbautenMesh.ts` |
| W-23 | `dachformVorlagen` | 1 | `renderers/three-d/dachMesh.ts` |
| W-26 | `aufbauOrientierung` | 1 | `geometry/gaubeGeometrie.ts` |
| W-28 | `linienBauteile` | 2 | `geometry/dachAusschnitt.ts` |
| W-29 | `dachOeffnung` | 1 | `geometry/dachAusschnitt.ts` |
| W-31 | `pvBelegung` | 1 | `app/dashboard/enginePanels.ts` |
| W-33 | `StartView` | 1 | `app/HausplanerStudio.tsx` |
| W-34 | `GuidedView` | 1 | `app/HausplanerStudio.tsx` |
| W-35 | `ConfigWizard` | 3 | `app/HausplanerStudio.tsx` |
| W-36 | `FaehigkeitenNavi` | 1 | `app/rahmen/GruppenzeileUndSchiene.tsx` |
| W-37 | `EngineFlaeche` | 2 | `app/HausplanerApp.tsx` |
| W-38 | `studioDaten` | **22** | `app/StartView.tsx` |
| W-39 | `HausplanerStudio` | 1 | `main.tsx` |
| W-40 | `configuratorPackage` | 2 | `app/ConfigWizard.tsx` |
| W-42 | `paketSpeichern` | 2 | `main.tsx` |

**W-08 ist der Nachsatz, der die Zahl relativiert:** `polygonFlaeche` hat vier Aufrufer, und der
erste ist `deckenMesh` — ein Modul, das selbst niemand aufruft. **Eine Aufruferzahl ist kein Beleg
für Erreichbarkeit, solange die Aufrufer nicht selbst erreichbar sind.** Das ist die Grenze dieser
Messung und sie wird hier genannt, nicht verschwiegen.

---

## 4 · Was diese Liste NICHT ist

- **Keine Lückenzahl.** Es steht nirgends „24 fehlen". Es steht: sechs Module haben null
  Produktivaufrufer, einzeln belegt.
- **Kein Bauauftrag.** Ob ein totes Modul angeschlossen oder gestrichen gehört, ist der nächste
  Schritt, nicht dieser. `dachVorlage.ts` mit 34 Zeilen ist ein anderer Fall als `holzBauteile.ts`
  mit 82 und einer Registrierung als Engine.
- **Keine Erreichbarkeitsprüfung bis zur Oberfläche.** Gemessen ist die Importkette, nicht der
  Klick. Der W-08-Nachsatz zeigt, warum das ein Unterschied ist. **Für die sechs Kandidaten ist es
  egal — null Aufrufer heißt null Kette.** Für die einunddreißig ist es eine offene Verschärfung.
