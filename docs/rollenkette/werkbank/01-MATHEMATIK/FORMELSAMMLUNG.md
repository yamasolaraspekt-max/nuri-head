# FORMELSAMMLUNG

> Jede Formel hat eine feste Nummer. Werkzeuge verweisen auf diese Nummern,
> nie auf abgeschriebene Formeln. **Eine Formel steht genau einmal.**
>
> Aufbau je Eintrag: Zweck · Eingabe · Formel · Ausgabe · Grenzfall.
> **Der Grenzfall ist Pflicht** — er ist die Stelle, an der später der Fehler sitzt.

---

## Gruppe 1 — Punkte, Strecken, Winkel

### F-001 · Abstand zweier Punkte
- **Zweck:** Länge einer Wand, Prüfung auf Deckungsgleichheit
- **Eingabe:** P₁=(x₁,y₁), P₂=(x₂,y₂) in mm
- **Formel:** `d = √((x₂−x₁)² + (y₂−y₁)²)`
- **Ausgabe:** Abstand in mm
- **Grenzfall:** `d < ε` (0,5 mm) → beide Punkte gelten als **derselbe**. Eine Wand
  mit d < ε darf nicht angelegt werden — sie erzeugt später eine Division durch null.

### F-002 · Richtungswinkel einer Strecke
- **Zweck:** Wandrichtung, Ausrichtung einer Dachfläche
- **Eingabe:** P₁, P₂
- **Formel:** `φ = atan2(y₂−y₁, x₂−x₁)`
- **Ausgabe:** Winkel in Radiant, Bereich (−π, π]
- **Grenzfall:** P₁ = P₂ → `atan2(0,0)` liefert 0, ist aber **bedeutungslos**.
  Vorher F-001 prüfen. `atan2` und nicht `atan(Δy/Δx)` — sonst geht der Quadrant verloren.

### F-003 · Lotfußpunkt auf eine Strecke
- **Zweck:** Fang „Lot", Öffnung auf Wandachse einrasten
- **Eingabe:** Punkt P, Strecke A→B
- **Formel:**
  ```
  t = ((P−A)·(B−A)) / |B−A|²
  t' = max(0, min(1, t))            ← auf die Strecke begrenzen
  L = A + t'·(B−A)
  ```
- **Ausgabe:** Lotfußpunkt L, Parameter t' ∈ [0,1]
- **Grenzfall:** `|B−A|² = 0` → keine Strecke, Absage. Ohne das Begrenzen auf [0,1]
  landet der Fang auf der **Verlängerung** der Wand statt auf der Wand.

