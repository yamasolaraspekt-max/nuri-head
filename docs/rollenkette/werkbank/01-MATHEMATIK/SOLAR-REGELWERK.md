# SOLAR-REGELWERK — Sonnenstand, Einstrahlung, Verschattung

> **Reihe `S-`.** Eigene Nummern, weil das ein eigenes Fachgebiet ist und die `F-`-Reihe
> Planungsgeometrie führt. Aufbau je Eintrag wie in der `FORMELSAMMLUNG.md`:
> **Zweck · Eingabe · Formel · Ausgabe · Grenzfall.** Der Grenzfall ist Pflicht.
>
> **Erstellt vom Planner am 13.08.2026 auf Yamas Auftrag**, mit ausdrücklicher Erlaubnis,
> im Netz zu recherchieren und in `wberechnung` zu lesen. **Fachspezifikation, kein Bau** —
> keine Zeile Produktivcode. Quellen stehen unten mit Datum.
>
> ⚠ **Dieses Dokument entscheidet nichts über Geld, Recht oder Auslegung.** Wo ein Operand
> fehlt oder eine Fachfestlegung nötig ist, steht das ausdrücklich als **OFFEN AN YAMA**
> markiert — nach der Schutzgrenze werden solche Entscheidungen nicht still automatisiert.

---

## 0. Die tragende Einsicht: „Verschattung" ist EINE Frage aus FÜNF Schichten

**Der häufigste Fehler in diesem Thema ist, „Verschattung" als eine einzige Rechnung zu
behandeln.** Sie zerfällt in fünf Schichten mit **verschiedenen Datenquellen, verschiedener
Genauigkeit und verschiedener Zuständigkeit**. Wer sie vermischt, baut entweder etwas Falsches
oder etwas, das es schon gibt.

| # | Schicht | Was sie beantwortet | Woher | Selbst rechnen? |
|---|---|---|---|---|
| 1 | **Sonnenstand** | Wo steht die Sonne zu Zeitpunkt t? | Formel, keine Daten nötig | **JA** — deterministisch, exakt, billig |
| 2 | **Einstrahlungsangebot** | Wie viel Energie kommt bei wolkigem Wetter an? | Messreihen (DWD/PVGIS) | **NEIN** — das ist Wetter, kein Modell |
| 3 | **Transposition** | Wie viel davon trifft die geneigte Fläche? | Modell (isotrop/Hay/Perez) | **JA oder einkaufen** |
| 4 | **Verschattungsgeometrie** | Was steht wann im Weg? | **3D-Modell des Gebäudes** | **JA — und nur die Insel kann es** |
| 5 | **Elektrische Wirkung** | Was kostet der Schatten an Ertrag? | Modul-/Strangtopologie | **JA, aber nicht-linear** |
| 6 | **Simulation über Zeiträume** | Tag, Woche, Monat, Jahr — was kommt zusammen? | Schleife um 1–5 | **JA** — kein neuer Rechenweg, aber drei Fallen |

**Die Schichten 1, 4, 5 und 6 sind Rechnung. Schicht 2 ist Messung. Schicht 3 ist beides.**

**Und daraus folgt die Zuständigkeit, die meine eigene erste Antwort auf W-19 berichtigt:**
Ich hatte am 13.08. geschrieben, Verschattung werde *„eingekauft, nicht gerechnet"*. **Das
stimmt für Schicht 2 und trägt für Schicht 3 — für Schicht 4 ist es falsch.** Die
Verschattungs**geometrie** kann niemand einkaufen, der das Gebäude nicht kennt: Google Solar
sieht Nachbarhäuser und Bäume aus dem Luftbild, **aber nicht die Gaube, die erst geplant wird.**
**Genau das ist der Punkt, an dem die Insel etwas kann, was die Fremdquelle nicht kann.**

---

## Schicht 1 — Sonnenstand

**Normlage:** `DIN 5034-2`, `VDI 6007-3`, `VDI 3789-2`, `DIN EN 17037`. Die Formeln unten sind
die der VDI-6007-3-/DIN-5034-Linie, weil sie in der deutschen Gebäudetechnik die verbreitete
Referenz ist und PV*SOL sie neben NREL SPA ausdrücklich als wählbares Verfahren führt.

### S-001 · Tagesnummer und Jahreswinkel
- **Zweck:** Grundgröße aller folgenden Formeln
- **Eingabe:** Datum
- **Formel:** `J` = Tag des Jahres (1…365/366) · `J' = 360° · J / 365`
- **Ausgabe:** `J'` in Grad
- **Grenzfall:** **Schaltjahr.** Der Nenner 365 gilt auch im Schaltjahr — der Fehler ist
  < 1 Tag und liegt unter der Modellgenauigkeit. Wer 366 einsetzt, weicht von der Norm ab.
  **`J` muss aus dem lokalen Datum kommen, nicht aus UTC** — sonst springt der Jahrestag um
  Mitternacht falsch.

### S-002 · Deklination δ
- **Zweck:** Jahreszeitliche Höhe der Sonnenbahn — **die Größe, die „Sommer/Winter" abbildet**
- **Eingabe:** `J'` aus S-001
- **Formel (VDI 6007-3 / DIN 5034 / EN 17037):**
  `δ = 0,3948 − 23,2559·cos(J' + 9,1°) − 0,3915·cos(2J' + 5,4°) − 0,1764·cos(3J' + 26,0°)`
- **Ausgabe:** δ in Grad, Bereich ≈ −23,44° (21.12.) … +23,44° (21.06.)
- **Grenzfall:** Die einfache ASHRAE-Näherung `δ = 23,45°·sin(360/365·(J−81))` weicht bis zu
  **0,5°** ab. **Für Verschattung an Grenzlagen (flacher Sonnenstand, knapp über einem Nachbar-
  dach) ist das genug für ein falsches Ja/Nein.** Deshalb die dreigliedrige Fassung.

### S-003 · Zeitgleichung ZGL und Wahre Ortszeit WOZ
- **Zweck:** Aus der Uhrzeit die **tatsächliche** Sonnenzeit machen
- **Eingabe:** `J'`, Zonenzeit, geografische Länge λ
- **Formel:**
  `ZGL = 0,0066 + 7,3525·cos(J' + 85,9°) + 9,9359·cos(2J' + 108,9°) + 0,3387·cos(3J' + 105,2°)` [min]
  `MOZ = Zonenzeit − Zeitzonenversatz + 4 min/° · (λ − λ_Bezugsmeridian)`
  `WOZ = MOZ + ZGL/60` [h]
