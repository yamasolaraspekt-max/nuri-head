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

**Die Schichten 1, 4 und 5 sind Rechnung. Schicht 2 ist Messung. Schicht 3 ist beides.**

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
