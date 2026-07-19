# Prüfbericht Zimmermannmeister — PV-Dachplanung (Dachstuhl/Tragwerk)

**Datum:** 2026-06-12
**Prüfer:** Zimmermannmeister (fachlicher Prüfer Dachkonstruktion/Tragwerk)
**Gegenstand:** Fachkonzept + Umsetzungsplan PV-Dachplanung, Datenmodell `roofModel.ts`, 3D-Aufbau `RoofScene3D.tsx`
**Auftragsart:** NUR ANALYSE — kein Code geändert
**Geprüfte Dateien:**
`prompt-pv-dachplanung-fachkonzept.md`, `plan-pv-dachplanung-umsetzung.md`,
`konzept-produktdatenbank-import.md`, `src/utils/roofModel.ts`, `src/pages/energie/RoofScene3D.tsx`

> **Einordnung:** Konzept und Plan sind fachlich ambitioniert und richtig gedacht
> (Dachüberstand, Tragwerk, Material- und Montagelogik als Pflicht). Der **vorhandene
> Code** ist aber ein Visualisierungs-Prototyp, der mehrere zimmerhandwerkliche Grundregeln
> **falsch oder gar nicht** abbildet. Vor jeder Aussage gegenüber Kunden/Statik gilt: die
> aktuelle 3D-Darstellung ist eine **Skizze**, kein tragwerksgetreues Modell.

---

## Punkt 1 — Dachstuhl / Tragwerk

### 1.1 Sparrenrichtung im 3D ist falsch — [HOCH]
**Befund:** In `RoofScene3D.tsx:167` werden die Sparren so platziert:
`for (let u = rW/2; u <= W; u += rDist) place(L.sparren, rW, Lv, rH, u, Lv/2, …)`.
Damit ist jeder „Sparren" ein Balken mit Querschnitt `rW × rH`, der über die **volle Schräglänge `Lv`** läuft und **entlang u (Gebäudebreite, First-Richtung)** im Abstand `rDist` wiederholt wird. Das ist fachlich korrekt: Sparren laufen Traufe→First (= v-Richtung) und stehen im Achsabstand entlang des Firsts.
**Korrektur des ersten Eindrucks:** Die Richtung stimmt. **Aber** `rDist = sparrenAbstand/1000` als u-Schrittweite ist nur dann der reale Achsabstand, wenn `uDir` exakt der Firstlinie folgt — bei Walm/Pult mit verzogener Fläche stimmt das nicht mehr (siehe 1.5/2.x).
**Empfehlung:** Sparrenachsabstand als geometrisch garantierten Wert entlang der Traufe definieren, nicht über die u-Parametrisierung der ggf. nicht-rechteckigen PV-Fläche.

### 1.2 Pfettenlage fachlich falsch modelliert — [HOCH]
**Befund:** `RoofScene3D.tsx:169`:
`for (const vP of [0.4, Lv/2, Lv-0.4]) place(L.pfetten, W, 0.16, 0.16, W/2, vP, -rH-0.08, matPfette)`.
Die drei „Pfetten" werden als Balken `W` (= volle Breite, First-Richtung) **quer zur Schräge** an den Positionen v=0,4 m (Fußpfette), v=Lv/2 (Mittelpfette), v=Lv−0,4 m (Firstpfette) gelegt. Die Richtung (parallel First) ist richtig. **Falsch ist die statische Logik:**
- Bei einem **Sparrendach** (Konzept-Standard, Neigung 38°) gibt es **keine Mittelpfette** — die Sparren tragen als Dreieck Traufe→First. Eine Mittelpfette gehört zum **Pfettendach**. Das Modell zeigt immer beides gemischt.
- Die **Fußpfette** liegt nicht „0,4 m über Traufe auf der Schräge", sondern auf der Außenwand/Schwelle (Auflager). Position v=0,4 ist willkürlich.
- Die **Firstpfette** trägt nur beim Pfettendach; beim Sparrendach stoßen die Sparrenpaare am First gegeneinander (Firstpunkt, ggf. Firstbohle), **keine tragende Firstpfette**.
**Empfehlung:** Tragsystem als eigenes Feld einführen: `tragsystem: "sparrendach" | "kehlbalkendach" | "pfettendach"`. Pfetten nur erzeugen, wenn das Tragsystem sie verlangt. Pfettenauflager an reale Punkte (Außenwand, Innenwand/Stütze, First) koppeln.