- **Ausgabe:** WOZ in Stunden
- **Grenzfall:** **Zwei Fallen, beide teuer.** (1) **Sommerzeit**: MESZ ist +2 h, MEZ +1 h —
  wer die Umschaltung vergisst, verschiebt den ganzen Tag um eine Stunde und damit den Schatten
  um bis zu 15° Azimut. (2) **Die Zeitgleichung schwankt über ±16 Minuten** — das sind bis zu
  **4° Stundenwinkel**. Beides ist an Grenzlagen entscheidungsrelevant. **Zeitzone und Länge
  gehören zu den Operanden, die die Insel liefern muss.**

### S-004 · Stundenwinkel ω
- **Zweck:** Tageszeitliche Position der Sonne
- **Eingabe:** WOZ aus S-003
- **Formel:** `ω = (WOZ − 12 h) · 15°/h`
- **Ausgabe:** ω in Grad; **negativ vormittags, 0 im wahren Mittag, positiv nachmittags**
- **Grenzfall:** Vorzeichenkonvention **muss** einmal festgelegt und überall gleich benutzt
  werden — EN 17037 schreibt `ωη = (12,00 h − TST)·15°` und dreht damit das Vorzeichen um.
  **Beide sind richtig, gemischt sind sie falsch.** → siehe S-020 (Azimutkonvention).

### S-005 · Sonnenhöhe γs (Elevation)
- **Zweck:** Wie hoch steht die Sonne — **die Größe, gegen die jede Verschattung geprüft wird**
- **Eingabe:** Breite φ, Deklination δ (S-002), Stundenwinkel ω (S-004)
- **Formel:** `sin γs = sin φ · sin δ + cos φ · cos δ · cos ω`
- **Ausgabe:** γs in Grad; 0° = Horizont, 90° = Zenit
- **Grenzfall:** **`γs ≤ 0` heißt: die Sonne ist unter dem Horizont — jede weitere Rechnung ist
  bedeutungslos und muss abbrechen, nicht null liefern.** Und bei `γs < ca. 5°` wird die
  atmosphärische Refraktion (≈ 0,5° Anhebung am Horizont) größer als viele Verschattungs-
  differenzen. **Für Ertrag ist das egal (die Leistung ist dort nahe null), für die Frage
  „ab wann liegt Schatten auf dem Modul" ist es das nicht.**

### S-006 · Sonnenazimut αs
- **Zweck:** Aus welcher Himmelsrichtung kommt die Sonne
- **Eingabe:** γs (S-005), φ, δ, WOZ
- **Formel (EN 17037):**
  `αs = 180° − arccos[(sin γs · sin φ − sin δ) / (cos γs · cos φ)]` für WOZ ≤ 12 h
  `αs = 180° + arccos[…]` für WOZ > 12 h
- **Ausgabe:** αs in Grad, **Konvention Nord = 0°, Ost = 90°, Süd = 180°, West = 270°**
- **Grenzfall:** **Der Nenner geht gegen null**, wenn `γs → 90°` (Zenitdurchgang, in
  Deutschland unmöglich) oder `φ → ±90°` (Pol). Für Deutschland (φ ≈ 47°…55°) unkritisch,
  **aber die Fallunterscheidung nach WOZ ist Pflicht** — ohne sie ist der Nachmittag am
  Vormittag gespiegelt, und der Schatten fällt auf die falsche Seite des Hauses.

### S-007 · Sonnenauf- und -untergang, Tageslänge
- **Zweck:** Der Rahmen jedes Tageslaufs — **wann beginnt und endet die Rechnung überhaupt**
- **Eingabe:** Breite φ, Deklination δ (S-002)
- **Formel:**
  `cos ω_s = −tan φ · tan δ` → Halbtagsbogen ω_s in Grad
  Tageslänge `T = 2 · ω_s / 15°/h` [h]
  Aufgang `WOZ_auf = 12 h − ω_s/15` · Untergang `WOZ_unter = 12 h + ω_s/15`
- **Ausgabe:** Auf-/Untergangszeit in WOZ, Tageslänge in Stunden
- **Grenzfall:** **`|tan φ · tan δ| > 1` → die Sonne geht nicht auf oder nicht unter**
  (Polarnacht/Polartag). **In Deutschland (φ ≤ 55°) tritt das nie ein**, aber die Formel muss
  es abfangen — `arccos` eines Werts außerhalb [−1, 1] ist sonst ein Absturz, kein Ergebnis.
  Und: **das ist der geometrische Horizont.** Refraktion und Sonnenscheibendurchmesser
  verschieben den *sichtbaren* Auf-/Untergang um ca. **4 Minuten** nach außen — für Ertrag
  bedeutungslos, für eine Uhrzeitanzeige nicht.

### S-008 · Auf- und Untergangs-**Azimut** — „im Osten" stimmt an genau zwei Tagen im Jahr
- **Zweck:** **Aus welcher Richtung** kommt die erste und geht die letzte Sonne — die Größe,
  die entscheidet, ob ein Ost- oder Westdach überhaupt Morgen-/Abendsonne bekommt und ob ein
  Schattenwerfer im Nordosten je eine Rolle spielt
- **Formel:** S-006 mit `γs = 0` eingesetzt:
  `cos α = −sin δ / cos φ` → Aufgang `αs = 180° − α`, Untergang `αs = 180° + α`
- **Eckwerte für φ = 51° (Mitteldeutschland), selbst nachgerechnet:**

| Tag | δ | Aufgang | Untergang | Tageslänge | Mittagshöhe |
|---|---|---|---|---|---|
| **21.06.** Sommersonnenwende | +23,44° | **50,8° = NORDOST** | **309,2° = NORDWEST** | **16,32 h** | 62,4° |
| **21.03. / 23.09.** Tagundnachtgleiche | 0° | **90,0° = OST** | **270,0° = WEST** | 12,00 h | 39,0° |
| **21.12.** Wintersonnenwende | −23,44° | **129,2° = SÜDOST** | **230,8° = SÜDWEST** | **7,68 h** | 15,6° |

- **Das ist der praktisch folgenreichste Absatz dieses Dokuments:**
  1. **Die Sonne geht nur an den Tagundnachtgleichen im Osten auf.** Im Sommer geht sie in
     Deutschland **nordöstlich** auf und **nordwestlich** unter — sie steht morgens und abends
     **hinter** dem Haus. Im Winter läuft sie in einem schmalen Bogen von **SO nach SW**.
  2. **Der Azimutbereich schwankt über das Jahr um 78,4°** (50,8° … 129,2°). **Ein Ostdach
     bekommt im Juni früh Sonne und im Dezember gar keine** — nicht weil es verschattet wäre,
     sondern weil die Sonne dort nicht steht.
  3. **Ein Schattenwerfer im Nordosten oder Nordwesten ist ausschließlich im Sommerhalbjahr
     relevant.** Wer ihn am 21.12. prüft, findet nichts — und übersieht ihn.
  4. **Umgekehrt: Verschattung aus Süd trifft den Winter am härtesten**, weil die Sonne dort
     nur 15,6° hoch steht (S-051) und jedes Hindernis einen 3,6-fach so langen Schatten wirft.
