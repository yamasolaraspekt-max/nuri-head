# Prüfbericht — PV-Installateur / Montagefachkraft (Unterkonstruktion & Belegung)

**Datum:** 2026-06-12
**Prüfer:** PV-Installateur / Montagefachkraft (fachliche Prüfung, NUR Analyse)
**Geprüfte Grundlagen:**
- `docs/konzepte/layout-2026-06/prompt-pv-dachplanung-fachkonzept.md`
- `docs/konzepte/layout-2026-06/plan-pv-dachplanung-umsetzung.md`
- `docs/konzepte/layout-2026-06/konzept-produktdatenbank-import.md`
- `src/utils/pvPlanung.ts`, `src/utils/roofModel.ts`
- `src/pages/energie/PvPlanungPage.tsx`, `src/pages/energie/RoofScene3D.tsx`

**Verifizierter Code-Stand:** `PvMountingSystem`, `MountingRule`, `RoofObstacle`, `RoofMaterial` existieren ausschließlich als **Konzepttext** (Markdown). Im Code (`src/`, `backend-laravel/`) gibt es davon **keine Implementierung**. Der aktuelle Stand ist: einfaches `RoofModel` mit einer Eindeckungs-Enum (`ziegel | trapezblech | schiefer`), hartcodierten Modulmaßen und einer Ertragsformel mit zwei Faktoren. Die Konzepte sind fachlich deutlich reifer als der Code — der Bericht trennt daher konsequent **„Konzept gut, aber noch nicht gebaut"** von **„auch im Konzept fachlich zu dünn/fehlend"**.

---

## Punkt 1 — Unterkonstruktion nach Dachtyp / Eindeckung

### Befund: Code [HOCH] / Konzept [MITTEL]

**Code-Realität:** Es gibt **keine** Unterkonstruktion als Datenobjekt. In `RoofScene3D.tsx` werden Dachhaken (`L.dachhaken`), Schiene (`L.schienen`) und eine Tellerkopfschraube rein **dekorativ und pauschal** gezeichnet — unabhängig von der Eindeckung. `roofModel.ts:243` setzt „Dachhaken (Richtwert): 2 je Modul" **für jede Eindeckung gleich**, auch für Trapezblech und Schiefer. Das ist fachlich falsch:
- Trapezblech bekommt **keine** Dachhaken, sondern Stockschrauben/Trapezklemmen/Kurzschienen — die „2 Haken je Modul"-Logik ist hier komplett unzutreffend.
- Stehfalz/Metallfalz bekommt **Falzklemmen ohne Dachdurchdringung** — auch hier keine Haken.
- Flachdach (im Code `eindeckung: "trapezblech"` als Vorlage, `flach_standard`) braucht **Aufständerung + Ballast**, nicht Haken auf Latten.

**Konzept-Realität:** `PvMountingSystem` (Plan §3.6) und `MountingRule` (Produktdatenbank §E) decken den Grundgedanken fachlich richtig ab: Eignung nach `dachtypen`/`eindeckungen`, `dachhaken.aufSparrenPflicht`, `ballast.windlastzone`, Begründung + Quelle. Die Beispielregeln (Tonziegel→Haken/Solarhalter, Trapez→Kurzschiene/Klemme/Stockschraube nach Profil+Blechstärke, Stehfalz→Falzklemme, Flachdach→Aufständerung+Ballast, Schiefer→Sonderhaken) sind korrekt und vollständig in der Breite.