### 1.3 Kehlbalken ohne statische Lage / nur Deko — [MITTEL]
**Befund:** `RoofScene3D.tsx:171`: ein einzelner horizontaler Riegel bei `v = Lv*0,62`, Querschnitt 0,1×0,16, frei in der Luft über der Schräge. Ein Kehlbalken verbindet **zwei gegenüberliegende Sparren** (Süd↔Nord) auf gleicher Höhe und reduziert die Sparren-Knicklänge. Im Modell gibt es nur die eine PV-Hauptfläche bestückt, der Kehlbalken hängt nur an dieser Fläche und verbindet nichts. Höhe „0,62·Schräglänge" ist nicht hergeleitet (übliche Lage: unteres Drittel bis Mitte, Höhe aus Raumhöhe/Statik).
**Empfehlung:** Kehlbalken als Querverbindung beider Sparrenflächen auf definierter Höhe `kehlbalkenHoeheAbTraufe` modellieren; nur bei `tragsystem = kehlbalkendach`.

### 1.4 Zangen / Wechsel / Auflager fehlen vollständig — [HOCH]
**Befund:** Im Datenmodell (`roofModel.ts`) existieren **keine** Felder für Zangen, Wechsel (Auswechslung um Störer) oder Auflagerpunkte. Im 3D werden sie nicht dargestellt. Der Umsetzungsplan §3.3 sieht `zangen?`, `wechsel?`, `kehlbalken?` vor — im **aktuellen Code aber nicht umgesetzt**. Besonders kritisch: Um Schornstein/Gaube/Dachfenster wird **kein Wechsel** gezeigt; die Module/Eindeckung laufen optisch durch den Störer hindurch.
**Empfehlung:** Vor jeder fachlichen Aussage „Auswechslung erforderlich" zumindest als Hinweis ausgeben, sobald ein Störer breiter als 1 Sparrenfeld ist (Faustregel: Öffnung > `sparrenAbstand` ⇒ Wechsel + ggf. verstärkte Wechselbalken).

### 1.5 Sparrenlänge / Ausklinkung (Kerve) / Aufschiebling fehlen — [MITTEL]
**Befund:** `rafterLength` wird abgeleitet (`(T/2 + oT)/cos(a)`), ist aber nur die **schräge Projektionslänge bis Trauf-Außenkante**, nicht die reale Sparrenlänge bis Firstschnitt/Überstand-Ende inkl. Profilhöhe. Es fehlen: Sparren-**Ausklinkung (Kerve/Klaue)** am Fußpunkt auf der Pfette, **Aufschiebling** (Knaggen für flacheren Traufanlauf bei großem Überstand), **Sparrenkopf-/Sparrenfußschnitt**. Die Bauteilliste (`roofModel.ts:235`) gibt Sparren-Lfm aus `geo.rafterLength`, also ohne diese Zuschläge ⇒ **Mengen zu knapp**.
**Empfehlung:** Sparrenlänge als eigenes abgeleitetes Maß inkl. Überstand bis Sparrenende und Längen-Zuschlag (Verschnitt/Kerve) ausweisen; Aufschiebling ab Traufüberstand-Schwelle (z. B. > 0,8 m) als Hinweis.

---

## Punkt 2 — Dachformen