- **Grenzfall:** Die Tabelle gilt für **φ = 51°**. Zwischen Flensburg (55°) und dem Bodensee
  (47,5°) verschiebt sich alles spürbar — Tageslänge am 21.06. rund **17,3 h** im Norden gegen
  **15,8 h** im Süden. **Die Breite ist ein Operand, kein Näherungswert.**

### S-009 · Der Jahresgang der Bahn — und die Asymmetrie, die niemand erwartet
- **Zweck:** Zusammenhang der Tagesbögen über das Jahr — die Grundlage jeder Darstellung
- **Die Bahnschar:** Alle Tagesbögen sind zueinander **parallel verschobene Kreise**; ihre Höhe
  ist allein von δ bestimmt (S-002), also von der Jahreszeit. Zwischen Sommer- und
  Wintersonnenwende liegen **2 × 23,44° = 46,9°** Mittagshöhenunterschied — bei φ = 51° also
  62,4° gegen 15,6°. **Der Sonnenstand im Winter ist ein Viertel so hoch wie im Sommer.**
- **⚠ Die Asymmetrie, die fast jede selbstgebaute Darstellung falsch macht:** **Die Bahn ist
  symmetrisch zur WAHREN Ortszeit, nicht zur Uhr.** Weil die Zeitgleichung (S-003) über ±16
  Minuten schwankt, fallen **frühester Sonnenuntergang und spätester Sonnenaufgang NICHT auf
  den 21.12.** — der früheste Untergang liegt rund **zehn Tage vor**, der späteste Aufgang
  rund **zehn Tage nach** der Wintersonnenwende. **Wer Auf-/Untergang aus dem symmetrischen
  Halbtagsbogen um 12:00 Uhr Ortszeit rechnet, statt über WOZ, produziert genau diesen Fehler.**