**Fachliche Lücken im Modell (auch im Konzept zu dünn):**
- **Trapezblech-Profilgeometrie fehlt:** Es reicht nicht „Profil/Blechstärke" als Freitext. Für die Stockschraube/Klemme braucht es: Profilhöhe (mm), Sickenabstand/Rippenabstand (Befestigung erfolgt **auf dem Obergurt/der Hochsicke**, nicht im Tal), Blechstärke (≥ 0,4 mm o. ä. für Stockschraube), Trägerart darunter (Holzpfette vs. Stahlpfette → andere Schraube). `MountingRule.bedingung` hat zwar `profilTyp`/`blechstaerkeMin`, aber `PvMountingSystem` modelliert die **Verlegerichtung der Kurzschiene quer zur Trapezrichtung** und den **Befestigungsraster nach Sicke** nicht.
- **Dachhaken-Höhenverstellung/Typ je Ziegelmodell fehlt:** Dachhaken sind nicht universell. Sie müssen zur Ziegelkontur passen (z. B. Frankfurter Pfanne vs. Biberschwanz vs. Doppelmuldenfalz). Der notwendige **Ziegel-Anarbeiten/Flexen** und die **höhenverstellbare vs. starre** Hakenvariante fehlen. Solarhalteziegel als Alternative ist im Konzept genannt, aber die **Kompatibilitätsrelation Ziegelmodell ↔ Solarhalter** ist als `offen` markiert.
- **Stehfalz-Klemmentyp je Falzgeometrie fehlt:** Doppelstehfalz vs. Snap-Falz vs. Klick-Falz erfordern unterschiedliche Klemmen. Nur „Falzklemme" ist zu grob.
- **Sparren-/Pfettenbezug der Befestigung:** `aufSparrenPflicht:boolean` ist vorhanden — gut. Aber der **Befestigungsuntergrund bei Trapez/Sandwich** (Stahlpfette, Z-Pfette) ist nicht abbildbar; dort gibt es keinen Sparren.

### Empfehlung
- `PvMountingSystem` und `MountingRule` wie im Konzept umsetzen, ABER vor Aktivierung um folgende Pflichtattribute erweitern: Trapez-Profilhöhe + Befestigungsraster (Obersicke), Blechstärke-Untergrenze, Trägerart (Holz/Stahl), Falztyp bei Stehfalz, Dachhaken-Variante (starr/höhenverstellbar) + Ziegelmodell-Kompatibilität, Solarhalter↔Ziegel-Relation.
- Die hartcodierte „2 Haken je Modul"-Zeile (`roofModel.ts:243`) darf **nicht** auf Nicht-Ziegel-Eindeckungen angewendet werden — das ist ein systematischer Stücklistenfehler und für Angebote relevant.

---

## Punkt 2 — Modulbelegung

### Befund: Code [HOCH] / Konzept [MITTEL]

**Code-Realität (`pvPlanung.ts` `berechneLayout`):**
- **Modulmaß hartcodiert** auf 1,134 × 1,800 m (`MODUL_BREITE_M`/`MODUL_HOEHE_M`) — der gewählte `modulTypId` aus der UI ändert **nur Leistung/Preis, nicht die Geometrie**. Ein 450-W-Modul (oft ~2,1 × 1,1 m) belegt im Layout dieselbe Fläche wie ein altes 1,8-m-Modul → Modulzahl und kWp können kräftig daneben liegen. [HOCH]
- **Hoch/Quer:** vorhanden und korrekt (nimmt die Variante mit mehr Modulen).
- **Reihen/Spalten:** vorhanden.
- **Modul-/Reihenabstand:** nur **ein** Wert `reihenSpaltM = 0.02` (2 cm) für **beide** Richtungen. Fachlich sind Modulspalt (~15–20 mm, klemmbedingt) und ggf. thermischer/Reihenabstand unterschiedlich; bei aufgeständerten Reihen (Ost/West, Flachdach) braucht es echten **Reihen-/Verschattungsabstand** (Höhe × Faktor). [MITTEL]
- **Randabstände Traufe/First/Ortgang:** Nur **ein umlaufender** `randAbstandM` (default 0,3–0,4 m). Fachlich sind die Ränder **unterschiedlich**: Traufe oft größer (Wartung/Schnee/Absturz), First-Abstand wg. Be-/Entlüftung und Firstziegel, Ortgang wg. **Wind-Eckbereich** (DIN EN 1991-1-4 Randzonen). Ein einziger Wert ist zu grob. [MITTEL]
- **Mehr-Felder-Belegung:** Im Code **nicht vorhanden** — es gibt genau ein rechteckiges Vollfeld pro PV-Hauptfläche. Aussparungen um Störer, mehrere getrennte Felder, unterschiedliche Ausrichtung je Feld: alles nicht da. Das Konzept (`PvModuleLayout.felder[]`, Plan §3.7) sieht es vor — gut, aber ungebaut. [HOCH, weil reale Dächer fast nie ein sauberes Vollrechteck sind]
- **Kopieren/Duplizieren:** weder im Code noch in der UI vorhanden; nur im Konzept. [MITTEL]
- **Nur Süd-/Hauptfläche wird belegt:** `deriveRoofGeometry` markiert nur eine Fläche `isPv:true`. Nord-/Walmflächen, Ost/West-Belegung auf beiden Satteldachseiten sind nicht belegbar. [MITTEL]