### 2.1 Walmgeometrie ist mathematisch degeneriert (falsch) — [HOCH]
**Befund:** `roofModel.ts:188-189`:
```
planes.push({ id: "walm_l", …, corners: [eaveN_L, eaveS_L, ridgeS_L, ridgeS_L] });
planes.push({ id: "walm_r", …, corners: [eaveS_R, eaveN_R, ridgeS_R, ridgeS_R] });
```
Der Walm wird als Viereck mit **doppeltem Eckpunkt** (`ridgeS_L, ridgeS_L`) modelliert — also ein entartetes Quad, das im Renderer (`quad()`, zwei Dreiecke) eine Null-Fläche und falsche Normalen erzeugt. Zudem treffen **alle Walmecken auf denselben Firstpunkt** der Hauptschräge; die Walmfläche ist in Wahrheit ein **Trapez** (bei Walmdach) bzw. ein **Dreieck** mit eigener Walm-Neigung. Es gibt **keinen Gratsparren** und **keine eigene Walmneigung**.
**Folge:** Walmdach-Darstellung ist geometrisch falsch (Walmfläche schließt nicht sauber an, Grat fehlt). Aussagen zu Walm-Dachfläche, Eindeckmenge und PV-Restfläche am Walm sind unzuverlässig.
**Empfehlung:** Walm als eigene Fläche mit eigenem Firsteinzug **und eigener Walmschräge** rechnen; Gratsparren als Diagonale Trauf-Ecke→First-Ende einführen (`edges` mit Typ `grat`). Walmneigung optional getrennt von Hauptneigung (oft gleich, aber konstruktiv eigenständig).

### 2.2 Krüppelwalmdach = Walmdach (nicht unterscheidbar) — [HOCH]
**Befund:** `roofModel.ts:169` behandelt `walmdach` und `kruppelwalmdach` identisch (`isWalm`). Der **Krüppelwalm** (Schopfwalm) ist genau dadurch definiert, dass der Walm **nur das obere Giebeldreieck** abdeckt und unten ein **Giebelrest (Drempel/senkrechte Giebelfläche)** bleibt. Im Modell fehlt der dafür nötige Parameter (Walmhöhe / Krüppelwalm-Ansatzhöhe). Damit ist Krüppelwalm nicht abbildbar.
**Empfehlung:** Feld `walmHoehe` bzw. `krueppelAnsatz` (Höhe ab First, ab der der Walm beginnt) einführen; Giebelrestfläche unter dem Walm als eigene (senkrechte) Fläche modellieren.

### 2.3 Firstverkürzung willkürlich / nicht aus Neigung hergeleitet — [MITTEL]
**Befund:** `roofModel.ts:170`: `walmEinzug = min(B/2 − 0,5, T/2)`. Der Firsteinzug eines Walms ist **nicht frei wählbar**, sondern ergibt sich aus Walm- und Hauptneigung (bei gleicher Neigung läuft der Grat unter 45° im Grundriss ⇒ Einzug = T/2). Der Wert `B/2 − 0,5` ist eine Behelfsannahme und kann den First negativ/zu kurz machen.
**Empfehlung:** Firsteinzug aus Grundriss-Geometrie der Grate ableiten (`einzug = (T/2) · tan(walm)/tan(haupt)` bei gleicher Traufhöhe; bei gleicher Neigung = T/2). Plausibilitätsgrenze: Einzug < (B/2 − Mindestfirstlänge).