> ### Am Bestand nachgezogen (10.08.) — der Code weicht ZWEIMAL ab, beide Male mit Grund
>
> Belegstelle: `resources/planner/hausplaner/geometry/fangKern.ts:96-105`, `lotAufGerade()`.
>
> **1 · Die Begrenzung auf `[0,1]` fehlt — und das ist Absicht, kein Mangel.**
>
> ```text
> gemessen:  0 Treffer auf Math.max / Math.min / clamp in lotAufGerade()
> Code:      return { x: a.x + t * dx, y: a.y + t * dy };   // t UNBEGRENZT
> ```
>
> Der Grenzfall oben nennt das Ergebnis („landet auf der Verlängerung") und behandelt es als
> Fehler. **Im Fang ist es das Ziel:** `achse` ist ausdrücklich *„Lot auf die VERLÄNGERTE Gerade"*
> (`fangKern.ts:147`), und `verlaengerung` ist eine **eigene Fangart** (F-041). Wer hier begrenzt,
> zerstört zwei von sieben Fangarten.
>
> **Für die Anwendung heißt das:** `t' = max(0, min(1, t))` gilt, wenn der Punkt **auf dem Bauteil**
> liegen muss — Öffnung auf Wandachse, Bemaßung, Flächenzuordnung. **Sie gilt NICHT, wenn die
> Verlängerung selbst das Ziel ist.** *Ohne diesen Satz liest die nächste Rolle einen Fehler, wo eine
> Entscheidung steht — und „korrigiert" den Fang kaputt.*
>
> **2 · Der Grenzfall prüft im Code auf ein EPSILON, nicht auf exakte Null — und das ist besser.**
>
> ```text
> Sammlung:  |B−A|² = 0        exakte Null
> Code:      laenge2 < 1e-9    Epsilon      (fangKern.ts:100)
> ```
>
> **In Gleitkomma-Arithmetik ist die exakte Prüfung die gefährlichere.** Eine fast-entartete Strecke
> (zwei Punkte 10⁻⁷ mm auseinander) besteht den Test `= 0` und liefert dann ein `t` in
> Millionenhöhe — der Fang springt ins Nichts. *Hier trägt der Code, nicht die Formel; die
> Formelfassung wird auf `< ε` nachgezogen.*

### F-004 · Schnittpunkt zweier Geraden
- **Zweck:** Wandachsen verschneiden, Ecke bilden
- **Eingabe:** Gerade 1 durch A,B · Gerade 2 durch C,D
- **Formel:**
  ```
  n = (Ax−Cx)(Dy−Cy) − (Ay−Cy)(Dx−Cx)
  m = (Bx−Ax)(Dy−Cy) − (By−Ay)(Dx−Cx)
  t = n / m              (m ≠ 0)
  S = A + t·(B−A)
  ```
- **Ausgabe:** Schnittpunkt S
- **Grenzfall:** `|m| < ε` → **parallel oder deckungsgleich**, kein Schnittpunkt.
  Das ist der häufigste Absturzgrund in Wandverschneidungen. Immer prüfen.

---

## Gruppe 2 — Polygone

### F-010 · Orientierung eines Polygons (Schuhbandformel)
- **Zweck:** Feststellen, ob ein Umriss im oder gegen den Uhrzeigersinn läuft
- **Eingabe:** Punktfolge P₀…Pₙ₋₁
- **Formel:** `2A = Σᵢ (xᵢ·yᵢ₊₁ − xᵢ₊₁·yᵢ)`   (Index modulo n)
- **Ausgabe:** `2A > 0` → gegen den Uhrzeigersinn · `2A < 0` → im Uhrzeigersinn
- **Grenzfall:** `2A ≈ 0` → entartetes Polygon (alle Punkte auf einer Geraden).
  **Der Straight-Skeleton-Algorithmus verlangt gegen den Uhrzeigersinn** — wer die
  Orientierung nicht prüft, bekommt ein nach innen gestülptes Dach.

### F-011 · Fläche eines Polygons
- **Zweck:** Raumfläche, Dachfläche, Grundfläche
- **Eingabe:** Punktfolge, geschlossen, nicht selbstschneidend
- **Formel:** `A = ½·|Σᵢ (xᵢ·yᵢ₊₁ − xᵢ₊₁·yᵢ)|`
- **Ausgabe:** Fläche in mm² (→ /1 000 000 für m²)
- **Grenzfall:** Selbstschneidendes Polygon liefert eine **falsche, aber plausible**
  Zahl — keine Fehlermeldung. Deshalb vorher F-013 laufen lassen.

### F-012 · Punkt in Polygon (Strahlenmethode)
- **Zweck:** Welchen Raum hat der Anwender angeklickt?
- **Eingabe:** Punkt P, Polygon
- **Formel:** Waagerechten Strahl von P nach +x legen, Kantenschnitte zählen.
  Ungerade Anzahl → innen, gerade → außen.
- **Ausgabe:** innen / außen
- **Grenzfall:** P liegt **genau auf** einer Kante oder der Strahl trifft genau
  einen Eckpunkt → Ergebnis abhängig von der Zählregel. Regel festlegen:
  untere Kante zählt, obere nicht.

### F-013 · Selbstschnitt-Prüfung
- **Zweck:** Vorprüfung vor jeder Flächen- oder Dachberechnung
- **Eingabe:** Punktfolge
- **Formel:** Jedes Kantenpaar, das nicht benachbart ist, auf Streckenschnitt prüfen
- **Ausgabe:** frei von Selbstschnitt / Fundstelle
- **Grenzfall:** Aufwand wächst quadratisch mit der Punktzahl. Bis ~200 Punkte
  unproblematisch; darüber Rasterbeschleunigung nötig.

---

## Gruppe 3 — Dach

### F-020 · Straight Skeleton (Grundgleichung)
- **Zweck:** **Der Kern der Dachkonstruktion.** Aus einem Grundriss die Firste,
  Grate und Kehlen erzeugen.
- **Eingabe:** Einfaches Polygon, gegen den Uhrzeigersinn, Löcher im Uhrzeigersinn
- **Idee:** Jede Kante wandert mit gleichmäßiger Geschwindigkeit nach innen.
  Die Eckpunkte laufen dabei auf den **Winkelhalbierenden**. Die Spur, die sie
  ziehen, ist das Skelett.
- **Formel (Kantenversatz zur Zeit t):**
  ```
  Kante als Gerade:  a·x + b·y + c = 0    mit a²+b² = 1
  Versetzte Kante:   a·x + b·y + c − t = 0
  Gewichtet:         w·(a·x + b·y + c) − t = 0
  ```
- **Ereignisse:**
  - **Kanten-Ereignis:** zwei wandernde Ecken treffen sich → eine Kante verschwindet
  - **Spalt-Ereignis:** eine wandernde Ecke trifft auf eine gegenüberliegende Kante
    → das Polygon **zerfällt in zwei**
- **Ausgabe:** Skelettbaum aus Kanten mit je einer Zeit t
- **Grenzfall:** Bei einwärts springenden Ecken (L- oder U-Grundriss) treten
  **Spalt-Ereignisse** auf. Wer nur Kanten-Ereignisse behandelt, bekommt bei einem
  rechteckigen Grundriss ein korrektes Dach und bei einem L-Grundriss **gar keins**.
  → Genau hier lag der Befund aus Auftrag Z-07.

### F-021 · Dach aus Skelett anheben
- **Zweck:** Aus dem flachen Skelett wird ein räumliches Dach
- **Eingabe:** Skelettknoten mit Zeit t, Dachneigung α
- **Formel:** `z(Knoten) = t · tan(α)`
- **Ausgabe:** Dreiecksnetz der Dachflächen
- **Grenzfall:** α = 90° → `tan` läuft gegen unendlich, Absage. α = 0° → Flachdach,
  z bleibt 0, das Skelett wird bedeutungslos (Sonderweg).
- **Physikalische Probe:** Wasser muss von jedem Punkt der Dachfläche **immer**
  zur Traufe fließen. Wenn nicht, ist die Ableitung falsch.

### F-022 · Dachneigung umrechnen
- **Zweck:** Anwender denkt in Grad oder Prozent, Rechnung braucht Radiant
- **Formeln:**
  ```
  Prozent → Grad:      α = arctan(p / 100)
  Grad → Prozent:      p = tan(α) · 100
  Sparrenlänge:        s = h / sin(α)   =   b / cos(α)
  Firsthöhe (Sattel):  h = (b/2) · tan(α)      b = Gebäudebreite
  ```
- **Grenzfall:** 45° = 100 %, **nicht** 50 %. Das ist der häufigste Denkfehler
  bei Dachneigungen.

### F-023 · Wahre Dachfläche aus Grundfläche
- **Zweck:** Materialmenge, PV-Belegung
- **Formel:** `A_Dach = A_Grundriss / cos(α)`
- **Grenzfall:** α → 90° → cos → 0 → Fläche → unendlich. Über 85° absagen.

### F-024 · Ausrichtung einer Dachfläche (Azimut)
- **Zweck:** PV-Ertrag, Verschattung
- **Eingabe:** Normalenvektor n=(nx,ny,nz) der Dachfläche
- **Formel:**
  ```
  Azimut  γ = atan2(nx, ny)        0° = Nord, 90° = Ost, 180° = Süd
  Neigung α = arccos(nz / |n|)
  ```
- **Grenzfall:** Bei einem Flachdach (n ≈ (0,0,1)) ist der Azimut **undefiniert** —
  nicht 0° melden, sondern „keine Ausrichtung".

---

## Gruppe 4 — Körper

### F-030 · Wand aus Achse extrudieren
- **Zweck:** Aus Strecke + Stärke + Höhe wird ein Quader
- **Eingabe:** Achse A→B, Stärke d, Höhe h, Höhenlage z₀
- **Formel:**
  ```
  Richtung  r = (B−A)/|B−A|
  Normale   n = (−r_y, r_x)          ← 90° gedreht
  Vier Grundpunkte:  A ± (d/2)·n ,  B ± (d/2)·n
  Extrusion in z:    von z₀ bis z₀+h
  ```
- **Grenzfall:** Bei d ≤ 0 oder h ≤ 0 Absage. Bei sehr spitzen Wandwinkeln
  (< 15°) überlappen sich die Ecken — dann verschneiden (F-004), nicht stumpf stoßen.

### F-031 · Öffnung ausschneiden (CSG-Differenz)
- **Zweck:** Fenster und Tür durch die Wand
- **Eingabe:** Wandkörper, Öffnungsquader (Breite, Höhe, Brüstung, Position)
- **Formel:** `Ergebnis = Wand ∖ Öffnung` (boolesche Differenz)
- **Grenzfall:** Öffnung breiter/höher als die Wand → **lesbare Absage**, nicht
  „Wand verschwindet". Öffnung exakt bündig mit dem Wandrand → CSG erzeugt
  entartete Flächen; deshalb die Öffnung um ε (0,5 mm) über die Wand hinausziehen.

### F-032 · Transformation eines Punktes
- **Formel:** homogene 4×4-Matrix, `P' = M · P` mit P=(x,y,z,1)
  ```
  Verschieben  T(tx,ty,tz)
  Drehen um z  R_z(θ)
  Skalieren    S(sx,sy,sz)
  Verkettung:  M = T · R · S        ← Reihenfolge zählt!
  ```
- **Grenzfall:** Matrixmultiplikation ist **nicht** kommutativ. Erst drehen, dann
  verschieben ist etwas anderes als umgekehrt. Reihenfolge im Code festschreiben.

---

## Gruppe 5 — Fang und Raster

### F-040 · Rasterfang
- **Formel:** `x' = runde(x / g) · g` (g = Rasterweite)
- **Grenzfall:** Bei negativen Koordinaten muss `runde` kaufmännisch runden,
  nicht abschneiden — sonst ist das Raster links der Null um eine Zelle verschoben.

### F-041 · Fangkandidat wählen
- **Zweck:** Mehrere Fangpunkte in Reichweite — welcher gewinnt?
- **Regel:** feste Rangfolge, nicht „der nächste gewinnt":
  ```
  1. Endpunkt        (stärkste Bindung)
  2. Schnittpunkt
  3. Mittelpunkt
  4. Lot
  5. Verlängerung
  6. Raster          (schwächste)
  ```
  Innerhalb desselben Rangs gewinnt der nächstliegende.
- **Grenzfall:** Ohne Rangfolge springt der Fang bei dichten Objekten unvorhersehbar —
  der häufigste Grund, warum Anwender einen Planer als „zickig" empfinden.

> ### Am Bestand nachgezogen (10.08.) — die Ordnung stimmt, zwei Arten weichen ab
>
> **Der Code implementiert die Rangfolge als Früh-Return-Kette** in `fange()`
> (`fangKern.ts:108-175`, Blöcke `// 1)` bis `// 5)`), nicht über eine Tabelle:
>
> ```text
> Sammlung  Endpunkt > Schnittpunkt > Mittelpunkt > Lot        > Verlaengerung > Raster
> Code      endpunkt >               mittelpunkt > achse(=Lot) > verlaengerung > ortho > raster
>                                                                                       > keiner
> ```
>
> **Die relative Ordnung ist identisch** — Mittelpunkt steht in beiden **vor** dem Lot. Genau zwei
> Arten weichen ab:
>
> ```text
> Schnittpunkt   in der Sammlung Rang 2, im Code NICHT VORHANDEN.
>                `FangArt` kennt ihn nicht (fangKern.ts:26-33). Er haengt an F-004,
>                das im Code ebenfalls fehlt — es ist EIN Befund, nicht zwei.
> ortho          im Code Rang 5 (0/90° durch den Referenzpunkt), in der Sammlung
>                NICHT GENANNT. Eine gebaute und benannte Fangart ohne Formelplatz.
> ```
>
> **Begriffsabgleich, damit niemand zwei Dinge für eines nimmt:** was die Sammlung *„Lot"* nennt,
> heißt im Code `achse` — und es ist das **Lot auf die verlängerte Gerade**, nicht auf die Strecke.
> *Damit hängt dieser Eintrag direkt an der `[0,1]`-Frage in F-003; beide beschreiben dieselbe
> Entscheidung von zwei Seiten.*
>
> **OFFENE FACHFRAGE — hier ausdrücklich NICHT entschieden:**
>
> ```text
> a) Soll `Schnittpunkt` gebaut werden (dann bleibt Rang 2 als SOLL stehen)
>    oder aus der Rangfolge gestrichen (dann ist die Sammlung ein IST)?
> b) Wo gehoert `ortho` in die Rangfolge — im Code steht es NACH der Verlaengerung.
>    Ist das die gewollte Ergonomie?
> ```
>
> *Beides ist Fang-Ergonomie und keine Buchführung: die Rangfolge entscheidet, wie sich das Zeichnen
> **anfühlt**. Der Planner legt das nicht fest — er stellt fest, dass Sammlung und Code
> auseinandergehen, und legt die Frage vor. **Bis zur Entscheidung gilt der Code**, weil er läuft.*

---

# NACHTRAG — aus Yamas eigenem Bestand

> ## ⚠ AMPELN — Stand der Qualitätsprüfung (07.08.2026)
>
> | F-Nr | Ampel | Bedingung für die Nutzung |
> |---|---|---|
> | F-014, F-025, F-027 | 🟢 | keine — Mathematik nachvollzogen |
> | **F-026** | 🟢 | **ausgeführt 11.08.** (A-12, `docs/BERICHT-A-12-f026.md`, doppelt abgenommen). Es kam ein L-Dach mit vier benannten Flächen heraus. **Grün gilt für die PARAMETERGEOMETRIE, nicht für die Kantentopologie-Beschreibung unten** — siehe Berichtigung am Formelblatt |
> | **F-050** | 🟡 | **nur als Näherung.** Nicht für Angebote — 12 Stück/m² ist modellabhängig |
> | **F-051** | 🔴 | **GESPERRT.** Zeitwerte ohne jede Herkunft. Nicht verwenden |
> | **F-028** | 🔴 | **GESPERRT fuer das DURCHREICHEN.** Ein Azimutwert 0…180 ist in ZWEI Konventionen gueltig und bedeutet Entgegengesetztes. Ohne mitgelieferte Konvention und ohne Umrechnung an der Systemgrenze darf kein Azimut weitergegeben werden |
>
> Prüfraster und Begründung: `../05-MATERIALQUELLEN/VORGEHEN.md`

> Quelle: `~/Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d/dachdecker_pro_3d.tsx`
> (2.173 Zeilen, Funktion `analyzeTopology`, Zeilen 134–171).
> **Dieser Code läuft und kann bereits, was der Straight-Skeleton-Weg noch nicht kann.**

## Gruppe 6 — Kantentopologie (der zweite Dachweg)

### F-014 · Innenwinkel an einer Polygonecke — mit Erkennung einspringender Ecken
- **Zweck:** Feststellen, ob eine Ecke nach außen oder nach innen springt.
  **Das ist die Erkennung, an der W-07 bisher scheiterte.**
- **Eingabe:** Ecke P, Vorgänger V, Nachfolger N, Orientierung des Polygons (F-010)
- **Formel:**
  ```
  v₁ = V − P ,  v₂ = N − P          (auf Länge 1 normieren)
  Basiswinkel  β = arccos(v̂₁ · v̂₂) · 180/π
  Kreuzprodukt z = v̂₁ₓ·v̂₂ᵧ − v̂₁ᵧ·v̂₂ₓ
  einspringend, wenn:   CCW ? z > 0 : z < 0
  Innenwinkel  α = einspringend ? 360 − β : β
  Eckentyp:    α > 180° → innen   ·   sonst → außen
  ```
- **Ausgabe:** Winkel in Grad, Eckentyp `innen | aussen`
- **Grenzfall:** Länge 0 → im Quellcode auf 1 gesetzt (`|| 1`), damit nicht durch
  null geteilt wird. Sauberer wäre eine Absage — eine Ecke mit Länge 0 ist keine Ecke.
  `arccos` muss auf [−1, 1] geklemmt werden, sonst liefert Rundung `NaN`.
- **Belegstelle:** `dachdecker_pro_3d.tsx:139–147`

### F-025 · Verbindungstyp einer Ecke (Grat · Kehle · Ortgang)
- **Zweck:** Was entsteht an der Ecke — ein Grat, eine Kehle oder ein Ortgang?
- **Eingabe:** Eckentyp aus F-014, Kantentyp der beiden anliegenden Kanten
- **Kantentypen:** `TRAUFE · GIEBEL · PULT_WAND · WALM · TEILWALM`
- **Regel:**
  ```
  beide Kanten traufartig (TRAUFE|WALM|TEILWALM):
        innen  → KEHLE
        außen  → GRAT
  eine traufartig, eine GIEBEL      → ORTGANG
  sonst                             → neutral
  ```
- **Ausgabe:** `grat | kehle | ortgang | neutral` je Ecke
- **Grenzfall:** Eine Kehle entsteht **nur** an einspringenden Ecken. Ein Grundriss
  ohne einspringende Ecke hat keine Kehle — wenn doch eine gemeldet wird, ist F-014 falsch.
- **Belegstelle:** `dachdecker_pro_3d.tsx:153–158`

### F-026 · Dach über vorgegebene Grundform (Alternative zu F-020) · 🟢

> **⚠ BERICHTIGUNG 11.08. — das Verfahren unten beschreibt NICHT, was läuft.** *Eingetragen vom
> Planner nach A-12 (§A-12-4: Generator schlägt vor, Evaluator bestätigt, Planner trägt ein), und
> die Berichtigung ist die **Bedingung** des Grüns — nicht ein Zusatz dazu.*
>
> **Selbst am Fremdcode nachgemessen** (`~/Desktop/Gemini-Code-Ideen-2026-05-25/03-energie-pv-dach-3d/dachdecker_pro_3d.tsx`, 2173 Z / 132374 B):
>
> ```text
> ZWEI GETRENNTE WELTEN, und sie beruehren sich nirgends:
>
> WELT 1  die Kantentopologie-Kette — das Verfahren unten
>   :95   buildTopologyPolygon()
>   :123  getDefaultEdgeTopologyConfigs()
>   :134  analyzeTopology()          -> liefert grate / kehlen als ZAHLEN
>   Aufrufe: NUR :1496 (useState) und :1706 (useMemo) — beide in der REACT-Schicht.
>            analyzeTopology speist eine ANZEIGE, keine Geometrie.
>
> WELT 2  was tatsaechlich gebaut wird
>   :774  buildCompoundPitchedFaces()
>   :965  buildCompoundPitched()
>   :1137 der einzige Aufruf fuer l-shape / t-shape
>   Messung: sed -n '774,971p' | grep -E 'EdgeTopology|analyzeTopolog|joinType|grate|kehlen'
>            -> LEER. NULL Aufrufe der Kette aus Welt 1.
> ```
>
> **Was läuft, ist fest verdrahtete Parametergeometrie für L und T** — keine Kantentypisierung,
> keine Eckenklassifikation, kein Flächenaufbau aus typisierten Kanten.
>
> **Und die zwei Welten widersprechen sich in einer Zahl.** *Schritt 3 unten sagt „Vorgabe je
> Grundform" und listet `sattel`, `pult`, `walm`, `flach`.* **`l-shape` und `t-shape` fehlen in
> dieser Liste — im Formelblatt genauso wie im Code.** Sie fallen in
> `getDefaultEdgeTopologyConfigs` auf die letzte Zeile (`:131`) durch: **alle Kanten `TRAUFE`.**
> Daraus rechnet `analyzeTopology` bei einem L-Grundriss (6 Ecken, davon 1 einspringend) über
> `:157` **`grate=5`, `kehlen=1`**. Gebaut wird aber:
>
> ```text
> :861  'Kehlsparren Links'    if (u_L > 0)
> :866  'Kehlsparren Rechts'   if (type === 'T' && u_R < uMaxMain)
> :868  'Gratsparren Rechts'   ELSE  <- der L-Fall nimmt IMMER diesen Zweig
> -> bei einem L:  1 Kehle, 1 Grat.   Die Anzeige sagt 5 Grate.
> ```
>
> *Die Zahlen `grate=5/kehlen=1` hat der Evaluator gemessen; ich habe die Logik gegengelesen, die
> dazu führt, und die drei Bauzeilen selbst am Code belegt.* **Für F-026 heißt das: genau der
> Zweig, der F-026 gegenüber F-020 auszeichnet (L und T), ist der Zweig, für den kein
> Kantentyp-Schema existiert.**
>
> **Was das Grün deshalb erlaubt und was nicht:**
>
> ```text
> ERLAUBT      F-026 als PARAMETERGEOMETRIE fuer L und T zu benutzen und zu begruenden.
>              Das ist gerechnet, gesehen, doppelt abgenommen — vier benannte Flaechen
>              (main_N/main_S/ext_W/ext_E), zwei Firstlinien, 1 Kehle, 1 Grat, 7 Pfetten.
> NICHT        das sechsschrittige Verfahren unten zu ZITIEREN, als waere es der Weg.
> ERLAUBT      Wer Schritt 3 und 5 fuer L/T zitiert, zitiert eine Anzeige-Kette, die
>              nachweislich nicht gelaufen ist und eine andere Zahl liefert als der Bau.
> UNBERUEHRT   F-014 und F-025 (die Ecken-/Kantenformeln selbst) bleiben 🟢 — sie sind
>              richtig, sie werden von l-shape nur nicht ERREICHT.
> ```
>
> *Ohne diese Berichtigung würde 🟢 genau die Zitierung erlauben, die die 🟡-Sperre verhindern
> sollte. Das ist der Grund, warum der Evaluator sein Grün ausdrücklich daran gebunden hat.*
- **Zweck:** Dach erzeugen, **ohne** allgemeines Straight Skeleton
- **Verfahren:**
  ```
  1. Grundform wählen:  rechteck | l-shape | t-shape | pult | walm | sattel | flach
  2. Konturpunkte aus Länge L, Breite W, Schenkelbreite WB, Schenkellänge LB erzeugen
  3. Jeder Kante einen Typ zuweisen (Vorgabe je Grundform):
        sattel  → [TRAUFE, GIEBEL, TRAUFE, GIEBEL]
        pult    → [TRAUFE, GIEBEL, PULT_WAND, GIEBEL]
        walm    → alle TRAUFE
        flach   → alle TRAUFE
  4. Je Kante eine eigene Neigung (GIEBEL bekommt 0)
  5. Ecken über F-014/F-025 klassifizieren
  6. Flächen aus den typisierten Kanten aufbauen
  ```
- **Ausgabe:** Dachflächen mit Grat-/Kehl-/Ortganglinien
- **Belegstelle:** `dachdecker_pro_3d.tsx:101–131` (Konturen) · `:1134–1137` (Aufbau,
  `buildCompoundPitched` für L- und T-Form)

> ### Der Vergleich der beiden Wege — das ist die Entscheidung
>
> | | **F-020 Straight Skeleton** | **F-026 Kantentopologie** |
> |---|---|---|
> | Eingabe | **beliebiges** Polygon | Grundform aus einer festen Liste |
> | L-/T-Grundriss | **braucht Spalt-Ereignisse — noch nicht gebaut** | **läuft bereits** (`buildCompoundPitched`) |
> | Freie Grundrisse (7-eckig, schief) | ja, sobald gebaut | **nein** — nur die vorgegebenen Formen |
> | Verschiedene Neigung je Seite | nur mit Gewichten | **von Haus aus** (Neigung je Kante) |
> | Grat/Kehle/Ortgang benannt | muss abgeleitet werden | **fertig benannt** |
> | Mathematische Tiefe | hoch | gering |
> | Aufwand bis lauffähig | groß | **Code liegt vor** |
>
> **⚠ ZWEI ZEILEN DIESER TABELLE SIND NACH A-12 ANDERS ZU LESEN (Planner 11.08.):**
>
> ```text
> "laeuft bereits (buildCompoundPitched)"    STIMMT — aber es laeuft als
>                                            PARAMETERGEOMETRIE, nicht als
>                                            Kantentopologie. Die Spaltenueberschrift
>                                            "F-026 Kantentopologie" trifft fuer den
>                                            L-/T-Zweig nicht zu.
> "Grat/Kehle/Ortgang fertig benannt"        STIMMT fuer die Bauteile ('Kehlsparren
>                                            Links', 'Gratsparren Rechts' als Namen
>                                            im Modell), NICHT fuer die
>                                            Ecken-Klassifikation: die laeuft fuer
>                                            l-shape/t-shape nicht, und ihre Zahl
>                                            (grate=5) widerspricht dem Bau (1).
> "Verschiedene Neigung je Seite von Haus    UNGEPRUEFT fuer L/T. Der Mechanismus
>  aus (Neigung je Kante)"                   dafuer sitzt in getDefaultEdgeTopology-
>                                            Configs (pitch je Kante) — also in
>                                            Welt 1, die fuer L/T nicht laeuft.
>                                            Nicht widerlegt, nur nicht belegt.
> ```
>
> *Die dritte Zeile nenne ich ausdrücklich als **ungeprüft** statt sie zu berichtigen — A-12 hat sie
> nicht gemessen, und eine Berichtigung ohne Messung wäre derselbe Fehler in der anderen Richtung.*
>
> **Befund:** Der Fall, an dem Auftrag Z-07 scheiterte — ein L-förmiges Dach —
> ist auf Yamas eigenem Schreibtisch bereits gelöst. Nicht mit dem Verfahren,
> das ich recherchiert habe, sondern mit einem einfacheren, das weniger kann,
> aber den gebrauchten Fall abdeckt.
>
> ~~**Empfehlung: F-026 zuerst bauen** … **F-020 später** für freie Grundrisse. Beide können
> nebeneinander bestehen …~~
>
> ### ENTSCHIEDEN 11.08. — die Wahl ist keine Wahl mehr, sie ist gebaut
>
> **Yamas Freigabe 11.08. auf die Vorlage `docs/VORLAGE-DACHWEG-ENTSCHEIDUNG.md`.** *Die Empfehlung
> oben stellte zwei Wege gegenüber, als stünde die Entscheidung noch aus. **Sie stand nicht mehr
> aus — sie war durch drei Bauten längst gefallen, nur nie getroffen worden.***
>
> ```text
> GEMESSEN 11.08. (Planner, am Code, nicht aus einem Bericht):
>
> F-020 STRAIGHT SKELETON
>   grep -rliE 'straight.?skeleton|skelett' resources/ app/     -> 0 Treffer
>   nicht gebaut, nicht angefangen, kein Modul, keine Zusage.
>
> F-026-WEG — vier Module, 2.882 Zeilen, 105 Exporte, 7 Zusagen, PRODUKTIV VERDRAHTET
>   geometry/dachVerschneidung.ts    205 Z   L/T-Kehl-/Gratlinien, byte-treu
>   geometry/dachUForm.ts            126 Z   U-Form
>   geometry/dachformVorlagen.ts   2.399 Z   Formenkatalog + Validierung
>   geometry/schifterListe.ts        152 Z   Schifter (jack rafters)
>   renderers/three-d/dachMesh.ts:17  importiert verschneidungsFlaechen + lTBauGueltig
>
> UEBER DIE QUELLE HINAUSGEWACHSEN
>   buildCompoundPitchedU    Fremdcode 0 Treffer  ·  Insel 2 Treffer
>   Dachformen               F-026s Liste 7       ·  Insel 9 (+ u-shape, mansard)
>   -> die U-Form hat die Insel SELBST gebaut. F-026 kann sie nicht.
> ```
>
> **Die neue Lage, und sie ersetzt die Vergleichstabelle als Entscheidungsgrundlage:**
>
> | | **F-020 Straight Skeleton** | **F-026-Weg (Parametergeometrie)** |
> |---|---|---|
> | Zustand | **RESERVE** — nicht gebaut | **DER WEG** — gebaut, verdrahtet, zugesagt |
> | Wofür | **ein freier Grundriss, der in keine Formvorlage passt** | alle 9 Formen des Katalogs |
> | Wann anfassen | **erst wenn dieser Fall real auftritt** | jetzt und weiter |
> | Im Register | als Reserve führen, **nicht als offene Wahl** | als gegangener Weg |
>
> **F-020 wird NICHT verworfen und NICHT gelöscht.** *Die Recherche ist gültige Arbeit und im Moment,
> in dem ein Kunde einen freien Grundriss bringt, die einzige Antwort. **Aber sie ist eine Reserve
> für einen benannten Fall, keine gleichrangige Option** — eine Vergleichstabelle, die eine nie
> gebaute Alternative als gleichrangig führt, lädt jeden nächsten Auftrag ein, die Wahl neu
> aufzumachen, und das kostet jedes Mal eine Runde.*
>
> **Die Grenze von F-026 bleibt bestehen und ist im eigenen Katalog belegt:** nur vorgegebene Formen,
> und **eine** von zwölf steht auf `status: 'geplant'` statt `'verfuegbar'`. *Das ist der Beleg, dass
> die Grenze real ist — und gleichzeitig, dass die Insel sie ehrlich anzeigt.*

### F-028 · Azimut-Konvention an der Systemgrenze · 🔴

> **Aufgenommen 12.08. auf Yamas ausdrückliche Auflage.** *Herkunft: `docs/BEFUND-AZIMUT-KONVENTION.md`
> (`3d368625`), Planner-Messung, von Yama gegengelesen und in zwei Punkten bestätigt.*
> **Das ist keine Rechenformel, sondern eine Sperre — sie steht hier, weil hier nachgeschlagen wird.**

- **Zweck:** verhindern, dass ein Azimutwert zwischen zwei Konventionen durchgereicht wird
- **Die zwei Konventionen, beide dokumentiert, beide richtig:**

```text
KOMPASS (Hausstandard)   0 = Nord · 90 = Ost · 180 = Sued · 270 = West · Bereich 0..360
  create_p_v_roofs_table.php:67       "// 0=N, 90=E, 180=S, 270=W"
  create_heizlast_bauteile_table:22   "// 0=N,90=O,180=S,270=W"
  SzeneProjektionService.php:257      "Nord = +y = 0 Grad, Ost = 90 Grad"
  CanonicalBuildingModelValidator:24  "0 <= azimut < 360 (0=Nord)"
  szene.ts:60                         "Kompass-Azimut der Sonne: 180 Grad = Sued"
  ZUGESAGT: BuildingModelSchemaContractTest:53 · SzeneProjektionServiceTest:80
            assertSame([0, 90, 180, 270]) · GeometrieAbleitungReferenzTest:84 (Sued=180)

PVGIS (Fremdgrenze)      0 = Sued · -90 = Ost · +90 = West · Bereich -180..+180
  PvgisErtragService.php:41           "@param float $aspect … 0 = Sued, -90 = Ost, 90 = West"
  InverterSizingService.php:69        "(Sued = 0 Grad, Konvention PVGIS)"
  energiekonzept.blade.php:394        <input min="-180" max="180">
  pvgis/index.blade.php:128           <input min="-180" max="180">
```

- **Warum 🔴 und nicht 🟡 — der Bereich, der NICHT auffällt:**

```text
270      im PVGIS-Feld nicht eingebbar (max 180)   -> faellt auf
-90      im Kompass ungueltig (< 0)                -> faellt auf
0..180   in BEIDEN gueltig, bedeutet das GEGENTEIL -> FAELLT NICHT AUF
   0     Kompass NORD  ·  PVGIS SUED     180 Grad Fehler
  90     Kompass OST   ·  PVGIS WEST     180 Grad Fehler
 180     Kompass SUED  ·  PVGIS (Nord)   180 Grad Fehler
```

> **Ein Süddach trägt im Kompass `180`. Unverändert an PVGIS gegeben rechnet PVGIS ein Norddach —
> der größtmögliche Ertragsfehler, und nichts schlägt an, weil `180` in beiden Systemen eine gültige
> Zahl ist.** *Genau die Werte, die deutsche Dächer am häufigsten haben, liegen im doppelsinnigen
> Bereich.* **Das ist die A-10-Klasse — stilles Nichts, nur nicht als leeres Ergebnis, sondern als
> plausible falsche Zahl.**

- **Was die rote Ampel sperrt und was sie NICHT sperrt:**

```text
GESPERRT      einen Azimutwert von einem System ins andere geben, ohne Umrechnung.
              Gemessen: grep '\+ *180|- *180' app/Services/Energie/*.php -> 0 Treffer.
              Es GIBT heute keine Umrechnung im Haus.
GESPERRT      einen Azimut annehmen, dessen Konvention nicht mitgeliefert ist.
NICHT         die PVGIS-Konvention. Sie ist korrekt benannt und bleibt wie sie ist —
GESPERRT      eine fremde API-Konvention anzupassen waere der Fehler.
NICHT         die Kompass-Konvention im Haus. Sie ist Standard, fuenffach dokumentiert
GESPERRT      und dreifach zugesagt.
NICHT         das Rechnen INNERHALB einer Konvention.
GESPERRT
```

- **Der Ableitungsmechanismus existiert und ist zweisprachig konsistent — nicht neu bauen:**

```text
wallGeometry.ts:37              azimutDerNormalen(start, end, seite) -> 0..359, Nord=0
SzeneProjektionService.php:258  azimutRechteNormale($von, $bis)
BEIDE rechnen atan2(dy, -dx) fuer die rechte Normale — selbst nachgemessen, identisch.
```

- **Und die Falle daneben, 90° statt 180°:**

```text
scene.types.ts:325  firstAzimutGrad = FIRST-Richtung, NICHT der Flaechenazimut
scene.types.ts:280  "Die Flaechen-Azimute werden NIE gepflegt, sondern [abgeleitet]"
-> der First laeuft ENTLANG des Dachs, die Flaeche schaut SENKRECHT dazu.
   Wer die Firstrichtung als Dachausrichtung nimmt, hat 90 Grad Fehler ZUSAETZLICH.
   ABLEITEN, NICHT DURCHREICHEN.
```

- **Was die Ampel auf 🟡 senken würde:** eine Umrechnungsfunktion an der Grenze mit **zwei
  Zusagen** (Süd→0 in beiden Richtungen, Ost→−90) **plus** der Ableitung First→Fläche.
  *Auf 🟢 erst, wenn zusätzlich belegt ist, welche Konvention die **vorhandenen** Zeilen in
  `p_v_roofs.roof_azimuth` tragen — diese Messung fährt **Yama**, nicht die Kette
  (Produktivdaten).*
- **Herkunft:** `docs/BEFUND-AZIMUT-KONVENTION.md` · Yamas Auflage vom 11.08.

## Gruppe N — NORMATIVE Größen (eigenes Präfix, eigene Regeln)

> **Angelegt 12.08. vom Planner.** *Anlass: der Generator hat bei W-21 gemeldet, dass
> `bodenschneelast` und `formbeiwertSchnee` rechnen, aber keine F-Nummer haben — und geschrieben,
> „die Sammlung kennt sie zu Recht nicht". **Er hat recht, dass sie keine F-Nummer sind. Er hat
> nicht recht, dass sie fehlen dürfen.***

**Warum ein eigenes Präfix und nicht F-052:**

```text
Eine GEOMETRIEFORMEL   ist zeitlos. Der Satz des Pythagoras hat keine Fassung.
Eine NORMATIVE GROESSE hat eine FASSUNG, einen Nationalen Anhang und einen Geltungsbereich.
                       Sie kann sich aendern, ohne dass die Mathematik falsch wird.
-> Deshalb traegt jede N-Zeile PFLICHTFELDER, die F-Zeilen nicht haben:
   Norm · Fassung/Anhang · Geltungsbereich · Wer darf sich darauf verlassen
```

> **Und der Grund, warum sie nicht einfach fehlen dürfen:** *Ohne Nummer kann kein Werkzeugblatt auf
> sie verweisen. W-21 rechnet mit ihnen, und seine Registerzeile trug bis heute zwei **falsche**
> F-Nummern (F-001, F-030 — beide mit 0 Treffern in allen fünf Modulen, selbst gegengemessen).*
> **Eine leere Stelle im Register wird mit der nächstbesten Zahl gefüllt. Das ist genau passiert.**

### N-001 · Charakteristische Bodenschneelast sₖ · 🟢

- **Norm:** DIN EN 1991-1-3 + **Nationaler Anhang** (Zone + Geländehöhe)
- **Belegstelle:** `geometry/sparrenBerechnung.ts:33`, `bodenschneelast(zone, gelaendehoeheM)`
- **Formel im Code, selbst gelesen:**

```text
t  = (A + 140) / 760            A = Gelaendehoehe in m, auf >= 0 begrenzt
Zone 1:  sk = 0,19 + 0,91·t²    Mindestwert 0,65 kN/m²
Zone 2:  sk = 0,25 + 1,91·t²    Mindestwert 0,85 kN/m²
Zone 3:  sk = 0,31 + 2,91·t²    Mindestwert 1,10 kN/m²
Rueckgabe: max(Mindestwert, gerechneter Wert)
```

- **Ausgabe:** sₖ in kN/m²
- **Grenzfall:** Negative Geländehöhe wird auf 0 gesetzt (`Math.max(0, …)`) — **kein Wurf, keine
  Meldung**. *Das ist zulässig, weil eine negative Höhe über NN in Deutschland kein Anwendungsfall
  ist; **wer sie übergibt, hat einen Eingabefehler und bekommt still den Wert für 0 m**.*
- **🟢 weil belegt:** *Norm namentlich, Zonenformel vollständig, Mindestwerte je Zone, Herkunft im
  Dateikopf.* **Das ist mehr Beleg als F-050 (🟡 „plausibel, aber unbelegt") und deutlich mehr als
  F-051 (🔴 „ohne jede Herkunft") haben.**

### N-002 · Formbeiwert μ₁ für Schneelast · 🟢

- **Norm:** DIN EN 1991-1-3 (Pult- und Satteldach)
- **Belegstelle:** `geometry/sparrenBerechnung.ts:45`, `formbeiwertSchnee(neigungGrad)`
- **Formel im Code:**

```text
α <= 30°   ->  μ₁ = 0,8
α >= 60°   ->  μ₁ = 0
sonst      ->  μ₁ = 0,8 · (60 − α) / 30      (linear)
```

- **Grenzfall:** **Bei α ≥ 60° ist μ₁ = 0 — die Schneelast verschwindet vollständig.** *Das ist
  normgerecht (Schnee rutscht ab), aber es ist die Stelle, an der eine Vorbemessung optimistisch
  wird: ein Dach mit 60° trägt nach dieser Rechnung **keinen** Schnee.* **Wer ein steiles Dach
  bemisst, muss wissen, dass die Last hier nicht klein, sondern null wird.**

### N-003 · Sparren-Vorbemessung (Biegung + Durchbiegung) · 🟡 FACH-GATE

- **Norm:** DIN EN 1995-1-1 (Eurocode 5) — Biegenachweis σₘ,d ≤ fₘ,d und Durchbiegung
- **Belegstelle:** `geometry/sparrenBerechnung.ts:86`, `berechneSparren(e)`
- **Beiwerte im Code, selbst gelesen:**

```text
GAMMA_G = 1,35   GAMMA_Q = 1,5   GAMMA_M = 1,3   KMOD = 0,9
                 Kommentar: "Vollholz, NKL 1/2, mittelfristige Einwirkung Schnee"
DURCHBIEGUNG_GRENZE = 300        L/300, "Empfehlung Endzustand, Vorbemessung"
```

- **Geltungsbereich — wörtlich aus dem Dateikopf, und das ist der Grund für 🟡:**

```text
Einfeldtraeger · gleichmaessige Last · NUR die senkrechte Lastkomponente
NICHT enthalten: Wind · Mehrfeld · Knicken · Auflagerpressung · Lastkombinationen
-> "ersetzt KEINE prueffaehige Statik"
```

> **🟡 ist hier keine Aussage über die Rechenqualität — die ist belegt. Es ist eine
> Reichweitengrenze mit Haftungsbezug.** *Eine Sparrenbemessung, die als „geprüft" gilt und dann
> nicht trägt, ist Personenschaden. Deshalb steht sie als **Fach-Gate**: benutzbar für Vorbemessung,
> Angebot und Machbarkeit — **nicht** als Nachweis für die Ausführung ohne Tragwerksplaner.*
>
> **Diese Ampel setze ich in der STRENGEREN Lesart, und sie braucht Yamas Bestätigung.** *Statik ist
> Fachrecht; CLAUDE.md verlangt, dass Fachentscheidungen nicht still automatisiert werden. Bis Yama
> bestätigt oder lockert, gilt die strengere Fassung — **das ist die einzige Richtung, in der ein
> Irrtum niemandem schadet.***

### N-003 · Geltungsbereich — von Yama festgelegt 12.08., DAUERGELB

> **Yamas Bestätigung liegt vor, in der strengeren Fassung und ohne Einschränkung.** *Er hat den
> Geltungsbereich vorher selbst im Wortlaut gelesen.* **Und er hat meine 🟢-Zeile aufgehoben:**

```text
ERLAUBT — N-003 darf benutzt werden fuer:
  Vorbemessung im Entwurf     Querschnittsvorschlag, Achsabstand, Machbarkeit
  Angebot und Kalkulation     Holzmengen, Laengen, Kosten
  Plausibilitaet              "passt die Groessenordnung ueberhaupt"
  Variantenvergleich          Sparrendach gegen Pfettendach, 60 gegen 80 cm Achsmass

NICHT ERLAUBT — N-003 darf NIEMALS benutzt werden fuer:
  Ausfuehrungsnachweis        kein Ersatz fuer die Statik des Tragwerksplaners
  Genehmigungsunterlage       nicht in einen Bauantrag, nicht in eine Prueferklaerung
  Freigabe zur Ausfuehrung    kein "gerechnet, also gebaut"
  Sonderlasten                Wind, Schnee-Verwehung, Mehrfeld, Knicken,
                              Auflagerpressung, Lastkombinationen
```

> **DAUERGELB — 🟢 ist für diese Formel nicht vorgesehen.** *Yamas Korrektur meiner Fassung, wörtlich:
> „🟢 würde bedeuten: uneingeschränkt benutzbar. Das kann N-003 nie werden — nicht wegen der
> Rechenqualität, sondern weil der Geltungsbereich **fachlich unvollständig ist und bleibt**. Wind
> fehlt, Mehrfeld fehlt, Knicken fehlt. **Das ist kein Mangel, den man wegarbeitet; das ist die Natur
> einer Vorbemessung.**"*
>
> **Ich hatte geschrieben, eine Freigabe würde die Ampel auf grün heben. Das war falsch** — eine
> Freigabe *über den Geltungsbereich* macht die Grenze nicht kleiner, sie macht sie **verbindlich**.
> *Gelb heißt in diesem Regelwerk genau das Richtige: benutzbar **mit** der genannten Bedingung.*

### N-003 · AUFLAGE an die Ausgabe — keine stille Zahl

**Yamas Auflage, wörtlich:** *„Jede ausgegebene Bemessungszahl aus N-003 trägt ihren Vorbehalt mit —
in der Oberfläche, im Export, in der Stückliste, im PDF. Wer die Zahl sieht, sieht den Satz
‚Vorbemessung, ersetzt keine prüffähige Statik'. Nicht als Fußnote in den Einstellungen, sondern **am
Wert**."*

**Der Anschlusspunkt ist gemessen, nicht vermutet:**

```text
DIE ZAHL ENTSTEHT      geometry/sparrenBerechnung.ts:86   berechneSparren(e)
SIE WIRD GERUFEN VON   app/dashboard/enginePanels.ts:210  (die EINZIGE Aufrufstelle
                       ausserhalb der Tests)
SIE WIRD ANGEZEIGT IN  app/EngineFlaeche.tsx:56-58
                       "Die Rechengrundlage steht sichtbar: der Nutzer soll wissen,
                        wonach gerechnet wird."   ->   Grundlage: {panel.grundlage}
WAS HEUTE DORT STEHT   'Eurocode 5 (Biegung, Durchbiegung L/300) mit Schneelast
                        nach DIN EN 1991-1-3'
                       -> die NORM steht da, die GRENZE nicht. "Eurocode 5" liest sich
                          wie ein Nachweis.
AUSGEGEBEN WERDEN u.a. ausnutzungBiegung · ausnutzungDurchbiegung
                       -> eine "Ausnutzung 0,85" liest JEDER als "Nachweis erfuellt".
                          Das ist die gefaehrlichste Zeile der ganzen Ausgabe.
WEITERE AUSGABEWEGE    gemessen: KEINE. Kein Export, kein PDF, keine Stueckliste
                       traegt heute ein EngineErgebnis (0 Treffer ausserhalb
                       enginePanels/EngineFlaeche).
```

> **Deshalb gehört der Vorbehalt NICHT in die Anzeige, sondern in das ERGEBNIS.** *Heute gibt es
> genau einen Ausgabeweg; morgen gibt es Export, Stückliste und PDF. Wer den Satz in
> `EngineFlaeche.tsx` schreibt, pflegt ihn danach an vier Stellen — und die vierte vergisst ihn.*
>
> **Vorschlag für das Kriterium (gehört an die Ausgabestelle, nicht in einen eigenen Auftrag):**
> `SparrenErgebnis` trägt ein **Pflichtfeld** mit dem Vorbehaltssatz. Dann kann keine Ausgabestelle
> ihn weglassen, ohne ihn **aktiv zu unterdrücken** — und das fällt in einer Prüfung auf.
> *Das ist genau der Generator-Satz, konsequent gedacht: **„ein Dateikopf wird nicht mitgeliefert,
> wenn jemand nur die Zahl übernimmt" → also muss der Vorbehalt Teil der Zahl sein, nicht ihrer
> Darstellung.***

### Regeln für die N-Gruppe

```text
1  Jede N-Zeile nennt NORM und FASSUNG/ANHANG. Ohne Norm keine N-Nummer —
   dann ist es ein Erfahrungswert und gehoert als F-Nummer mit Ampel behandelt
   (Muster: F-050 gelb, F-051 rot).
2  Jede N-Zeile nennt den GELTUNGSBEREICH. Eine Norm ohne Geltungsbereich ist
   eine Einladung zur Uebertragung auf einen Fall, den sie nicht deckt.
3  N-Nummern begruenden KEINE Ausfuehrungsentscheidung, solange ein Fach-Gate
   offen ist. Sie begruenden Vorbemessung, Angebot und Machbarkeit.
4  Aendert sich eine Norm, aendert sich die ZEILE — nicht der Code allein.
   Wer den Code aendert, ohne die Zeile zu aendern, hinterlaesst zwei Wahrheiten.
```

## Gruppe 7 — Gauben

### F-027 · Gaubenaufbau
- **Zweck:** Schlepp-, Trapez-, Flach- und Giebelgaube auf eine Dachfläche setzen
- **Eingabe:** Breite b, Höhe h, Tiefe d, Eigenneigung αG, Position (x,y) auf der Fläche
- **Formel:**
  ```
  αG in Radiant:  φ = αG · π/180
  Anstieg:        rise = d · tan(φ)
  Körper:         Quader(b, h + Sockeltiefe, d), um (h − Sockeltiefe)/2 angehoben
  Ausrichtung:    Drehung um y = atan2(fallRichtungₓ, fallRichtungz)
  ```
- **Grenzfall:** Gaube breiter als die Dachfläche → Absage. Eigenneigung ohne Vorgabe
  → 15° (Wert aus dem Quellcode).
- **Belegstelle:** `dachdecker_pro_3d.tsx:1190–1210, :1255`

## Gruppe 8 — Mengen und Zeiten

### F-050 · Materialkennwerte je Deckung · 🟡 NUR NÄHERUNG
| Deckung | kg/m² | Stück/m² |
|---|---|---|
| Dachziegel (Ton/Beton) | 45 | 12 |
| Naturschiefer | 30 | 35 |
| Trapezblech | 8 | 1 |
| Bitumenschweißbahn | 5 | 1 |

- **Verwendung:** Gewicht = Fläche(F-023) · kg/m² · Anzahl = Fläche · Stück/m²
- **Grenzfall:** Gilt für die **wahre** Dachfläche, nicht die Grundfläche.
  Wer die Grundfläche einsetzt, unterschätzt bei 45° um 41 %.
- **🟡 Q2-Prüfung 07.08.:** Gegenquelle nennt Tonziegel 2,9–3,7 kg/Stück,
  Betondachstein 3,4–4,35 kg/Stück. Bei 12 Stück/m² ergibt das 35–52 kg/m² —
  **45 liegt im Bereich, ist also plausibel.** Aber 12 Stück/m² ist modellabhängig
  (Frankfurter Pfanne eher 10). **Als Näherung nutzbar, für Angebote nicht.**
- **Belegstelle:** `dachdecker_pro_3d.tsx:186–191`

### F-051 · Zeitwerte je Gewerk (Minuten) · 🔴 GESPERRT — unbelegt
```
Gerüst    8 /m²      Sparren    10 /lfm     Unterspannbahn  3 /m²
Lattung   2 /lfm     Deckung    15 /m²      Dämmung        12 /m²
Aufräumen 90 pauschal
Dachhaken  6 /Stk    Haken geschliffen 5    Schiene 4 /lfm  Modul 12 /Stk
```
- **🔴 GESPERRT.** Diese Werte haben **keine Herkunft**. Sie stammen aus einem
  Prototyp, der sie selbst nicht belegt. Kalkulationszeiten hängen ab von Neigung,
  Zugänglichkeit, Kolonnengröße, Witterung und Region — eine Zahl ohne diese
  Bedingungen ist keine Zahl. **Wer damit ein Angebot rechnet, rechnet mit
  Erfundenem.** Bleibt als markierter Platzhalter stehen, damit sichtbar ist,
  dass hier echte Werte fehlen.
- **Belegstelle:** `dachdecker_pro_3d.tsx:194–198`

#### Vierter Fundort — im Produktivbaum, aber **ohne Aufrufer** (Planner 12.08., A-16)

*Yamas Antwort auf den M-02-Bericht führte hierher. Die Zahlen stehen zeichengenau dort — die
Auslieferung nicht. Beides gehört in dieselbe Zeile, sonst wird aus einer Sperre eine Panik oder
eine Beruhigung, und beide wären falsch:*

```text
resources/views/admin/layouts/roof.blade.php:73    // time assumptions (minutes) – adjust to your company values
                                            :74    const TIME_VARS = { … elf Werte … }
                                          :1672    const laborCost = (installMinutes / 60) * 65;
                                          :2266    'Montage (Arbeit)'  →  ein Euro-Betrag

GEMESSEN, weil "steht in resources/views" wie Auslieferung aussieht und keine ist:
  statische View-Referenz  "admin.layouts.roof" / "layouts.roof" / "layouts/roof"   0 / 0 / 0
  Route::get('roof')       zeigt auf admin/roof_config/roof.blade.php  →  0x TIME_VARS
  Historie der Datei       EIN Commit, e14cd1ec "Checkpoint: save WIP" (26.06.2026)
  Serverschreibpfade       0 (kein fetch, kein axios, kein <form action>)
  Reichweite dieser Aussage: kein STATISCHER Aufrufer. Dynamische View-Namen sind nicht
  ausgeschlossen (ProductController.php:443 ruft view($view, …)) — offen in A-16-1.
```

**Was das für die Ampel bedeutet: 🔴 bleibt, breiter begründet statt schärfer.** *Vier Fundorte,
null unabhängige Herkunftsangaben — die Sperre ist damit doppelt belegt. Sie wird **nicht** zur
Meldung „live", weil kein Bildschirm diesen Betrag heute zeigt. **Der zweite unbelegte Wert steht
daneben und ist keine Zeit, sondern ein Preis:** `* 65` — Stundensatz, hart im Code, ohne Quelle,
ohne Datum, ohne Gewerk. Er wird in A-16-3 gesondert geführt, weil er sonst unter „Zeitwerte"
mitläuft und dort niemand nach ihm sucht.*

> **Die Gefahr liegt nicht in der Auslieferung, sondern in ihrer Leichtigkeit:** *2.688 Zeilen
> fertige PV-Konfigurator-Oberfläche warten auf drei Zeilen in `web.php`. Der Aufwand für „live"
> ist kleiner als der Aufwand, die Sperre zu lesen. Deshalb A-16-2: **der Vermerk gehört an die
> Zahl, nicht nur in dieses Blatt.***

#### Nachtrag 12.08. (Generator, A-16 gebaut) — der Vermerk steht jetzt an der Zahl, und die offene Zeile ist zu

**Der Sperrvermerk ist eingebaut.** *Er steht dort, wo die Zahlen stehen — nicht nur hier:*

```text
roof.blade.php:74-101    Sperrvermerk ueber TIME_VARS  (F-051, vier Fundorte, was je Wert fehlt,
                         Reichweite der Datei, Verweis auf das Auftragsblatt)
              :102       const TIME_VARS = { … }        Werte UNVERAENDERT
              :1701-1714 eigener Vermerk fuer den Stundensatz — er ist ein PREIS, keine Zeit
              :1715      const laborCost = (installMinutes / 60) * 65;   Wert UNVERAENDERT
              :2309      'Montage (Arbeit)' → der Euro-Betrag, unberuehrt
```

**Die Zeilennummern oben im Abschnitt (`:73`, `:74`, `:1672`) sind der Stand VOR dem Bau** — *die
Vermerke haben sie verschoben; beide Stände stehen hier, damit niemand zweimal sucht.*

> **UND DIE OFFENE ZEILE IST GESCHLOSSEN.** *Oben steht: „Dynamische View-Namen sind nicht
> ausgeschlossen (`ProductController.php:443` ruft `view($view, …)`) — offen in A-16-1."*
> **Gemessen: sie sind ausgeschlossen.** *Die eine dynamische Stelle im ganzen Haus bekommt ihr
> `$view` zwei Zeilen davor zugewiesen, auf **genau zwei feste Namen** —
> `admin.product.product.partials.product_cards` und `…product_list`. **Keiner davon ist diese
> Datei.*** *Damit lautet die Aussage jetzt vollständig:* **kein Aufrufer, statisch UND dynamisch
> geprüft.**

*Zur Vollständigkeit der Belegkette, alle wörtlich gesucht: `fetch(` **0**, dazu `axios` · `$.post` ·
`$.ajax` · `<form` · `XMLHttpRequest` · `sendBeacon` je **0**. Die sieben `action=`-Treffer sind
**`data-action`-Attribute an Knöpfen**, kein Formularziel — gelesen, nicht gezählt (B5). **Angebot →
Auftrag → Rechnung ist damit aktenkundig unberührt.***

**Die Ampel bleibt 🔴.** *Sie wird durch diesen Bau weder schärfer noch milder: die elf Zeitwerte und
die 65 sind vorher wie nachher zeichengleich, es kam nur der Vermerk hinzu.* **Aus W3 wird ohne Umbau
W2, sobald Yamas Firmenwerte vorliegen.**