**Konzept-Realität:** `PvModuleLayout` mit `felder[]`, `ausrichtung` je Feld, getrennten `randabstaende.{traufe,first,ortgang}` ist fachlich richtig aufgesetzt. Modulabstände aber auch dort nur als `abstaende.{modul,reihe}` ohne Verschattungs-/Windzonenbezug.

### Empfehlung
- **Modulmaße an die Produktdatenbank koppeln** (Länge/Breite/Höhe je Modell aus `Product.attributes`), Hartwerte nur als Fallback. Ohne das ist die Belegungs-/kWp-/Ertragsrechnung nicht angebotssicher.
- Randabstände als **drei getrennte Werte** (Traufe/First/Ortgang) + Pflichthinweis auf Wind-Eckbereich.
- Mehr-Felder + Duplizieren wie im Konzept umsetzen; Modulspalt vom Reihenabstand trennen.

---

## Punkt 3 — Störflächen

### Befund: Code [HOCH] / Konzept [MITTEL]

**Code-Realität (`roofModel.ts` `RoofObject`):**
- Typen vorhanden: nur `gaube | dachfenster | schornstein | schneefang`. Es **fehlen**: Lüfter/Dunstrohr, Antenne/SAT, Solarthermie, Ausstieg/Dachluke, Blitzschutz/Fangstange. [HOCH]
- **Keine Sperrfläche, kein Sicherheitsabstand, keine Verschattung.** `RoofObject` hat nur `u, v, breite, hoehe`. In `RoofScene3D` werden Störer nur als Deko gezeichnet; **Module werden NICHT um sie ausgespart** — Module und Schornstein/Gaube können sich im 3D überlappen. Das ist fachlich der gravierendste Belegungsfehler. [HOCH]
- **Schneefang** ist im Code zugleich „Störer-Typ" und eine pauschale Gitterleiste — fachlich gehört der Schneefang als **durchgehende Traufenlinie** modelliert, nicht als Punktstörer.

**Konzept-Realität:** `RoofObstacle` (Plan §3.5) ist fachlich gut: `sicherheitsabstand`, `sperrflaeche` (für Modul-Kollision), `verschattung.{richtung,laenge,profil}`, vollständige Typliste inkl. Solarthermie/Antenne/Ausstieg. Das ist genau die richtige Richtung.

**Fachliche Lücke auch im Konzept:**
- **Schornstein-Verschattung ist 3D/zeitabhängig**, nicht nur „Richtung+Länge". Der Sperr-/Verschattungskegel eines Kamins wandert über den Tag — ein statisches Polygon je Störer (Plan) ist als Sperrfläche ok, aber für Ertragsminderung zu grob (siehe Punkt 5).
- **Wartungs-/Feuerwehr-Laufweg** und **Mindestabstand Modulfeld ↔ First/Ortgang aus Brandschutz** (länderspezifisch, z. B. 0,5 m / Brandwand) fehlen als eigene Regel.
- **Dachfenster:** Schwenkbereich/Öffnungsraum beim Dachfenster (kein Modul direkt davor) fehlt.

### Empfehlung
- `RoofObstacle` mit Sperrfläche umsetzen und die **Modul-Aussparung verbindlich** in der Belegung verankern (Modul, das die Sperrfläche schneidet, wird nicht gesetzt → Prüfregel + rote Markierung).
- Typliste auf Lüfter/Antenne/SAT/Solarthermie/Ausstieg/Blitzschutz erweitern. Schneefang als Traufenelement, nicht als Punktstörer.

---

## Punkt 4 — Montage-/Prüfregeln

### Befund: Code [HOCH] / Konzept [MITTEL]