### 2.4 Pultdach: Auflager/Höhenlogik unkritisch, aber Traufzuordnung prüfen — [NIEDRIG]
**Befund:** Pultdach (`roofModel.ts:143`) ist geometrisch plausibel (eine Schräge, Trauf-/Firstkante). Es fehlt die Unterscheidung **niedrige Traufe (Pulttraufe) vs. hohe Wand (Pultfirst/Attika)** als benannte Kante und das Auflager auf der höheren Wand.
**Empfehlung:** Kanten typisieren (Traufe unten, „Pultfirst" oben), damit Rinne nur an der tatsächlichen Traufe sitzt.

### 2.5 Flachdach: Tragwerk komplett ausgeblendet — [MITTEL]
**Befund:** Beim Flachdach (`roofModel.ts:124`) werden zwar Sparren/Pfetten-Loops über die PV-Fläche gezeichnet, fachlich hat ein Flachdach aber i. d. R. **Massivdecke oder Flachdachsparren/Balkenlage + Gefälledämmung**, keine geneigten Dachsparren mit Konter-/Eindecklattung. Der gesamte Konstruktionsteil aus `RoofScene3D` (Sparren, Konterlatte, Eindecklatte, Firstziegel) ist beim Flachdach **fachlich unsinnig**, wird aber trotzdem erzeugt (Firstziegel sind nur per `roofType`-Guard ausgenommen, Lattung nicht).
**Empfehlung:** Aufbau-Layer je Dachtyp gültig schalten (Flachdach: Abdichtung/Gefälledämmung/Attika statt Lattung); Bauteilliste entsprechend trennen.

### 2.6 Schleppdach/Mansarddach — [NIEDRIG]
**Befund:** Im Datenmodell `RoofType` (`roofModel.ts:9-14`) fehlen `schleppdach` und `mansarddach` (Konzept als spätere Erweiterung markiert — korrekt). Mansarddach braucht **zwei Neigungen** (`neigung2` ist im Plan §3.2 vorgesehen, im Code-Modell nicht vorhanden).
**Empfehlung:** Bei Aufnahme zwingend zweite Neigung + Knicklinie (Mansardgesims) modellieren; Schleppgaube/Schleppdach als angesetzte flache Teilfläche.

---

## Punkt 3 — Dachüberstand & Höhenlogik (First/Traufe)

### 3.1 Firsthöhe nicht als Eingabe — nur Neigung führend — [HOCH]
**Befund:** `RoofDimensions` (`roofModel.ts:18-26`) kennt nur `traufhoehe` und `neigung`; die Firsthöhe wird **immer** aus der Neigung gerechnet (`rise = (T/2)·tan(a)`, `yR = H + rise`). Das Konzept fordert ausdrücklich **„First ODER Neigung führend"** (Plan §3.2: `firsthoehe?`, `neigung`). In der Praxis ist oft die **Firsthöhe vorgegeben** (Bebauungsplan/Nachbar) und die Neigung ergibt sich. Aktuell nicht möglich ⇒ Nutzer kann reale Bestandsdächer nicht maßgetreu eingeben.
**Empfehlung:** `firsthoehe?` ins Modell aufnehmen; wenn gesetzt, Neigung daraus ableiten (`neigung = atan((firsthoehe − traufhoehe)/(T/2))`) und im UI gegenseitig sperren/anzeigen (führendes Feld markiert).

### 3.2 Traufüberstand vs. Firstüberstand — Firstdetail fehlt — [MITTEL]
**Befund:** Modell kennt `ueberstandTraufe` und `ueberstandOrtgang`, aber **keinen First-/Pultüberstand** (Plan §3.2 sieht `ueberstand.first?` vor). Bei Pultdach und an Anschlüssen ist der Firstüberstand real relevant.
**Empfehlung:** `ueberstandFirst?` ergänzen; bei Walm First-Überstand entfällt (kein offener First) — als Plausibilitätsregel.

### 3.3 Trauf-/Ortgangüberstand getrennt links/rechts — [NIEDRIG]
**Befund:** `ueberstandOrtgang` ist **ein** Wert für beide Seiten; das Konzept (Punkt 1, Plan §3.2) fordert **Ortgangüberstand links/rechts getrennt** (asymmetrische Reihenhaus-/Grenzbebauung). Traufüberstand ist nur ein Wert (meist ausreichend symmetrisch).
**Empfehlung:** `ueberstand.ortgangLinks/ortgangRechts` wie im Plan vorgesehen umsetzen.

### 3.4 Überstand-Höhenversatz an Traufe plausibel, aber ohne Aufschiebling-Logik — [MITTEL]
**Befund:** Die Traufkante wird abgesenkt (`eaveY = H − oT·tan(a)`, `roofModel.ts:163`). Das ist die korrekte Verlängerung der Sparrenunterkante über die Wand hinaus. Es fehlt aber die Kopplung an einen **Aufschiebling**, der bei großem Überstand den Traufanlauf abflacht — siehe 1.5. Reiner geometrischer Versatz ohne Hinweis kann zu unrealistisch tiefer Traufkante führen.
**Empfehlung:** Ab Überstand > ~0,8–1,0 m Hinweis „Aufschiebling/Knaggen prüfen" (teilweise vorhanden in `validateRoof`, s. u.) und Traufanlauf optional abflachen.

---

## Punkt 4 — Fehlende Tragwerksfelder im Datenmodell

`RoofConstruction` (`roofModel.ts:28-37`) enthält Sparren/Latten/Dämmung/Eindeckung,
aber **keine** der folgenden tragwerksrelevanten Felder:

**Fehlt im Modell/Code (Tragwerk):**
- `tragsystem` (Sparren-/Kehlbalken-/Pfettendach) — entscheidet, welche Pfetten überhaupt existieren.
- `firsthoehe?` (First führend statt Neigung) — Punkt 3.1.
- `neigung2?` / Mansard-Knick — Punkt 2.6.
- `sparrenLaenge` (real, inkl. Überstand bis Sparrenende) — Punkt 1.5.
- `ausklinkung` / Kerve (Tiefe, Position am Fußpunkt).
- `aufschiebling` (ja/nein, Höhe) — Punkt 1.5/3.4.
- `pfetten[]` mit echter **Lage + Auflager** (Außenwand/Innenwand/Stütze) statt fixer v-Positionen — Punkt 1.2.
- `pfettenstuetzen` / `stiele` (Mittelpfettenstützen) bei großer Gebäudetiefe — heute nur Textwarnung.
- `kehlbalken` mit Höhe ab Traufe + Querschnitt + Bezug auf beide Sparrenflächen — Punkt 1.3.
- `zangen` (Querschnitt, Lage) — Punkt 1.4.
- `wechsel[]` (je Störer: Wechselbalken, verstärkte Sparren) — Punkt 1.4.
- `gratsparren` / `kehlsparren` (Walm/Kehle) inkl. Querschnitt — Punkt 2.1.
- `ortgangsparren` (Ortgang-/Schalbrettsparren als äußerster Sparren).
- `stirnbrett` / `traufbohle` (im Plan §3.2 vorgesehen, im Code-Modell fehlend; im 3D nur generisches „Ortgangbrett").
- `firstbohle` (Sparrendach) vs. `firstpfette` (Pfettendach).
- `auflagerpunkte[]` (Wand-/Schwellen-/Pfettenlinien) — für Statik-Plausibilität.
- Holzgüte/Festigkeitsklasse je Bauteil (`C24`, `GL24h` …) — für spätere Bemessung; das Produktkonzept nennt KVH/BSH, das Tragwerksmodell trägt sie nicht.

**Fehlt im Modell/Code (Geometrie-Output):**
- `edges` mit Typ `grat`/`kehle` (im Plan §3.2 vorgesehen, in `RoofGeometry` `roofModel.ts:96-104` **nicht** vorhanden — nur `planes`/`gables`).
- Reale Walm-/Kehlflächen statt degenerierter Quads — Punkt 2.1.

---

## Punkt 5 — Plausibilitäts-/Statikgrenzen (Warnregeln)

**Vorhanden** in `validateRoof` (`roofModel.ts:205-216`): flache/steile Neigung, großer/kleiner
Traufüberstand, großer Sparrenabstand, Lattenabstand vs. Ziegel, Gebäudetiefe > 14 m ⇒ Mittelpfette.
Das ist ein guter Anfang, aber zimmerhandwerklich zu grob. **Fehlende Regeln:**

- **Sparrenhöhe vs. Stützweite (Kernregel) — [HOCH]:** Es gibt **keine** Prüfung Sparrenquerschnitt ↔ freie Sparrenlänge. Faustwert Zimmerei: Sparrenhöhe ≈ Stützweite/20 (frei gespannt). Bei `rafterLength` z. B. 6 m und `sparrenHoehe` 180 mm ist 180 < 300 ⇒ Warnung „Sparrenhöhe knapp, Kehlbalken/Mittelpfette oder höherer Querschnitt prüfen". Heute völlig ungeprüft, obwohl das die häufigste Fehlentscheidung ist.
- **Pfettenstütze ab Gebäudetiefe gestaffelt — [MITTEL]:** Aktuelle Grenze pauschal > 14 m. Beim **Sparrendach** ist schon ab ~4,5–5,0 m freier Sparrenlänge ein Kehlbalken/Mittelpfette nötig (nicht erst bei 14 m Gesamttiefe). Regel an freie Sparrenlänge koppeln, nicht an Gesamttiefe.
- **Überstand vs. Aufschiebling — [MITTEL]:** Warnung > 1,2 m vorhanden, aber kein Bezug zu Aufschiebling/Sparrenkopf-Auskragung (max. Kragarm ≈ 1/3 Stützweite des angrenzenden Feldes).
- **Neigung vs. Regeldachneigung der gewählten Eindeckung — [HOCH]:** `validateRoof` warnt nur generisch < 7°/„≥22°". Real ist die **Regeldachneigung modellabhängig** (Biberschwanz 30°, Frankfurter Pfanne 22°, Schiefer 25–30°, Trapezblech ~7°). Erst mit Produktdatenbank (`min_roof_pitch_deg`) sauber prüfbar — bis dahin **keine harten Aussagen** treffen.
- **Walmgrat-Plausibilität — [MITTEL]:** Keine Prüfung, ob Firsteinzug < halbe Gebäudebreite (sonst kein First). Bei Walm fehlt Prüfung Mindest-Firstlänge.
- **Schneelast-/Windlastzone — [MITTEL]:** Weder Eingabe noch Hinweis. Für Sparren-/Pfettenbemessung und PV-Ballast (Flachdach) zwingend; Konzept nennt Windlastzonen beim Flachdach, Modell trägt sie nicht.
- **Sparrenabstand ↔ Modulraster/Dachhaken — [MITTEL]:** Konzept fordert „Haken auf passendem Sparren". Heute setzt der 3D-Code Dachhaken im Raster `rDist` (`RoofScene3D:213`), prüft aber **nicht**, ob die Schiene/der Haken tatsächlich über einem Sparren liegt. Faustregel Haken ↔ Sparren fehlt als Regel.

---

## Punkt 6 — Fehlende Norm-/Herstellerdaten

Diese Daten fehlen und dürfen **nicht erfunden** werden (Konzept-Grundsatz „nichts erfinden,
offene Felder markieren" ist hier strikt einzuhalten):

- **EC 5 / DIN EN 1995-1-1 (Holzbau-Bemessung)** und national **DIN EN 1991-1-3/-1-4** (Schnee/Wind): nur als **Hinweisebene** nötig — die App soll **keine Standsicherheitsnachweise** führen, sondern Plausibilität + Verweis „Statik durch Tragwerksplaner". (DIN 1052 ist zurückgezogen, durch EC5 ersetzt — im Modell/Plan korrekt nur als Hinweis nennen.)
- **KVH-/BSH-Querschnittsreihen** (Konstruktionsvollholz/Brettschichtholz): genormte Standardquerschnitte (z. B. KVH 60/120, 80/160, 100/200; BSH GL24h-Reihen). Aktuell sind Querschnitte frei (mm), ohne Bezug zu lieferbaren Reihen ⇒ Material-/Bauteilliste kann unlieferbare Maße ausgeben. **Quelle:** Herstellerprogramme (z. B. Holzindustrie), als Produkt-Kategorie `sparren`/`pfette`/`schalung` im Produktkonzept §B vorgesehen — dort einpflegen.
- **Festigkeitsklassen** (C24 Nadelholz Standard, GL24h/GL28h BSH): fehlen als Bauteilattribut.
- **Regeldachneigung-Tabellen je Eindeckungsmodell** (ZVDH-Fachregeln, Herstellerangaben): liefert die geplante Produktdatenbank (`min_roof_pitch_deg`, „Regeldachneigung = offen" laut `konzept-produktdatenbank-import.md` Abschnitt C). Bis Import erfolgt: **keine harten Neigungsaussagen**.
- **ZVDH-Fachregeln für Aufschiebling/Traufausbildung/First-Hinterlüftung**: als Quelle für die Regeldetails referenzieren, nicht hartkodieren.
- **PV-Montagesystem-Statik (Kragarm Schiene, Haken-Tragfähigkeit, Sparren-Mindestquerschnitt für Hakenmontage):** herstellerspezifisch (z. B. Schienen-Stützweiten) — im Montageregelwerk (`konzept-produktdatenbank-import.md` Abschnitt E) als Daten mit Quelle pflegen.

---

## Punkt 7 — Risiken fachlich falscher Darstellung/Aussage

- **R1 [HOCH] Walm-/Krüppelwalmdarstellung ist falsch** (degenerierte Fläche, kein Grat, beide Typen identisch). Kunde/Monteur sieht eine geometrisch unmögliche Dachfläche; PV-Restfläche am Walm und Eindeckmengen sind unzuverlässig. → Walmdächer bis zur Korrektur **nicht für verbindliche Belegung/Mengen** verwenden.
- **R2 [HOCH] Tragsystem-Mischung** (Mittel-/Firstpfette beim Sparrendach gleichzeitig sichtbar). Ein Zimmermann erkennt sofort einen unstimmigen Dachstuhl; gegenüber Statiker/Bauherr wirkt das Werkzeug unseriös. → Tragsystem-Feld vor Kundennutzung.
- **R3 [HOCH] Keine Sparrenquerschnitt-↔-Stützweiten-Prüfung.** Das Werkzeug könnte ein zu schwaches Dach „grün" darstellen. → Warnregel + überall sichtbarer Disclaimer „keine Statik, Nachweis durch Tragwerksplaner".
- **R4 [MITTEL] Mengen in der Bauteilliste zu knapp** (Sparrenlänge ohne Überstand-/Kerve-Zuschlag, kein Verschnitt, Flachdach mit Lattung). → Mengen als „Richtwert, ohne Verschnitt" kennzeichnen.
- **R5 [MITTEL] Störer ohne Wechsel/Aussparung** — Eindeckung/Module laufen optisch durch Schornstein/Gaube. → bis Wechsel-/Sperrflächenlogik (Plan Phase 4) klarer Hinweis „Auswechslung/Aussparung nicht dargestellt".
- **R6 [MITTEL] Firsthöhe nicht eingebbar** → Bestandsdächer nach Bebauungsplan nicht maßtreu abbildbar; Aussagen zu Höhe/Neigung können von der Realität abweichen.
- **R7 [NIEDRIG] Flachdach-Tragwerk fachlich unpassend** dargestellt (Sparrenlogik statt Decke/Gefälle). → Layer je Dachtyp gültig schalten.

---

## Gesamt-Fehltliste „fehlt im Modell/Code" (kompakt)

| Bereich | Fehlend | Schwere |
|---|---|---|
| Tragsystem-Unterscheidung (Sparren-/Kehlbalken-/Pfettendach) | ja | HOCH |
| Korrekte Walmfläche + Gratsparren (statt degeneriertem Quad) | ja | HOCH |
| Krüppelwalm-Parameter (Walm-/Schopfhöhe) | ja | HOCH |
| Firsthöhe als führende Eingabe (`firsthoehe?`) | ja | HOCH |
| Sparrenhöhe-↔-Stützweite-Warnregel | ja | HOCH |
| Regeldachneigung je Eindeckungsmodell (aus Produkt-DB) | ja | HOCH |
| Pfettenauflager an reale Punkte koppeln | ja | MITTEL |
| Kehlbalken als echte Querverbindung beider Flächen | ja | MITTEL |
| Zangen / Wechsel / Auswechslung um Störer | ja | MITTEL |
| Sparrenlänge real + Ausklinkung/Kerve + Aufschiebling | ja | MITTEL |
| Grat-/Kehlsparren, Ortgangsparren, Stirnbrett/Traufbohle/Firstbohle | ja | MITTEL |
| `edges` mit Typ grat/kehle in RoofGeometry | ja | MITTEL |
| Ortgangüberstand links/rechts getrennt + Firstüberstand | ja | MITTEL |
| Holzgüte/Festigkeitsklasse + KVH/BSH-Querschnittsreihen | ja | MITTEL |
| Schnee-/Windlastzone (Eingabe/Hinweis) | ja | MITTEL |
| Flachdach: Tragwerk/Aufbau dachtyp-gültig schalten | ja | MITTEL |
| Mansard-Knick (`neigung2`) + Schleppdach | ja | NIEDRIG |
| Haken-↔-Sparren-Lageprüfung | ja | MITTEL |

## Offene Norm-/Herstellerdaten (nicht erfinden, als Quelle pflegen)

- EC5 / DIN EN 1995-1-1 (Holzbau), DIN EN 1991-1-3/-1-4 (Schnee/Wind) — **nur Hinweisebene**, kein Nachweis im Tool. (DIN 1052 zurückgezogen → EC5.)
- KVH-/BSH-Standardquerschnitte + Festigkeitsklassen (C24, GL24h…) — Herstellerprogramme.
- ZVDH-Fachregeln (Regeldachneigung, Traufe/Aufschiebling, First-Hinterlüftung).
- Regeldachneigung je Ziegelmodell — aus geplanter Produkt-DB (`min_roof_pitch_deg`; Regeldachneigung dort als `offen`).
- PV-Montagesystem-Statik (Schienen-Kragarm, Haken-Tragfähigkeit) — herstellerspezifisch, ins Montageregelwerk.

---

## Empfehlung zur Reihenfolge (zimmermannseitig priorisiert)

1. **Disclaimer überall:** „Skizze, keine Statik — Nachweis durch Tragwerksplaner" (sofort, ohne Codeumbau, rein fachliche Pflicht).
2. **Tragsystem-Feld** einführen, Pfetten-/Kehlbalken-Logik daran koppeln (behebt R2).
3. **Sparrenhöhe-↔-Stützweite-Warnung** + freie-Sparrenlänge-Regel (behebt R3, häufigster Fehler).
4. **Firsthöhe als führende Eingabe** (behebt R6, hoher Praxisnutzen).
5. **Walmgeometrie korrekt** (echte Walmfläche + Gratsparren), danach Krüppelwalm-Parameter (behebt R1).
6. Sparrenlängen-/Mengenzuschläge + Flachdach-Aufbau trennen (behebt R4/R7).
7. Wechsel/Zangen + Störer-Aussparung (mit Plan-Phase 4).

---

## Kurz-Zusammenfassung (max. 8 Zeilen)

Konzept und Plan sind fachlich richtig gedacht, der vorhandene Code ist aber ein
Visualisierungs-Prototyp mit mehreren zimmerhandwerklichen Fehlern. Schwerwiegend [HOCH]:
(1) Walm-/Krüppelwalmdach sind geometrisch falsch (degenerierte Fläche, kein Gratsparren,
beide Typen identisch); (2) Pfettenlogik mischt Sparren-/Pfettendach (Mittel-+Firstpfette
beim Sparrendach); (3) keine Sparrenquerschnitt-↔-Stützweiten-Prüfung; (4) Firsthöhe ist
nicht eingebbar (nur Neigung führend). Es fehlen Tragwerksfelder: Tragsystem, Sparrenlänge/
Ausklinkung/Aufschiebling, Zangen/Wechsel/Auflager, Grat-/Kehlsparren, Holzgüte/KVH-BSH-Reihen.
Statikgrenzen und Norm-/Herstellerdaten (EC5 nur als Hinweis, Regeldachneigung je Ziegel,
KVH/BSH) sind als konfigurierbare Regeln mit Quelle zu pflegen — nichts erfinden. Bis dahin:
Walmdächer nicht für verbindliche Mengen nutzen, überall „keine Statik"-Disclaimer.