- **Grenzfall:** Der Jahresgang ist **nicht sinusförmig in der Tageslänge**. Um die
  Sonnenwenden ändert sie sich kaum (Tage „stehen"), um die Tagundnachtgleichen am
  schnellsten — bis zu **4 Minuten pro Tag**. Wer zwischen Stützstellen linear interpoliert,
  trifft im März und September daneben.
- **Darstellungsempfehlung:** **Sonnenbahndiagramm** (Azimut waagerecht, Höhe senkrecht, je eine
  Kurve pro Stichtag aus S-051) — und darüber die Verschattungsmatrix als **Iso-Shading-Diagramm**
  (S-073). Damit steht in **einem** Bild: wann im Jahr, zu welcher Tageszeit, wie stark verschattet.

---

## Schicht 2 — Einstrahlungsangebot: **wird nicht gerechnet, sondern gemessen**

### S-010 · Die drei Komponenten
- **`GHI`** Globalstrahlung horizontal · **`DNI`** Direktstrahlung normal ·
  **`DHI`** Diffusstrahlung horizontal
- **Zusammenhang:** `GHI = DNI · sin γs + DHI`
- **Grenzfall:** Die Zerlegung ist **nicht** frei wählbar — wer nur GHI hat und DNI/DHI
  schätzt, benutzt ein Trennmodell (Erbs, Reindl, DIRINT) und **erbt dessen Fehler**.
  PVGIS liefert die Komponenten direkt; das ist der Grund, sie dort zu holen.

### S-011 · Kennzahlen Deutschland (Stand der Recherche 13.08.2026)
| Größe | Wert | Quelle |
|---|---|---|
| Globalstrahlung Jahressumme, Mittel 1991–2020 | **≈ 1.086 kWh/m²a** | DWD-Auswertung |
| Streubreite im selben Zeitraum | **975 … 1.259 kWh/m²a** | DWD |
| Nord- vs. Süddeutschland | **900–1.100** gegen **bis 1.300 kWh/m²a** | DWD-Karten |
| Jahressumme 2024 | 1.112 kWh/m²a | DWD |
| Jahressumme 2025 | 1.187 kWh/m²a | DWD |
| DWD-Raster | **1 × 1 km**, kostenfrei | DWD |

- **Grenzfall — und das ist die wichtigste Zahl in diesem Dokument:** **die Spanne
  975…1.259 ist ±13 % um den Mittelwert.** Wer mit einem bundesweiten Mittelwert rechnet,
  hat allein daraus einen Fehler, der **jeden Verschattungseffekt unter 10 % überdeckt**.
  **Eine aufwendige Verschattungsrechnung auf einem pauschalen Einstrahlungswert ist
  verschwendete Genauigkeit** — die Ortsdaten müssen zuerst stimmen.

---

## Schicht 3 — Transposition auf die geneigte Fläche

### S-020 · Einfallswinkel AOI (die Kernformel)
- **Zweck:** Wie schräg trifft die Direktstrahlung auf Dach/Modul
- **Eingabe:** Flächenneigung β (0° = waagerecht), Flächenazimut γF, Sonnenzenit
  θz = 90° − γs, Sonnenazimut αs
- **Formel:** `cos θ_AOI = cos θz · cos β + sin θz · sin β · cos(αs − γF)`
- **Ausgabe:** θ_AOI in Grad
- **Grenzfall:** **`cos θ_AOI < 0` bedeutet: die Sonne steht HINTER der Fläche.** Der Beitrag
  ist dann **null, nicht negativ** — `max(cos θ_AOI, 0)`. Wer das vergisst, bekommt negative
  Erträge am Nordhang. **Und: γF und αs müssen dieselbe Konvention haben** (S-006). Die
  Insel führt Azimut über `azimutDerNormalen` (F-024); PVGIS erwartet `aspect` als
  **−180…+180 mit 0 = Süd**. **Das sind zwei verschiedene Konventionen — die Umrechnung ist
  eine benannte Stelle, kein Detail.**

### S-021 · POA-Zerlegung (Plane of Array)
- **Formel:** `POA = POA_direkt + POA_diffus,Himmel + POA_boden`
  - `POA_direkt = DNI · max(cos θ_AOI, 0)`
  - `POA_diffus,isotrop = DHI · (1 + cos β)/2`
  - `POA_boden = GHI · ρ · (1 − cos β)/2`
- **Ausgabe:** W/m² bzw. kWh/m² je Zeitschritt
- **Grenzfall:** **Albedo ρ ist ein Operand, kein Naturgesetz** — typisch 0,1…0,4 für Land,
  deutlich höher über Schnee. **Ein fester Wert 0,2 ist eine Annahme und gehört benannt.**
  Bei β = 0 (Flachdach) verschwindet der Bodenanteil vollständig, bei senkrechten Flächen
  (Fassade, bifazial) ist er der halbe Bodenterm — dort trägt er erheblich.

### S-022 · Modellwahl
| Modell | Diffus-Zerlegung | Eignung |
|---|---|---|
| **Isotrop (Liu-Jordan)** | ein Term | einfachstes; **schneidet in Vergleichsmessungen für Südflächen am schlechtesten ab** |
| **Hay-Davies** | isotrop + circumsolar | *„klassisch und robust, gute Ergebnisse auch bei unvollkommener Diffusdatenkenntnis"* (PVsyst) |
| **Perez** | isotrop + circumsolar + **Horizontband** | PVsyst-Standard seit V6, **verlangt hochwertige Messdaten** |
- **Grenzfall:** **Perez auf geschätzte Diffusdaten anzuwenden ist Scheingenauigkeit.**
  Die Modellwahl muss zur Datenqualität passen, nicht umgekehrt. → **OFFEN AN YAMA**, falls
  je selbst transponiert wird; solange PVGIS die Transposition mitliefert, entfällt die Frage.

---

## Schicht 4 — Verschattungsgeometrie: **hier kann die Insel etwas, das niemand liefert**

### S-030 · Horizontverschattung (Fernverschattung)
- **Zweck:** Berge, Nachbargebäude, Baumreihen — alles, was den Horizont anhebt
- **Eingabe:** Horizontprofil `α_h(Azimut)` — Höhenwinkel je Himmelsrichtung
- **Formel:** verschattet, wenn `γs(t) < α_h(αs(t))`
  Objekthöhenwinkel: `tan α = (h_Objekt − h_Modul) / d_horizontal`
- **Ausgabe:** je Zeitschritt ein Ja/Nein für die **Direktstrahlung**
- **Praxis:** **PVGIS nimmt genau dieses Profil als Datei entgegen** — txt/csv, freie
  Auflösung (36 Werte = 10°-Schritte, 8 Werte = 45°), **beginnend im Osten gegen den
  Uhrzeigersinn**. Das ist der billigste denkbare Anschluss: *die Insel erzeugt das
  Horizontprofil aus ihrer Geometrie, PVGIS rechnet den Ertrag.*
- **Grenzfall — der wird am häufigsten übersehen:** **Horizontverschattung nimmt der Fläche
  die Direktstrahlung, aber NICHT die Diffusstrahlung.** Ein verschatteter Zeitschritt ist
  nicht ertragslos; es bleibt DHI übrig, in Deutschland **über das Jahr rund die Hälfte der
  Globalstrahlung**. Wer verschattete Stunden auf null setzt, rechnet den Verlust **grob zu
  groß**. Streng genommen reduziert der angehobene Horizont auch den sichtbaren Himmels-
  ausschnitt und damit den Diffusanteil — das ist der „Horizontband"-Term bei Perez.

### S-031 · Nahverschattung (Eigenverschattung am Gebäude)
- **Zweck:** Schornstein, Gaube, Dachfenster, Sat-Schüssel, Attika, Nachbargiebel
- **Eingabe:** 3D-Körper mit Position/Höhe, Sonnenrichtung `(γs, αs)`, Modulebene
- **Formel:** Schattenpolygon = Projektion des Körpers auf die Modulebene entlang des
  Sonnenvektors `s = (cos γs·sin αs, cos γs·cos αs, sin γs)`; verschatteter Anteil =
  Schnittfläche(Schattenpolygon, Modulfläche) / Modulfläche
- **Ausgabe:** verschatteter Flächenanteil je Modul und Zeitschritt
- **Grenzfall:** **Der Flächenanteil ist NICHT der Ertragsverlust** — siehe S-040. Und:
  **die Schattenlänge wächst bei flachem Sonnenstand gegen unendlich.** Ein 1 m hoher Kamin
  wirft bei γs = 5° einen **11 m langen** Schatten. Deshalb ist Nahverschattung im **Winter**
  und in den **Morgen-/Abendstunden** dominant — genau dann, wenn ohnehin wenig Ertrag da ist.
  **Wer die Verschattung ohne Zeitgewichtung mittelt, überschätzt sie deutlich.**
- **⚠ Und hier liegt der Grund, warum das nicht eingekauft werden kann:** Google Solar liefert
  Verschattung aus dem **Luftbild des Ist-Zustands**. Eine **geplante** Gaube, ein
  **geplanter** Anbau, ein **geplanter** Kamin sind dort nicht enthalten. **Die Insel kennt
  sie — sie plant sie gerade.**

### S-032 · Selbstverschattung bei Aufständerung (Reihenabstand)
- **Zweck:** Flachdach/Freifläche, Modulreihen hintereinander
- **Eingabe:** Modultischhöhe h, Neigung β, Grenz-Sonnenhöhe γs,grenz
- **Formel:** nötiger Reihenabstand `d = h · cos(γ_azimutdifferenz) / tan γs,grenz`
- **Grenzfall:** **Die Grenz-Sonnenhöhe ist eine wirtschaftliche Festlegung, keine physikalische.**
  Übliche Auslegung: schattenfrei am **21.12. zwischen 9 und 15 Uhr WOZ** — das ergibt in
  Deutschland γs,grenz ≈ **10…17°** je nach Breite. **Enger stellen bringt mehr kWp auf die
  Fläche und kostet spezifischen Ertrag.** → **OFFEN AN YAMA: welcher Auslegungstag/-zeitraum
  gilt als Hausstandard?** Das ist eine Geschäftsentscheidung, keine Rechenregel.

---

## Schicht 5 — Elektrische Wirkung: **der Schritt, an dem die meisten Rechnungen falsch werden**

### S-040 · Verschattung wirkt NICHT-LINEAR
- **Der Satz, der alles trägt:** **10 % verschattete Fläche sind nicht 10 % Ertragsverlust.**
- **Ursache:** Alle Zellen eines Moduls und alle Module eines Strangs sind in **Reihe**
  geschaltet. **Die schwächste Zelle bestimmt den Strom des ganzen Strangs** —
  „Gartenschlaucheffekt", fachlich Mismatch-Verlust.
- **Bypassdioden** überbrücken verschattete Zellgruppen: typisch **drei Substrings je Modul**.
  Sie begrenzen den Schaden auf ⅓ Modulleistung je betroffenem Substring, **beseitigen ihn
  aber nicht**. Ohne sie kann **eine** verschattete Zelle das ganze Modul auf nahe null ziehen.
- **MPP-Tracking:** Teilverschattung erzeugt **mehrere lokale Maxima** auf der Kennlinie.
  Einfache MPPT-Algorithmen bleiben im lokalen Maximum hängen; Geräte mit Schattenmanagement
  scannen die volle Kennlinie.
- **Grenzfall:** **Die Wirkung hängt von der Verschaltung ab, nicht nur von der Geometrie.**
  Ein Schatten quer über eine Modulreihe trifft **alle** Substrings, derselbe Schatten längs
  trifft **einen**. **Ohne Kenntnis von Strangbelegung, Modulorientierung und Wechselrichter-
  typ ist aus einer Verschattungsfläche kein Ertragsverlust ableitbar** — nur eine Bandbreite.
- **⚠ Regel daraus:** Ein Werkzeug, das aus verschatteter Fläche **eine einzelne
  Verlustprozentzahl** ausgibt, **behauptet mehr, als es weiß.** Zulässig sind: der
  **geometrische** verschattete Flächenanteil (das ist eine Messung), eine **Bandbreite**,
  oder ein Verlust **mit ausgewiesener Verschaltungsannahme**.

---

## Zeit und Jahreszeiten — die Auflösungsfrage

### S-050 · Zeitliche Auflösung
| Auflösung | taugt für | taugt nicht für |
|---|---|---|
| **Stundenwerte, 8.760/a** | Jahresertrag, Verschattungsverluste, Standard aller Ertragsrechner | steile Schattenkanten |
| **15 min, 35.040/a** | Nahverschattung mit scharfen Kanten (Kamin, Mast) | — |
| **Monats-/Jahresmittel** | Überschlag, Angebotsvergleich | **jede Verschattungsaussage** |
| **Vier Stichtage** (21.03./21.06./21.09./21.12.) | **Darstellung** und Plausibilitätsprüfung | Ertragszahlen |
- **Grenzfall:** **Ein Jahresmittel kann Verschattung grundsätzlich nicht abbilden**, weil
  Verschattung eine **Funktion der Tageszeit** ist. Wer aus Jahressummen einen
  Verschattungsverlust ableitet, hat geraten.

### S-051 · Die vier Stichtage (für Darstellung und Prüfung)
| Tag | δ | Sonnenhöhe mittags bei φ = 51° | Bedeutung |
|---|---|---|---|
| 21.06. Sommersonnenwende | +23,44° | ≈ 62,4° | höchste Bahn, kürzeste Schatten |
| 21.03. / 23.09. Tagundnachtgleiche | ≈ 0° | ≈ 39,0° | Referenzfall |
| **21.12. Wintersonnenwende** | **−23,44°** | **≈ 15,6°** | **längste Schatten — der Auslegungsfall** |
- **Formel:** mittags gilt `γs,max = 90° − φ + δ`
- **Grenzfall:** **Der 21.12. ist der Prüfstein jeder Verschattungsaussage.** Bei γs = 15,6°
  wirft **jedes** Objekt einen **3,6-fach so langen** Schatten wie seine Höhe. **Wer die
  Verschattung am Sommertag prüft, prüft den harmlosen Fall.**

---

## Anwendungsfall entscheidet — und hier liegt ein Befund

### S-060 · PV-Ertrag → Verschattung zählt voll
Direkter Einfluss auf kWh. Alle Schichten 1–5 sind relevant.

- **⚠ Grenzfall — und er berichtigt die Überschrift, die für sich allein irreführend ist
  (ergänzt 15.08.):** *„Zählt voll"* meint **die Frage**, nicht **die Zahl**. Beim PV-Ertrag ist
  Verschattung ertragswirksam und darf nicht vernachlässigt werden — **daraus folgt aber nicht,
  dass verschattete Fläche gleich Ertragsverlust ist.** Genau das verbietet **S-040**: die
  Wirkung ist **nicht-linear**, hängt an Strangbelegung, Modulorientierung und
  Wechselrichtertyp, und *„ein Werkzeug, das aus verschatteter Fläche eine einzelne
  Verlustprozentzahl ausgibt, behauptet mehr, als es weiß."*
- **Was S-060 also verlangt:** alle fünf Schichten **rechnen** — und das Ergebnis nach S-040
  ausweisen: **geometrischer Flächenanteil** (Messung) · **Bandbreite** · oder **Verlust mit
  benannter Verschaltungsannahme**. **Ein einzelner Prozentwert ohne Annahme ist auch hier
  unzulässig.**
- **Der Fehler, gegen den das steht:** Wer S-060 allein liest, hält *„zählt voll"* für die
  Erlaubnis, die Fläche direkt in Prozent umzurechnen. **S-060 und S-040 standen bis hierher
  ohne Verweis nebeneinander** — die schärfere Regel war da, aber nicht von hier aus auffindbar.

### S-061 · **Heizlast nach DIN EN 12831 → solare Gewinne werden BEWUSST NICHT angesetzt**
- **Die Norm bildet einen Auslegungs-Worst-Case ab:** Norm-Außentemperatur, **keine solaren
  und keine internen Gewinne**, ggf. Aufheizzuschlag. Damit ist sichergestellt, dass die
  Heizung auch am kalten, trüben Tag ohne innere Wärmequellen trägt.
- **⚠ BEFUND, der eine frühere Einschätzung von mir berichtigt:** `wberechnung` führt in
  `INVENTUR_BERICHT.md:76` und `:218` *„Solare Gewinne / Verschattung / Orientierungsfaktoren
  in der Heizlast"* mit **❌**. Ich hatte das am 13.08. als **Lücke** gelesen.
  **Es ist keine Lücke — es ist normkonform.** Für die **Norm-Heizlast** gehören solare
  Gewinne **nicht** hinein. Wer sie dort einbaut, rechnet die Heizung **zu klein**.
- **Die bekannte Folge, sauber benannt:** weil DIN EN 12831 solare und interne Gewinne
  vernachlässigt, wird die Normheizlast **im realen Betrieb meist nicht erreicht**. Das ist
  ein gewollter Sicherheitsabstand, kein Rechenfehler.

### S-062 · Energiebedarf nach DIN V 18599 / GEG → Verschattung zählt, aber pauschaler
- Dort **gibt** es Verschattungsfaktoren. Für übliche Fälle mit geringer Laibungsverschattung
  ist **0,9** der geführte Standardwert. Das Verfahren ist stärker tabelliert und lässt
  weniger Pauschalierung zu als andere Normen.
- **Grenzfall:** **Ein Verschattungsfaktor aus DIN V 18599 ist kein PV-Verschattungsverlust
  und umgekehrt.** Verschiedene Normen, verschiedene Bezugsgrößen, nicht austauschbar. Eine
  Zahl aus dem einen Verfahren im anderen zu verwenden, ist ein Fachfehler.

---

## Schicht 6 — Simulation über Zeiträume

> **Yamas Auftrag vom 13.08.:** *„für Ertragsprognose und Verschattungsanalyse brauchen wir auch
> eine Simulation für bestimmte Zeiträume — beliebiger Tag im Jahr, Monat, oder über die
> gesamte Woche, Monat, Jahr."*
>
> **Die gute Nachricht zuerst: das ist KEIN neuer Rechenweg.** Ein Zeitraum ist eine
> **Schleife** um die Schichten 1–5. Was neu dazukommt, sind **drei** Dinge, und alle drei sind
> Fallen: **wie man aggregiert** (S-074), **wie man den Rechenaufwand beherrscht** (S-073), und
> **welches Wetterjahr man nimmt** (S-077).

### S-070 · Der Simulationskern — ein Zeitschritt
- **Zweck:** Die kleinste Einheit, aus der jeder Zeitraum zusammengesetzt wird
- **Ablauf je Zeitschritt `t`:**
  ```
  1  Sonnenstand      γs(t), αs(t)                        S-001…S-006
  2  ABBRUCH wenn     γs ≤ 0  (Nacht → Ertrag 0, kein Schatten, keine Rechnung)
  3  Verschattung     f_schatt(γs, αs) ∈ [0,1] je Modul   S-030…S-032
  4  Einstrahlung     GHI(t), DNI(t), DHI(t) aus Wetterreihe   S-010
  5  Transposition    POA(t) = f(AOI, β, γF, Albedo)      S-020…S-021
  6  Verschattung an  POA_direkt · (1 − f_schatt) + POA_diffus (siehe Grenzfall!)
  7  Elektrisch       Ertrag(t) unter Verschaltungsannahme     S-040
  ```
- **Ausgabe je Zeitschritt:** `γs`, `αs`, `f_schatt`, `POA`, `Ertrag`
- **Grenzfall — Schritt 6 ist die Stelle, an der die meisten Simulationen falsch werden:**
  **Der Verschattungsfaktor wirkt auf die DIREKTstrahlung, nicht auf die gesamte POA.**
  `POA · (1 − f_schatt)` ist **falsch** und rechnet den Verlust zu groß (S-030). Richtig ist,
  nur den Direktanteil zu mindern; der Diffusanteil bleibt weitgehend, weil der Himmel
  weiterhin sichtbar ist.

### S-071 · Zeitraumtypen — vier, und sie haben verschiedene Zwecke
| Typ | Zeitschritte | wofür er taugt | wofür **nicht** |
|---|---|---|---|
| **Zeitpunkt** | 1 | Schattenbild „jetzt", Anschauung | jede Zahl |
| **Tagesgang** (beliebiger Tag) | ~50–1.440 | **Schattenwanderung**, Verschattungsprüfung, Darstellung | Ertragsprognose |
| **Woche / Monat** | 168 / ~720 | Teilsumme, saisonaler Vergleich | Jahresaussage |
| **Jahr** | **8.760** (Schaltjahr 8.784) | **Ertragsprognose**, Verschattungsverlust in % | Momentaussagen |
- **Grenzfall:** **Ein Tageslauf ist keine Prognose, und ein Jahreslauf ist keine Anschauung.**
  Yamas vier Zeiträume sind **nicht dieselbe Rechnung in verschiedener Länge**, sondern
  **verschiedene Fragen**: der Tag beantwortet *„liegt Schatten auf dem Modul, und wann?"*,
  das Jahr beantwortet *„was kostet er?"*. **Beide Ausgaben müssen unterschiedlich aussehen** —
  der Tag als Verlauf, das Jahr als Summe mit Monatsaufteilung.

### S-072 · Zeitschrittweite je Zweck
| Schrittweite | Schritte/Jahr | wofür |
|---|---|---|
| **1 min** | 525.600 | Animation der Schattenkante; **nie** für Jahresläufe |
| **15 min** | 35.040 | Nahverschattung mit scharfen Kanten (Kamin, Mast, Gaubenwange) |
| **1 h** | **8.760** | **Standard** — alle Ertragsrechner, und die Auflösung der Wetterdaten |
| **Monatsmittel** | 12 | Überschlag, Angebotsvergleich |
- **Grenzfall — die Schrittweite darf die Datenlage nicht überholen:** Wetterreihen liegen als
  **Stundenwerte** vor. Wer mit 1-Minuten-Schritten simuliert, **interpoliert Wetter, das er
  nicht hat** — die Verschattungsgeometrie wird feiner, die Ertragsaussage nicht besser.
  **Feine Schritte sind für die Geometrie richtig und für den Ertrag Scheingenauigkeit.**
  → Empfehlung: **Geometrie fein, Ertrag stündlich** — das ist genau die Trennung, die S-073 möglich macht.

### S-073 · **Die Verschattungsmatrix — der Trick, der Jahresläufe erst bezahlbar macht**
- **Zweck:** Verschattung für 8.760 Zeitschritte berechnen, ohne 8.760 Geometrieläufe
- **Die Einsicht:** **Der Verschattungsfaktor hängt NUR vom Sonnenstand ab, nicht vom Wetter
  und nicht vom Datum.** Zwei Zeitpunkte mit demselben `(γs, αs)` haben **denselben** Schatten —
  egal ob 15. Februar oder 27. Oktober, egal ob bewölkt oder klar.
- **Verfahren (so löst es PVsyst):** Den Faktor **einmal** über ein Raster in Sonnenhöhe und
  Azimut vorberechnen — **PVsyst nutzt 10° in der Höhe und 20° im Azimut** — und im Simulations-
  lauf je Zeitschritt nur **interpolieren**. PVsyst nennt das den *„Fast calculation"*-Modus;
  der Gegenmodus *„Slow calculation"* rechnet die Geometrie in jedem Schritt neu und vermeidet
  den Interpolationsfehler.
- **Größenordnung:** Ein Raster mit 20°/10° hat ≈ 18 × 9 = **162 Stützstellen**, von denen in
  Deutschland nur die tatsächlich durchlaufenen Bahnen belegt werden. Gegenüber 8.760 vollen
  Geometrieläufen ist das rund **eine Größenordnung weniger Arbeit** — und der Lauf lässt sich
  **wiederholen** (anderes Wetterjahr, andere Modulbelegung), **ohne die Geometrie erneut zu rechnen**.
- **Das Nebenprodukt ist die beste Darstellung, die es für dieses Thema gibt:** dieselbe Matrix,
  über das Sonnenbahndiagramm gelegt, ergibt das **Iso-Shading-Diagramm** — Linien gleichen
  Verschattungsfaktors auf den Sonnenbahnen. **Ein Bild, das gleichzeitig beantwortet:
  wann im Jahr, zu welcher Tageszeit, wie stark.**
- **Grenzfall:** **Bei scharfen Schattenkanten ist die Interpolation zwischen Stützstellen der
  Fehler.** Ein Kamin erzeugt einen harten Sprung von 0 auf 1 — 10°-Schritte glätten den weg.
  **Für kleine Anlagen mit wenigen Schattenwerfern ist die volle Rechnung je Schritt vorzuziehen**
  (PVsysts eigene Empfehlung: *„for not too big systems"*). **Die Matrix ist eine Optimierung
  für große Systeme, kein Genauigkeitsgewinn.**

### S-074 · **Aggregation — hier entsteht die häufigste Falschaussage**
- **Zweck:** Aus Zeitschritten eine Zahl für Woche/Monat/Jahr machen
- **Die Regel:** **Energie wird summiert. Anteile werden ertragsgewichtet. Nichts wird
  ungewichtet gemittelt.**
  ```
  Ertrag_Zeitraum   = Σ Ertrag(t)                                  [kWh]
  Einstrahlung      = Σ POA(t) · Δt                                [kWh/m²]
  Verschattungs-
  verlust_Zeitraum  = 1 − ( Σ Ertrag_mit(t) / Σ Ertrag_ohne(t) )   [%]   ← RICHTIG
                    ≠  Mittelwert( f_schatt(t) )                          ← FALSCH
  ```
- **Warum das der Kernfehler ist, an Yamas eigenem Fall:** Ein Kamin verschattet im **Dezember**
  30 % der Fläche und im **Juni** 2 %. Der ungewichtete Mittelwert sagt **16 %**. Aber der
  Dezember trägt in Deutschland nur einen **Bruchteil** des Jahresertrags — ertragsgewichtet
  bleiben vielleicht **4 %** übrig. **Die ungewichtete Zahl ist um das Vierfache zu hoch, und
  sie ist die Zahl, die ein naiv gebautes Werkzeug ausgibt.**
- **Der saubere Weg ist ein Doppellauf:** einmal **mit** und einmal **ohne** Schattenwerfer,
  Differenz der **Ertragssummen**. Das ist die einzige Definition von „Verschattungsverlust",
  die stimmt — und sie kostet zwei Läufe. **Mit der Matrix aus S-073 ist der zweite fast gratis.**
- **Grenzfall:** **Nachtstunden gehören nicht in den Nenner.** `γs ≤ 0` heißt kein Ertrag und
  keine Verschattung — wer über alle 8.760 Stunden mittelt statt über die Sonnenstunden,
  halbiert jede Prozentzahl.

### S-075 · Was ein Lauf zurückgeben muss
- **Immer mitzuliefern, sonst ist das Ergebnis nicht prüfbar:**
  Zeitraum und Schrittweite · Zahl der Zeitschritte **mit `γs > 0`** · Wetterquelle und
  Zeitbezug (**TMY oder reales Jahr, welches**) · Albedo · Verschaltungsannahme (S-040) ·
  **ob die Matrix (S-073) benutzt wurde und mit welcher Rasterweite**
- **Grenzfall:** **Eine Ertragszahl ohne diese Angaben ist nicht nachrechenbar und damit keine
  Prognose, sondern eine Behauptung.** Zwei Läufe mit verschiedenem Wetterjahr liefern
  verschiedene Zahlen — ohne Angabe des Jahres ist der Unterschied unerklärbar.

### S-076 · Zeitrechnung im Jahreslauf — drei Stolperstellen
1. **Sommerzeit.** Im Jahreslauf springt die lokale Uhr zweimal. **Intern wird durchgehend in
   WOZ (oder UTC) gerechnet**, die lokale Zeit ist reine Anzeige. Wer in Ortszeit schleift,
   erzeugt eine doppelte und eine fehlende Stunde — und beide fallen in die Ertragsstunden.
2. **Schaltjahr.** 8.784 statt 8.760 Stunden. Für Summen relevant, für S-001 nicht (dort bleibt
   der Nenner 365).
3. **Der Zeitstempel eines Stundenwerts.** Ein Stundenwert kann für den **Anfang**, die **Mitte**
   oder das **Ende** der Stunde stehen. **Ein halbstündiger Versatz ist ein Fehler von 7,5° im
   Stundenwinkel** — morgens und abends entscheidet das über verschattet oder nicht.
   **Die Konvention der Wetterquelle ist zu übernehmen, nicht zu raten.**

### S-077 · Welches Wetterjahr — TMY, nicht ein reales
- **TMY** (*Typical Meteorological Year*) ist ein **künstliches Jahr aus Stundenwerten**, bei
  dem die repräsentativsten Monate aus einer langen Messreihe zusammengesetzt werden — PVGIS
  bildet es aus dem Zeitraum **2005–2020**. Es bildet **langfristig typische Bedingungen** ab.
- **Regel:** **Für eine Prognose gilt TMY. Ein einzelnes reales Jahr ist keine Prognose,
  sondern ein Rückblick** — und die Jahre schwanken erheblich (die deutschen Jahressummen
  1.112 im Jahr 2024 gegen 1.187 kWh/m² im Jahr 2025, S-011). **PVGIS gibt die zu erwartende
  Schwankung zwischen zwei Jahren selbst mit aus; diese Angabe gehört in jede Ausgabe.**
- **Grenzfall:** **Ein reales Jahr ist richtig, wenn eine bestehende Anlage nachgerechnet wird**
  (Soll-Ist-Vergleich). Dann ist TMY falsch. **Der Zweck entscheidet, und der Zweck gehört in
  die Ausgabe** (S-075).

### S-078 · Was die Simulation nicht darf
1. **Nicht vom Tag aufs Jahr hochrechnen.** Ein Tageslauf × 365 ist keine Jahresprognose —
   weder Sonnenstand noch Wetter noch Verschattung sind über das Jahr konstant.
2. **Nicht Monatsmittel verschatten.** Verschattung ist eine Funktion der **Tageszeit**; ein
   Monatsmittelwert hat keine Tageszeit mehr (S-050).
3. **Keine Verschattungszahl ohne den Doppellauf** (S-074). „Geschätzter Verschattungsverlust"
   ohne Referenzlauf ohne Schattenwerfer ist geraten.
4. **Nicht die Matrix ohne Vermerk benutzen.** Sie ist eine Näherung mit benannter Rasterweite
   (S-073) — sie gehört in die Ausgabe, nicht in die stille Voreinstellung.

---

## Was das für ticket konkret heißt

### Die Naht, vollständig benannt
```
Insel (3D-Modell)                    →  Geometrie
   Dachflächen-Azimut (F-024 azimutDerNormalen, gebaut)
   Neigung (RoofNode.neigungGrad, gebaut)
   Fläche, Aufbauten (Gaube/Kamin/Sat — W-22, gebaut)
   ↓  erzeugt daraus:  HORIZONTPROFIL (S-030) + Nahschatten-Zeitreihe (S-031)
PVGIS / Google Solar                 →  Einstrahlung und Ertrag
   PvgisController.php:41   PVCalc  (angle, aspect, loss)
   PvgisController.php:103  Google Solar (azimuth_deg, pitch_deg, yearly_kwh, sunshine_hours)
   ↓
fachFlaechen.ts:358                  →  Anzeige „Verschattungsverlust %"
```

### Drei Dinge, die dabei nicht passieren dürfen
1. **Keine eigene Einstrahlungs-Zeitreihe.** Schicht 2 ist Messung. Eine dritte Wahrheit neben
   DWD und PVGIS wäre schlechter als beide.
2. **Keine Verlustprozentzahl ohne Verschaltungsannahme** (S-040).
3. **Keine zwei Dachgeometrien.** Google Solar liefert eigene Dachsegmente aus dem Luftbild,
   die Insel modelliert dasselbe Dach. **→ OFFEN AN YAMA: welche führt?** Meine Empfehlung:
   **die Insel führt** (sie kennt den Planungsstand, das Luftbild kennt den Ist-Stand), und
   Google Solar dient als **Plausibilitätsprüfung**, nicht als Quelle.

### Offene Operanden — nach der Schutzgrenze nicht still zu setzen
| # | Operand | warum es Yamas Entscheidung ist |
|---|---|---|
| 1 | **Albedo ρ** (S-021) | Annahme über den Standort, keine Konstante |
| 2 | **Grenz-Sonnenhöhe für Reihenabstand** (S-032) | wirtschaftliche Auslegung: kWp gegen spezifischen Ertrag |
| 3 | **Zeitliche Auflösung** (S-050) | Stundenwerte reichen für Ertrag, nicht für scharfe Schattenkanten |
| 4 | **Führende Dachgeometrie** | Insel oder Google Solar — sonst zweite Wahrheit |
| 5 | **Transpositionsmodell** (S-022) | entfällt, solange PVGIS transponiert |

---

## Quellen (recherchiert 13.08.2026)

- **Sonnenstandsformeln, Normen DIN 5034-2 / VDI 6007-3 / VDI 3789-2 / DIN EN 17037** —
  [Energie-Wiki: Sonnenstandsberechnung](https://wiki.energie-m.de/Sonnenstandsberechnung)
- **Azimutkonvention, DIN 5034-2 und NREL SPA als Verfahren** —
  [PV*SOL Hilfe: Sonnenposition](https://help.valentin-software.com/pvsol/de/berechnungsgrundlagen/einstrahlung/sonnenposition/)
- **Transpositionsmodelle Hay / Perez, isotroper Term (1+cos θ)/2, Albedo (1−cos θ)/2** —
  [PVsyst: Transposition model](https://www.pvsyst.com/help/physical-models-used/irradiation-models/transposition-model.html)
- **POA-Komponentenzerlegung, AOI-Projektion, Albedo-Bereich 0,1–0,4** —
  [pvlib: get_total_irradiance](https://pvlib-python.readthedocs.io/en/stable/reference/generated/pvlib.irradiance.get_total_irradiance.html)
- **Vergleich der Modellgüte (Hay-Davies/Reindl gut, Liu-Jordan am schlechtesten für Südflächen)** —
  [Comparison of Modelled and Measured Tilted Solar Irradiance](https://d-nb.info/1214330517/34)
- **PVGIS-Horizontdatei: Format, Auflösung, Ostbeginn gegen den Uhrzeigersinn, tan α** —
  [photovoltaik-web: Verschattungsberechnung mit Horizontdatei](https://www.photovoltaik-web.de/photovoltaik/ertragsprognose/pvgis/verschattungsberechnung-mit-horizontdatei)
- **Verschattungsmatrix: Vorberechnung in 10°-Höhen- und 20°-Azimutschritten, Interpolation,
  „Fast" gegen „Slow calculation"** —
  [PVsyst: Shading factor table](https://www.pvsyst.com/help/project-design/shadings/calculation-and-model/shading-factor-table.html) ·
  [PVsyst: Shading factor (Definition 0…1)](https://www.pvsyst.com/help/glossary/shadings/shading-factor.html)
- **Iso-Shading-Diagramm: Linien gleichen Verschattungsfaktors über den Sonnenbahnen** —
  [PVsyst: Iso-shading diagram](https://www.pvsyst.com/help/project-design/shadings/calculation-and-model/iso-shading-diagram.html)
- **TMY: künstliches Jahr aus Stundenwerten, PVGIS aus 2005–2020, Jahresschwankung wird
  mitgegeben** —
  [PVGIS TMY-Generator (JRC)](https://joint-research-centre.ec.europa.eu/photovoltaic-geographical-information-system-pvgis/pvgis-tools/pvgis-typical-meteorological-year-tmy-generator_en) ·
  [Solargis: Time Series and TMY data](https://kb.solargis.com/docs/time-series-and-tmy-data)
- **Nicht-Linearität, Bypassdioden, Mismatch, MPPT-Schattenmanagement** —
  [Photovoltaikforum: Wie wirkt sich Verschattung auf PV-Module aus](https://www.photovoltaikforum.com/core/article/13-wie-wirkt-sich-verschattung-auf-pv-module-aus/) ·
  [photovoltaikbuero: Teilverschattung bei Solarmodulen (Messungen)](https://photovoltaikbuero.de/pv-know-how-blog/teilverschattung-bei-solarmodulen-messungen/)
- **Globalstrahlung Deutschland, DWD-Raster 1×1 km** —
  [DWD: Globalstrahlung, 30-jährige Monats- und Jahressummen](https://www.dwd.de/DE/leistungen/solarenergie/strahlungskarten_mvs.html) ·
  [Echtsolar: Globalstrahlung](https://echtsolar.de/globalstrahlung/)
- **DIN EN 12831: keine solaren/internen Gewinne im Auslegungsfall** —
  [Bau mal Schlau: DIN EN 12831 Heizlastberechnung](https://bau-mal-schlau.de/din-en-12831-heizlastberechnung-2026-einfach-erklaert/) ·
  [Haustec: Die Lücke zwischen Regelwerk und Realität](https://www.haustec.de/heizung/waermeerzeugung/heizlast-nach-din-en-12831-die-luecke-zwischen-regelwerk-und-realitaet)
- **DIN V 18599 Verschattungsfaktor, Standardwert 0,9** —
  [bauphysik-software: solare Gewinne, Verschattungsfaktor](https://www.bauphysik-software.de/de-de/support-report399.html)

> **Grenze dieses Dokuments, ausdrücklich:** Die Normtexte selbst (DIN 5034-2, DIN EN 12831,
> DIN V 18599, VDI 6007-3) sind **kostenpflichtig und lagen mir nicht vor**. Die Formeln oben
> stammen aus Fachquellen, die sich auf sie berufen. **Vor einer normativ belastbaren
> Auslegung ist am Normtext gegenzulesen** — für Planung, Darstellung und Größenordnung
> tragen sie.