**Code-Realität:** Es gibt **keine** Prüfregeln im PV-Sinn. `validateRoof()` (`roofModel.ts:205`) prüft nur Dach-Plausibilität (Neigung, Überstand, Sparrenabstand, Lattenabstand, Tiefe). Die vom Konzept geforderten Regeln sind **nicht implementiert**:
- „Modul außerhalb Dachfläche" — nicht geprüft (das Vollrechteck wird durch `berechneLayout` zwar in die nutzbare Fläche gerechnet, aber es gibt keinen Check/keine Markierung bei Überschreitung).
- „Haken nicht auf passendem Sparren" — nicht geprüft. Im 3D werden Haken im **Raster `rDist` (Sparrenabstand)** gesetzt, aber ohne Bezug zur tatsächlichen Sparrenlage unter dem Modulfeld; bei Trapez/Stahl gibt es gar keinen Sparren.
- „Schiene ohne Haken / Schiene ohne Auflager" — nicht geprüft.
- „Mindestabstände zu Ortgang/Traufe/First" — nicht als Regel, nur impliziter Randabstand.
- **Kabel-/Wartungswege** — `L.kabelwege` ist eine Deko-Box; kein echter Wegeplan, keine Kollision mit Modulfeld.

**Konzept-Realität:** `CheckResult { regel; schwere; text; bezug }` und die Regel-Aufzählung (Plan §3.7, Lückenliste L-4) sind fachlich richtig benannt.

**Fachliche Lücken auch im Konzept:**
- **Statiknachweis/Lastabtrag fehlt als Regel:** Modulgewicht + Schneelast (Schneelastzone, Geländehöhe) + Windsog (Windzone, Gebäudehöhe, Eckbereich) → daraus folgt der **maximale Haken-/Klemmabstand** und die Ballastmenge beim Flachdach. Ohne diese Eingangsgrößen ist „Haken alle X m" nicht belastbar. [HOCH — sicherheitsrelevant]
- **Klemmbereich des Moduls** (vom Hersteller freigegebene Klemmzonen am Rahmen) bestimmt die Schienenlage — fehlt als harte Regel (`Product.attributes.klemmbereiche` ist im Produktkonzept genannt, aber nicht mit der Schienenposition verknüpft).
- **Potentialausgleich / Erdung / Blitzschutz** (Modulrahmen, Schiene, Trennungsabstand zu vorhandener Blitzschutzanlage) fehlt vollständig — montage- und sicherheitsrelevant.

### Empfehlung
- Prüfregel-Engine wie im Konzept umsetzen; zwingend ergänzen um **Last-/Klemmabstands-Check** (Schnee-/Windzone als Eingabe) und **Klemmbereich-Konformität**. Diese zwei sind sicherheitsrelevant und sollten als **Fehler (rot)**, nicht nur Info, geführt werden.

---

## Punkt 5 — Ertrags-/Auslegungslogik (`pvPlanung.ts`)

### Befund: [HOCH] (für belastbare Angebote zu grob)

`berechnePv` rechnet: `kWp × 1000 kWh/kWp × Ausrichtungsfaktor × Neigungsfaktor`.

**Was plausibel ist:** Größenordnung des spezifischen Ertrags (≈ 1000 kWh/kWp/a für DE-Süd) ist als grober Richtwert vertretbar. Ausrichtungsfaktoren (Süd 1,0; O/W 0,85; Nord 0,6) und die Neigungs-Glockenkurve um 32° sind als grobe Näherung akzeptabel.

**Was fachlich fehlt / zu grob ist:**
- **Standortabhängigkeit (Globalstrahlung):** Der Basiswert 1000 ist bundesweit fix. Real reicht DE von ~950 (Norden) bis ~1150+ kWh/kWp (Süden). Die PLZ→Globalstrahlung-Quelle (`PLZ_Solar...` im Import-Konzept) ist vorhanden, aber **nicht angebunden**. Die Google-Solar-Abfrage liefert `bestConfigYearlyKwh`, wird aber **nicht** in die Ertragsrechnung übernommen — sie steht nur als Infotext daneben. [HOCH]
- **Verschattung:** Geht mit **0** in die Rechnung ein. Kein Horizont, kein Nahschatten (Kamin/Gaube/Nachbargebäude/Baum). Bei realen Dächern ein zweistelliger Prozentfehler. [HOCH]
- **Temperaturverhalten / PR (Performance Ratio):** Es gibt **kein PR** (typ. 0,80–0,88) und keinen Temperaturkoeffizienten. Der Faktor-Stack rechnet effektiv mit PR ≈ 1,0 → **systematische Überschätzung** des Ertrags. [HOCH — angebotsrelevant]
- **Wechselrichter-Dimensionierung fehlt komplett:** Kein WR im Modell, keine kWp/kW-AC-Ratio (Überbelegung typ. 1,0–1,2), keine AC-Kappung. Damit ist die Anlage nicht auslegbar. [HOCH]
- **String-/MPPT-Planung fehlt:** Keine Modulanzahl je String, keine Stringspannung gegen WR-MPP-Fenster, keine Kälte-Leerlaufspannung (Umpp/Uoc × Tempkorrektur) vs. WR-Max-DC. Das ist **die** elektrotechnische Kernauslegung und sicherheitsrelevant (DC-Überspannung). [HOCH]
- **Mismatch durch gemischte Ausrichtung** (Ost/West an einem MPPT) nicht berücksichtigt.
- **Eigenverbrauch/Speicher/Autarkie** kommen im Ertragskern nicht vor (sind aber Kern eines PV-Angebots).

### Empfehlung
- Ertragskern um **PR-Faktor** und **standortabhängige Globalstrahlung** (PLZ-Quelle oder Google-Solar-Wert übernehmen) erweitern — sonst werden Erträge systematisch zu hoch ausgewiesen. Das ist das wichtigste Einzelthema dieses Berichts.
- WR-Dimensionierung (kWp/kW-AC, MPPT-Zahl) und String-Plausibilität (Modulzahl/String gegen WR-Spannungsfenster inkl. Kältekorrektur) als eigene Stufe. Bis dahin Ertrag klar als „Richtwert ohne Verschattung/PR" kennzeichnen (der Disclaimer auf der Seite tut das teilweise — gut, aber PR/Standort sollten benannt werden).

---

## Punkt 6 — Datenfelder Module / Wechselrichter / Speicher / UK (Produktdatenbank)

### Befund: Konzept [MITTEL] / Code [HOCH, weil real nur 4 hartcodierte Module]

**Code-Realität:** `MODUL_TYPEN` = 4 Hartwerte mit nur `wattp, wirkungsgrad, preis`. **Keine** Maße, kein Gewicht, kein Glas/Glas, kein Temperaturkoeffizient, keine Klemmbereiche. WR und Speicher existieren im Code **gar nicht**. [HOCH]

**Konzept-Realität (Produktkonzept §B):** Sehr gut und weitgehend vollständig — `Product` mit kategorie-spezifischen `attributes`, Provenienz, Vollständigkeitsstatus, `freigabe.hersteller`, `asset3dUrl`. Kategorien decken Modul/WR/Speicher/UK/Eindeckung/Zubehör vollständig ab.

**Fachlich fehlende Pflichtfelder (auch im Konzept):**
- **PV-Modul:** Temperaturkoeffizient Pmpp (%/K), Uoc/Isc/Umpp/Impp (für Stringauslegung — kritisch!), max. Systemspannung (1000/1500 V), **mechanische Lastzonen** (Druck/Sog in Pa) und **freigegebene Klemmbereiche** (sind in Plan §4a/Produktkonzept erwähnt, aber nicht als Pflicht-Attribut mit Stringbezug). Bifazialität.
- **Wechselrichter:** MPP-Fenster (U_min/U_max), max. DC-Strom je MPPT, max. Eingangs-DC-Leistung, max. Strangzahl je MPPT, AC-Nennleistung + max. Scheinleistung, NA-Schutz/Norm (VDE-AR-N 4105), Notstrom/Backup-fähig, IP-Schutzart. Konzept nennt MPPT/Strings/Stromgrenzen — gut, aber Spannungsfenster + Strom je MPPT als **Pflicht** betonen.
- **Speicher:** nutzbare vs. Brutto-kWh (da), C-Rate/Lade-Entladeleistung (da), Batteriechemie (LFP/NMC), Zyklenzahl/Garantie, Notstromfähigkeit, Kopplung AC/DC — **Chemie/Zyklen/Notstrom fehlen** im Konzept-Stichwort.
- **UK/Montagesystem:** **Systemstatik/-zulassung (allg. bauaufsichtliche Zulassung/abZ), max. Schienen-Kragarm, max. Schienenstützweite, zulässige Modulrahmenhöhe (Klemmbereich), Korrosionsklasse** — Plan §3.6 hat `maxKragarm`, aber Zulassung/Stützweite/Korrosion fehlen.

### Empfehlung
- Beim Ausmodellieren der `attributes` je Kategorie die oben genannten **elektrischen Kennwerte (Uoc/Umpp/Isc/Tempkoeff.) für Module** und **MPP-Fenster/Strom je MPPT für WR** als Pflicht aufnehmen — ohne sie ist keine String-/WR-Auslegung möglich. Speicher: Chemie + Notstrom ergänzen. UK: Zulassung + Stützweite ergänzen.

---

## Punkt 7 — Fehlende Norm-/Herstellerdaten + Risiken

### Befund: [HOCH]

- **Wind- und Schneelastzonen fehlen vollständig.** Im Code keine Spur; im Konzept nur als Stichwort `ballast.windlastzone` (Flachdach) und `MountingRule … neigungMin/Max`. Für **jedes** Dach (nicht nur Flachdach) bestimmen Windzone (1–4), Geländekategorie, Gebäudehöhe und **Eckbereich** den maximalen Befestigungsabstand und die Sogsicherung. Ohne das ist die UK-Auslegung nicht normkonform (DIN EN 1991-1-4 / DIN EN 1991-1-3). **Sicherheitsrelevant.** [HOCH]
- **Systemfreigaben/Herstellerzulassungen:** `freigabe.hersteller` ist im Datenmodell vorgesehen — gut — aber als Pflicht-Gate für die Montagesystem-Empfehlung noch nicht verankert. Risiko: System wird vorgeschlagen, das der Modul-/Ziegelhersteller **nicht freigegeben** hat (Garantieverlust).
- **Regeldachneigung je Eindeckung:** Im Konzept als `offen` markiert (Mindest ≠ Regel). `validateRoof` warnt nur grob ab < 22°. Bei Unterschreitung der Regeldachneigung ohne Zusatzmaßnahmen (Unterdach) → Undichtigkeit. Fachlich korrekt als offen erkannt, aber Risiko bleibt bis zur Datenpflege.
- **Brandschutz-Abstände** (Modul ↔ Brandwand/First, Länderbauordnung) fehlen ganz.
- **Provenienz/Lizenz** (Produktkonzept §0/§I): sehr gut adressiert — keine erfundenen Herstellerdaten, Quarantäne, `offen`-Markierung. Aus Installateur-Sicht genau richtig, da falsche Ziegelmaße/Deckmaße direkt zu falscher UK-/Hakenwahl führen.

### Empfehlung
- Wind-/Schneelastzone als **Pflicht-Eingabe** (oder aus PLZ ableiten) und in die Befestigungs-/Ballastregel einspeisen, bevor irgendeine UK-Empfehlung als „freigegeben" gilt.
- Herstellerfreigabe als **hartes Gate** vor der Montagesystem-Empfehlung; ohne Freigabe nur Vorschlag mit Hinweis „Freigabe prüfen" (Konzept §E sieht das im Verhalten schon vor — konsequent umsetzen).

---

## „Fehlt im Modell / Code"-Liste (kompakt)

**Im Code real fehlend (nicht nur ungebaut, sondern Bestand falsch/zu grob):**
1. Unterkonstruktion als Objekt — Haken/Schiene rein dekorativ, eindeckungsunabhängig. „2 Haken je Modul" für **alle** Eindeckungen (falsch für Trapez/Falz/Flach). [HOCH]
2. Modulmaße folgen **nicht** dem gewählten Modul (1,134×1,8 m hartcodiert) → Belegung/kWp unzuverlässig. [HOCH]
3. Getrennte Randabstände Traufe/First/Ortgang; getrennter Modul- vs. Reihenabstand. [MITTEL]
4. Mehr-Felder-Belegung, Belegung mehrerer Dachflächen, Kopieren/Duplizieren. [HOCH/MITTEL]
5. Störflächen mit Sperrfläche + verbindlicher Modul-Aussparung; fehlende Störer-Typen (Lüfter/Antenne/SAT/Solarthermie/Ausstieg/Blitzschutz). [HOCH]
6. PV-Prüfregeln (außerhalb Fläche / Haken auf Sparren / Schiene ohne Haken / Mindestabstände). [HOCH]
7. WR + Speicher existieren im Code gar nicht; keine String-/MPPT-Auslegung. [HOCH]
8. Ertrag ohne PR, ohne Temperatur, ohne Verschattung, ohne Standort-Globalstrahlung; Google-Solar-Wert nicht in die Rechnung übernommen. [HOCH]

**Im Konzept noch zu ergänzen (Konzept gut, aber fachlich lückenhaft):**
9. Trapez-Profilgeometrie (Profilhöhe, Sickenraster, Trägerart Holz/Stahl); Falztyp bei Stehfalz; Dachhaken-Variante + Ziegelmodell-Kompatibilität; Solarhalter↔Ziegel-Relation. [MITTEL]
10. Wind-/Schneelastzonen als durchgängige Eingangsgröße (nicht nur Flachdach-Ballast) → max. Befestigungsabstand. [HOCH, sicherheitsrelevant]
11. Modul-Klemmbereiche mit Schienenlage verknüpfen; UK-Zulassung/Stützweite/Korrosionsklasse als Pflicht. [MITTEL/HOCH]
12. Elektrische Modulkennwerte (Uoc/Umpp/Isc/Impp/Tempkoeff.) und WR-MPP-Fenster/Strom je MPPT als Pflichtattribute. [HOCH]
13. Potentialausgleich/Erdung/Blitzschutz-Trennungsabstand; Brandschutzabstände First/Ortgang/Brandwand; Wartungs-/Feuerwehrweg. [MITTEL, teils sicherheitsrelevant]

---

## Offene Daten (vom Inhaber / aus Datenblättern zu liefern)
- Wind-/Schneelastzonenkarte bzw. PLZ→Zone-Tabelle (für Befestigungs-/Ballastregel).
- Standort-Globalstrahlung (PLZ-Quelle ist vorhanden, muss angebunden werden) **oder** verbindliche Übernahme des Google-Solar-Werts.
- Modul-Datenblätter: Maße, Gewicht, Uoc/Umpp/Isc/Impp, Tempkoeff., max. Systemspannung, mech. Lastzonen, Klemmbereiche.
- WR-Datenblätter: MPP-Fenster, max. DC-Strom/MPPT, max. DC-Leistung, AC-Nennleistung, NA-Schutz-Norm.
- UK-Herstellerunterlagen: Systemzulassung (abZ), max. Stützweite/Kragarm, Korrosionsklasse, Freigabe je Eindeckung/Ziegelmodell.
- Ziegel-Deckmaße/Lattenabstände (im Braas-Import als `offen` erkannt — korrekt) für Haken-/Solarhalter-Anpassung.
- Solarhalteziegel↔Ziegelmodell-Kompatibilität + Herstellerfreigaben.

---

## Fazit / Empfehlung (PV-Installateur-Sicht)

**Konzept:** Richtung stimmt, fachlich überdurchschnittlich (Provenienz, Quarantäne, MountingRule mit Begründung+Quelle, `RoofObstacle` mit Sperrfläche). **Freigabe mit Auflagen** für das Konzept: vor Umsetzung um Wind-/Schneelast, elektrische Modul-/WR-Kennwerte, Trapez-Profilgeometrie, Klemmbereich-Bezug und PR/Standort im Ertrag ergänzen.

**Code:** Aktuell eine brauchbare 3D-**Visualisierung mit Richtwert-Ertrag**, aber **keine montagereife Auslegung**. Drei Befunde sind angebots-/sicherheitskritisch und sollten Priorität haben:
- (A) Modulmaße aus echtem Produkt statt Hartwert → sonst falsche kWp/Stückliste.
- (B) Ertrag ohne PR/Standort überschätzt systematisch → Angebotsrisiko.
- (C) UK eindeckungsunabhängig + „2 Haken je Modul" pauschal → falsche Stückliste, falsche Montagelogik; bei Last-/Windbezug sicherheitsrelevant.

**Keine** der Punkte ist ein Abbruchgrund (kein produktiver Schaden, reine Planungs-/Konzeptebene), aber A–C sollten vor jeder echten Angebots-/Montagenutzung geschlossen werden.

---
*Erstellt 2026-06-12 · PV-Installateur-Fachprüfung · NUR Analyse, keine Codeänderung.*
