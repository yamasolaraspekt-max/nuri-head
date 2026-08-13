# REGISTER DER WERKZEUGE

> Eine Zeile je Werkzeug. **Hier steht, was es gibt und woran es hängt** —
> nicht, wie weit es ist. Der Auftragszustand steht in `docs/STATUS.md`.
>
> Reifegrad: `LEER` (nur Ordner) · **`n/7 BLÄTTER`** (Zwischenstufe: teilweise gefüllt — zählt
> **NICHT** als `BESCHRIEBEN`; die Zahl nennt, wie viele stehen) · `BESCHRIEBEN` (**alle sieben**
> Blätter gefüllt) · `GEBAUT` (Code vorhanden) · `GEPRÜFT` (Kriterien grün, Grenzen belegt)
>
> **`ENTWORFEN`** (Yamas Entscheidung 12.08.) — **alle sieben Blätter gefüllt, aber als VORGABE
> statt als Ablesung.** *Für Werkzeuge, deren Code noch nicht existiert (Klasse C).*
>
> ```text
> BESCHRIEBEN   die Blaetter LESEN den vorhandenen Code ab.   Quelle: der Bestand.
> ENTWORFEN     die Blaetter GEBEN VOR, was gebaut werden soll. Quelle: Vertrag,
>               Fachregel oder Zielbild — KEIN Code.
> -> zwei PARALLELE Stufen, keine aufeinanderfolgenden. Beide fuehren zu GEBAUT.
> -> Der Abschlusszaehler zaehlt NUR 'BESCHRIEBEN'. ENTWORFEN wird NICHT gezaehlt:
>    sonst misst die Zahl Plaene statt Messungen (H-3).
> -> BEIM UEBERGANG ENTWORFEN -> GEBAUT ist zu pruefen, ob die VORGABE mit der
>    ABLESUNG uebereinstimmt. Ein Entwurf, der gebaut wurde und danach nicht
>    nachgemessen wird, ist eine unbelegte Behauptung ueber den eigenen Code.
> ```
>
> *Die Zwischenstufe trägt das Wort `BESCHRIEBEN` absichtlich NICHT: der Abschlussbefehl
> `grep -cE '^\| W-[0-9]+ .*BESCHRIEBEN'` würde es sonst mitzählen, und der Zähler soll nur
> Vollständiges zählen (Yamas Bedingung vom 11.08.).*
>
> ---
>
> **WER DEN REIFEGRAD ZIEHT — Planner-Festlegung 13.08., auf Yamas „ok".**
>
> Der Reifegrad ist **kein Auftragszustand** (Satz 2 dieses Kopfes) und gehört deshalb in
> **kein Auftragskriterium**. Er wird vom **Planner** nachgezogen, sobald ein Blatt-Auftrag
> `ABGENOMMEN` ist — als Pflege dieser Planungsquelle, nicht als Arbeitsschritt des Generators.
>
> **Der Anlass, damit es niemand für Bürokratie hält:** W-06 war am 13.08. abgenommen, die
> sieben Blätter standen (468 Z., Bau `a05d6d12`, Abnahme `8f213aa0`) — und diese Zeile sagte
> weiter `LEER`. Der Generator hat es gesehen und korrekt **nicht** angefasst: *„KEIN Kriterium
> und KEIN Scope-Punkt nennt das Register"* (`W-06-geschoss-verwalten.md:319`). Nachgemessen
> galt dasselbe für **alle acht** damals offenen Ablesungsaufträge — W-03/1, W-10/1, W-12/1,
> W-14/1, W-16/1, W-18/1, W-31, W-37: **null** von acht nannten das Register in einem Kriterium.
> Sie hätten vollständig durchlaufen können, ohne dass die Zahl sich bewegt. **Der Mangel lag
> in den Auftragsblättern des Planners, nicht in der Ausführung** — und er wird hier oben
> behoben statt in acht laufenden Blättern, weil ein Blatt zu ändern, an dem gerade gearbeitet
> wird, den Boden unter dem Bearbeiter verschiebt.

---

## Stufe 1 — Fundament (alles andere hängt daran)

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-01 | Raster und Fang | **BESCHRIEBEN** | — | F-040 ✓, F-041 ✓, F-001 ✓, F-003 ✓, ~~F-004~~ ⓝ · **KEIN Werkzeug in der Registry, und es braucht auch keins** (planner 13.08., klargestellt auf Punkt 3 der Frontend-Bestandsaufnahme des Release-Prüfers). Sein eigenes Blatt zieht die Grenze wörtlich — `W-01-raster-und-fang/7-GRENZEN.md:17`: *„Als Werkzeug aufgerufen werden | in der `toolRegistry` gibt es keinen Eintrag für Raster/Fang | **der Fang liegt unter anderen Werkzeugen, er ist keines**"*. Der Code sagt dasselbe: `geometry/fangKern.ts:1-6` — *„reine Funktion ohne Konva/three"*, *„Eine Wahrheit: es gibt genau EINE Fang-Entscheidung"*, mit der Rangfolge Endpunkt > Ortho > Raster. Aufrufer: `app/HausplanerApp.tsx` und `app/tools/werkzeugEnde.ts`. **Der Satz steht hier, weil die Spalte sonst „BESCHRIEBEN, 0 GEBAUT" heißt und jemand ein Fang-Werkzeug baut, das es nicht geben darf** — Infrastruktur wird nicht bedient, sie wirkt. |
| W-02 | Wand zeichnen | **BESCHRIEBEN** | W-01 | **F-001 ✓** (`wallGeometry.ts:14` `Math.hypot`), **F-002 ✓** (`:46` `Math.atan2`, auch `:305-306`), **F-030 ✓** (`:60` Wand = gefülltes Band der Breite `thickness`, `:74`, `:181`) — **hier ist F-030 RICHTIG**; von hier wurde die Paarung zu W-09/W-21 kopiert, wo sie nicht passt |
| W-13 | Auswahl und Griffe | **BESCHRIEBEN** | W-02 | **keine** ⓝ (~~F-012~~, ~~F-003~~) |
| W-12 | Ansicht und Kamera | LEER | — | F-032 |

## Stufe 2 — Grundriss

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-03 | Wand bearbeiten | LEER | W-02, W-13 | F-003, F-004, F-030 |
| W-04 | Öffnung (Tür/Fenster) | **BESCHRIEBEN** | W-02 | **keine** ⓝ (~~F-001 ✓, F-002 ✓~~, ~~F-031~~) | · **F-ZUORDNUNG BERICHTIGT vom planner 13.08.** Der Generator meldete, W-04 enthalte **keine Rechnung** (`Math.` 0× in `oeffnungsBauarten.ts`/`oeffnungsTypen.ts`) — **das war zu eng gemessen:** diese zwei sind die **Katalogmodule**, und das Werkzeug hat fünf. `geometry/wallGeometry.ts` (317 Z.) rechnet **15×** mit `Math.`, `wandFlaeche.ts` 2×. **Seine Schlussfolgerung trägt trotzdem, nur anders begründet:** F-003 (Lotfußpunkt) und F-031 (CSG-Differenz) haben über **alle fünf** Module **0 Treffer** — sie werden nicht benutzt und sind gestrichen. **Belegt sind stattdessen F-001 und F-002**, je selbst geöffnet: F-001 Abstand an `wallGeometry.ts:14` und `:87` (`Math.hypot`), F-002 Richtungswinkel an `:46`, `:305`, `:306` (`Math.atan2`). Die Spalte beschreibt nach `LIESMICH.md:69` was **benutzt** wird — also das Ist, nicht das Soll; eine falsche Nummer behauptet eine Benutzung, die es nicht gibt.
| W-05 | Raum erkennen | **BESCHRIEBEN** | W-02 | **F-010 ✓ + F-011 ✓ in EINER Funktion** — `roomDetection.ts:70` `signierteFlaeche()` gibt `summe/2` **mit Vorzeichen**: das Vorzeichen ist die Orientierung, der Betrag die Fläche (`:38 flaecheMm2`), **F-001 ✓** (`:88`, `:95` `Math.hypot`), F-012 ⚠, F-013 ⚠ · **fehlte in der Spalte: F-002 ✓** (`:120` `Math.atan2`, Halbkanten-Sortierung) |
| W-10 | Decke und Boden | LEER | W-05 | F-011, F-030 |
| W-16 | Grundriss unterlegen | LEER | W-12 | F-032 |

## Stufe 3 — Aufbau in der Höhe

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-06 | Geschoss verwalten | **BESCHRIEBEN** | W-02 | F-032 · **Reifegrad vom Planner nachgezogen 13.08.** (LEER → BESCHRIEBEN). Beleg selbst gezählt: die sieben Blätter stehen in `W-06-geschoss-verwalten/` — `1-ZWECK` 101 Z., `2-FUNKTION` 89, `3-FORMELN` 59, `4-BEDIENUNG` 77, `6-PRUEFUNG` 70, `7-GRENZEN` 72, Ordner `5-CODE` gefüllt (468 Z. gesamt). Bau `a05d6d12`, Abnahme durch den Evaluator `8f213aa0` über acht Kriterien. **Die F-Spalte ist NICHT angefasst:** F-032 steht bei W-06, W-12, W-14 und W-16, das Auftragsblatt nennt diese Häufung ausdrücklich als Falle (`:208-211`) — ich habe sie hier nicht nachgemessen und ziehe deshalb nur den Reifegrad. |
| W-07 | **Dach aus Kontur** | **BESCHRIEBEN** | W-05, W-06 | F-010 ✓ (`dachAusschnitt.ts:103,154` `Math.sign(area)` — **nicht** in `dachGeometrie.ts:46`, dort `Math.abs`), F-013 ✓ (`dachAusschnitt.ts:136` Außenwinkelsumme ±2π statt Kantenschnitt-Test), **F-014 ⚠** (Konvexität wird geprüft, die **Erkennung** einspringender Ecken fehlt — die V1-Grenze **lehnt ab statt zu erkennen**), **F-025 ✓** (grat 49 · kehle 57 · ortgang 12 Treffer), **F-026 ✓**, ~~F-020~~ ⓝ **0 Treffer** (`skelett` in allen acht Modulen), ~~F-021~~ ⓝ (kein Skelett zum Anheben), F-022 ✓ Grad→Radiant (`PI/180`, 4 Module) · **Prozent nicht gefunden** |
| W-08 | Dachfläche messen | **BESCHRIEBEN** | W-07 | **F-011 ✓** (`polygonFlaeche.ts:44-46` Shoelace + `Math.abs(summe)/2`), F-023 ⚠, F-024 ⚠ |
| W-09 | Treppe | **BESCHRIEBEN** | W-06 | **F-001 ✓** (`treppe2D.ts:47`, `treppeSvg.ts:125` — `Math.hypot(dx,dy)`), ~~F-030~~ ⓝ **0 Treffer in allen sieben Treppen-Modulen** · Rechenkern sind **normative Größen**, keine Geometrieformel |
| W-21 | **Sparren und Lattung** | **BESCHRIEBEN** | W-07 | **N-001…N-003** ✓ (~~F-001~~, ~~F-030~~ ⓝ) · Quelle M-01/**M-02 ungelesen** | · **ZUORDNUNG ENTSCHIEDEN vom planner 13.08., und die „fehlende Nummer" EXISTIERT SCHON.** Der Generator meldete zu Recht: die zwei genannten F-Nummern stehen nicht im Code (kein `Math.hypot`/`sqrt` in den fünf Modulen, nichts wird extrudiert) — **und das ist stimmig**, drei der fünf Module aggregieren aus einer bereits erzeugten Liste. Er meldete weiter, `bodenschneelast()` und `formbeiwertSchnee()` rechnen, seien aber **normativ** (DIN EN 1991-1-3 / EN 1995-1-1), und *„eine erfundene F-Nummer wäre schlimmer als die gemeldete Lücke"*. **Er hat recht — und es muss nichts erfunden werden:** die Sammlung führt eine eigene **N-Reihe**, und sie deckt die Sache genau. Selbst gemessen: `FORMELSAMMLUNG.md:627` **N-001 · Charakteristische Bodenschneelast sₖ 🟢** ↔ `geometry/sparrenBerechnung.ts:33 bodenschneelast()`; `:649` **N-002 · Formbeiwert μ₁ für Schneelast 🟢** ↔ `sparrenBerechnung.ts:45 formbeiwertSchnee()`. **Damit trägt W-21 N-001 und N-002** — dieselbe Praxis wie W-37, das N-001…N-003 als Normbezug führt. Die F-Spalte bleibt leer, weil W-21 keine Geometrieformel benutzt. **Offen bleibt EIN Punkt, und er ist keine Zuordnungsfrage:** M-02 (2.021 Zeilen) ist nicht ausgewertet — das ist ein Quellen-Posten in `04-QUELLEN`, kein Registereintrag.
| W-22 | **Gaube** | **BESCHRIEBEN** | W-07 | **F-027** ✓ (Thema ja, Formel ⚠), ~~F-031~~ ⓝ | · **F-027 ENTSCHIEDEN vom planner 13.08.: thematisch richtig, als BENUTZTE Formel gestrichen.** Der Generator hat sie Merkmal für Merkmal gegen den Bau gehalten; ich habe beide Seiten selbst geöffnet. `FORMELSAMMLUNG.md:781-791` verlangt `rise = d · tan(φ)` (Anstieg aus der **Tiefe d**), einen **`Quader(b, h+Sockeltiefe, d)`** und `Drehung um y = atan2(fallRichtungₓ, fallRichtungz)`. Gemessen liefert das Modul **Dreiecke und Linien statt eines Quaders**, rechnet mit `halfW` statt `d`, hat **kein `atan2`** (Ausrichtung über ein lokales Dreibein) und andere Vorgaben. **Und der Grund liegt in der Herkunft:** F-027s Belegstelle ist `dachdecker_pro_3d.tsx` — **selbst gemessen: 0 Treffer im Repo**, die Datei ist M-01 auf Yamas Rechner. **Die Formel beschreibt also einen anderen Bau, nicht den ticket-Bau.** Da die Spalte nach `LIESMICH.md:69` beschreibt, was **benutzt** wird, steht F-027 hier nicht als ✓, sondern als Normbezug-Vermerk: *gleiche Sache, andere Konstruktion.* Wer aus F-027 eine Vorgabe für ticket macht, baut den M-01-Bau nach. **Und `auswechslung.ts` (174 Z.) ist meiner Entscheidung nach in W-21 zuhause, nicht in W-22:** ein Wechselholz ist **Tragwerk**, und seine Verbraucher sind mehrere (Gaube W-22, Dachdurchdringungen W-29). Ein Modul, das mehrere Werkzeuge brauchen, gehört zum Fundament und nicht zu einem Verbraucher — dieselbe Logik wie beim Fang (W-01). **Die Blatt-Erweiterung von W-21 ist ein eigener Auftrag**, keine Registerzeile: W-21 ist `BESCHRIEBEN`, und ein zusätzliches Modul berührt `1-ZWECK`, `2-FUNKTION` und `5-CODE`. · **DER WERKBANK-BEFUND IST GEPRÜFT UND KLEINER ALS GEMELDET (planner 13.08.).** Der Generator meldete, das Thema Dachaufbauten bestehe aus **fünf Modulen mit 975 Zeilen** und die Werkbank führe nur „Gaube". **Selbst nachgezählt stimmen die Zahlen exakt:** `gaubeGeometrie` 498 Z./26 Exporte · `aufbauPlatzierung` 190/13 · `auswechslung` 174/5 · `aufbauOrientierung` 61/4 · `aufbautenStatus` 52/5. **Aber W-22s `5-CODE/LIESMICH.md` führt ALLE FÜNF** — der Befund trifft also den **Namen** des Werkzeugs, nicht die Modulliste. **Was wirklich offen war, war genau ein Modul:** `auswechslung.ts`, von zwei Blättern als „in keinem Blatt zuhause" gemeldet — zugeordnet und als **W-21/2** geschnitten. Und `wandaufbau.ts` ist kein Fremdkörper: `5-CODE:43` benennt es selbst als Namensfall — *heißt „Aufbau", ist aber Wandaufbau/U-Wert* — und verweist nach **W-02**. **Bleibt die Namensfrage:** die Zeile heißt „Gaube", der Gegenstand sind **Dachaufbauten** (fünf Module, 975 Z.). Das ist eine Benennung und kein Bau — wer nach „Dachfenster" oder „Kamin" sucht, findet dieses Werkzeug unter „Gaube" nicht. · **VORARBEIT FÜR DEN BEDIEN-SCHNITT, gemessen am 13.08. und NICHT geschnitten** (fünf Aufträge stehen bereit; die Grundlage steht hier, damit der nächste Schnitt sie nicht neu erheben muss). **Die Lage ist dieselbe wie bei W-05: die Engine läuft, die Bedienung fehlt.** `geometry/aufbauPlatzierung.ts` (190 Z., 13 Exporte) ist *„reine, testbare Hilfslogik zur FLÄCHENABHÄNGIGEN Platzierung von Standard-Aufbauten (Kamin/Dachfenster/Lüfter/Sat/Gaube/Lichtkuppel)"* und prüft die Wünsche **gegen die echte Dachflächengeometrie** — kein zu breiter Aufbau auf schmaler Walmfläche. Sie hat **fünf Produktivaufrufer** (`dachAusschnitt`, `dachformVorlagen`, `dachOeffnung`, `linienBauteile`, `domain/scene.types`), aber alle in der Geometrieschicht. **Was fehlt, ist genau ein Registry-Eintrag:** `toolRegistry.ts` hat **0** Treffer für `gaube`. **Und die erwartete Designfrage stellt sich NICHT:** der Release-Prüfer hatte gewarnt, Werkzeugsymbol und Panel-Ort brauchten den Styleguide — gemessen liegen **111** Icons in `public/hausplaner/icons/tools/`, darunter **`gaube.svg` und `dachfenster.svg`**, und `werkzeugGruppen.ts:99 iconPfad()` löst über die **id** auf (*„seit AUF-31 heißt jede Datei wie ihre deutsche id"*). Kein neues Asset, keine Designentscheidung. **Muster für den Zuschnitt vorhanden:** W-35s `ConfigWizard` führt **vier** Arten in EINEM Dialog (`KonfigArt`) — ein Aufbau-Werkzeug für die sechs Arten wäre derselbe Weg und keine Erfindung.
| W-23 | **Deckung und Material** | **BESCHRIEBEN** | W-07, W-08 | **F-050** 🟡, **F-053** 🟡 — Bereich aus der Quelle, Wert aus der Rechnung |

## Stufe 4 — Darstellung und Ausgabe

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-11 | Maß und Bemaßung | **BESCHRIEBEN** | ~~W-13~~ ⓝ | F-001 ✓, ~~F-002~~ ⓝ, ~~F-003~~ ⓝ | · **BEIDE BEFUNDE BESTÄTIGT UND BERICHTIGT (planner 13.08.), je selbst nachgemessen.** **(1) F-002 und F-003 gestrichen:** `atan2` und `lotAufGerade` kommen in `bemassung.ts`, `masskette.ts` und `masseingabe.ts` **null mal** vor. Belegt bleibt **F-001** (`Math.hypot`/`sqrt` je 1× in `bemassung.ts` und `masseingabe.ts`) — Bemassung misst **Abstände**, keine Richtungen und keine Lotfußpunkte. **(2) Die Abhängigkeit „braucht W-13" gestrichen:** `auswahl`/`select`/`markiert` kommen in `bemassung.ts` und `masskette.ts` **null mal** vor, und die Signatur `bemassung(waende, oeffnungen, toleranz)` (`:52-55`) hat **keinen Auswahl-Parameter** — sie bekommt *alle* Wände und *alle* Öffnungen. Ein Werkzeug, das alles bemaßt, braucht keine Auswahl.
| W-14 | Kopieren/Spiegeln/Drehen | LEER | W-13 | F-032 |
| W-15 | Material und Farbe | **ENTWORFEN** | W-13 | **keine** ⓝ — zugewiesen wird, nicht gerechnet |
| W-17 | Export und Speichern | LEER | alle | — |
| W-43 | **Abbund-Zeichnung** | LEER | **W-07, W-21** | — · Darstellungslogik liegt in **M-02**, Fachwissen im Feld **`abbundhinweis`** ([dachformVorlagen.ts:107](../../../../resources/planner/hausplaner/geometry/dachformVorlagen.ts#L107), 2.399 Z) — **die Insel kennt ihre eigene Lücke**: `:2051` *„Geplant — Abbund erst nach sauberer Geometrie-/Tragwerksumsetzung."* **Ausschluss mit Zieladresse, nicht schneiden** (Yama 12.08.) |

## Stufe 5 — Prüfung und Auswertung

| Nr | Werkzeug | Reifegrad | Braucht | Formeln |
|---|---|---|---|---|
| W-18 | Topologie prüfen | LEER | W-02, W-05 | F-004, F-013 |
| W-19 | Sonne und Verschattung | LEER | W-07, W-08 | F-024 |
| W-20 | Stückliste und Mengen | **BESCHRIEBEN** | W-05, W-08 | F-011, F-023 — **heute keine benutzt**: Summe über die echte Liste, keine Formel |

---

## Stufe 5b — Zielbild-Ergänzungen (Yamas Zielbild 11.08., eingetragen 12.08.)

> **Neun Zeilen für zehn Zielbild-Lücken.** *Messgrundlage:
> `docs/VORLAGE-REGISTERZEILEN-L1-L10.md`. **Sechs der zehn „Lücken" sind gebauter Code ohne
> Registerzeile** — die Fundstelle steht deshalb in der Zeile, damit niemand nachbaut.*
> `LEER` heißt hier **„kein Blatt gefüllt"**, nicht „kein Code vorhanden".

| Nr | Werkzeug | Reifegrad | Braucht | Formeln / Fundstelle |
|---|---|---|---|---|
| W-24 | **Fundament und Bodenplatte** | LEER | W-05 | ungeprüft — Registry-Werkzeug `Bodenplatte`, **kein Geometriemodul** |
| W-25 | **Pfetten und Kehlbalken** | LEER | W-07, W-21 | ungeprüft — `dachformVorlagen`, `holzBauteile`; Registry `Pfette` |
| W-26 | **Dachschichten (Aufbau)** | LEER | W-07 | ungeprüft — **kein Modul**; `konterlattungMm` in `dachformVorlagen` ist ein **toter Vertrag** |
| W-27 | **Dachkantentypen** First·Grat·Kehle·Traufe·Ortgang | **GEBAUT** | W-07 | **F-025**, **F-026** — **die ECKEN-Erkennung ist gebaut** (W-27/1): `geometry/dachTopologie.ts` (183 Z.) + 11 Tests, drei Fangproben **gefahren** und gefallen. **ABER OHNE AUFRUFER, und das gehoert in diese Zeile** (planner 12.08., an drei Mustern gemessen): `analyzeTopology` hat **0** Aufrufe ausserhalb der Tests, **0** Stellen erzeugen `EdgeTopologyConfig`, **0** Module importieren `dachTopologie`. Das Tor sagt es selbst: *steht im Produktivcode gilt erst mit genanntem AUFRUFER*. **Und heute kann es keinen geben:** `dachGeometrie.ts:88-92` wirft `DachGeometrieUngueltig` fuer jede Kontur, die nicht ihrer Bounding-Box entspricht — also genau fuer die L-Formen, an denen `analyzeTopology` seine Innenecken erkennt. **Das ist kein Fehlbau, sondern ein Vorbau** nach demselben Muster wie `dachVerschneidung.ts` (dessen Kopf sich ausdruecklich als *reine, testbare Geometrie* und *Regressionsschloss* bezeichnet) — es steht hier, damit niemand `GEBAUT` als *wirkt* liest. **Der Anschluss ist nicht blockiert:** Yama hat A-01s Nicht-Ziel *keine L/T/U-Daecher* aufgehoben (*es stammt aus Unwissen ueber die Faehigkeit*), und `BERICHT-A-05-l-kontur.md` hat die Lueckenliste gemessen — Punkt 6 ist als **A-10** erledigt, Punkt 7 laeuft als **A-24**, die Punkte 1 bis 5 brauchen einen Zuschnitt und Punkt 8 ist unterbestimmt und gehoert Yama. Ortganglänge und Grat/Kehle **am Sparren** gibt es weiterhin getrennt (`dachformVorlagen.ts:291`, `schifterListe.ts:58`) — die Namensgrenze steht im Dateikopf |
| W-28 | **Dachentwässerung** | LEER | W-07, W-27 | ungeprüft — `linienBauteile` führt `'dachrinne'` als Linientyp; Bemessung fehlt |
| W-29 | **Dachdurchdringungen** Dachfenster·Kamin·Lüfter·Lichtkuppel | LEER | W-07, W-21 | ungeprüft — **stark gebaut**: `dachOeffnung`, `dachAusschnitt`, `auswechslung`, `aufbauPlatzierung`, `aufbautenStatus` |
| W-30 | **Flachdach-Aufbau** Gefälle·Attika·Abläufe | LEER | W-07 | ungeprüft — `dachformVorlagen` (`attika`, `svgFlach`) |
| W-31 | **PV-Belegung (vollständig)** | LEER | W-07, W-08, W-19 | **gesperrt bis F-028 🟢** — autarke Schnellstufe gebaut (`pvBelegung.ts`, **kein Azimut** → kein F-028-Fall) |
| W-32 | **Giebelwand-Bindung** `Wall.topConstraint` | LEER | W-02, W-03, W-07 | ungeprüft — **kein Modul**, 0 Treffer auf `topconstraint` |

*L-6 und L-8 sind in **W-29** zusammengefasst: dieselben Module bedienen Dachfenster, Kamin, Lüfter
und Lichtkuppel. **Die Trennlinie zieht der Code selbst** — `aufbauOrientierung` unterscheidet
**stehende** Aufbauten (Gaube, Kamin → W-22) von Durchdringungen.*

---

## Stufe 6 — Führung

> **Yamas Einordnung 12.08.: dieselbe Tafel, kein Sonderweg, dieselben Reifegrade.** *Was diese
> Werkzeuge von einem Wandwerkzeug unterscheidet, ist nicht ihre Art, sondern ihre
> **Abhängigkeitsrichtung**: sie benutzen viele Werkzeuge, statt von einem zu hängen — wie `W-17`,
> das schon „braucht: alle" trägt.*
>
> **Erhoben: 1.593 Zeilen in acht Bausteinen, bisher ohne eine einzige Registerzeile.** *Die größte
> Anschlusslücke, die diese Tafel hatte. Messbericht:
> `docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md`.*

| Nr | Werkzeug | Reifegrad | Braucht | Formeln / Fundstelle |
|---|---|---|---|---|
| W-33 | **Start und Projektwahl** | **BESCHRIEBEN** | alle | `app/StartView.tsx` (267 Z) ⓝ — **vier Komponenten, ein Export**; drei halten einen eigenen `hover`-Zustand. **Behebt eine Falschauskunft**: der Bildschirm zeigte erfundene Projekte bei jedem Nutzer (`startEhrlich`). **Der Leerzustand ist der Normalfall** (`:206`). Die Projektkachel ist ein **Verweis**, keine Schaltfläche (`:142`) · acht Wächter, davon **zwei rendernde** (`projektKlick`, `elevationTokens`) |
| W-34 | **Geführte Planung** (Stepper, elf Schritte) | **BESCHRIEBEN** | **W-38** *(Typquelle, `GuidedView.tsx:4`)* | **keine** ⓝ — gezählt, nicht gerechnet: 17 Zählungen über `zaehle()`; `statusAus` `fahrschritte.ts:43-49` · **6 der 11 Schritte haben keine Modellgrundlage** |
| W-35 | **Konfigurator-Dialog** Fenster·Tür·Treppe·**Heizkörper** | **BESCHRIEBEN** | W-04, W-09 | `app/ConfigWizard.tsx` (271 Z) ⓝ — **VIER Arten** (`:23`), fünf Schritte (`:34`), **`TYP_MAP` (`:43`)** bildet sie auf `ConfiguratorType` ab · **schreibt sehr wohl ins Gebäudemodell**: `ADD_NODE` in `:184`, `:205`, `:226` — der Schreibpfad selbst ist W-42 · sechs Wächter, davon `konfiguratorEhrlich` (11 Tests) |
| W-36 | **Fähigkeiten-Navigation** | **BESCHRIEBEN** | alle | `app/FaehigkeitenNavi.tsx` (76 Z) + `app/tools/faehigkeiten.ts` (129 Z) ⓝ — **die DRITTE von vier Statusachsen**: `FaehigkeitZustand` an der `Faehigkeit` (`:25`). **25 Fähigkeiten aus drei Quellen**, die dritte seit I4 leer; **13 Engines, 8 angeschlossen**. `doppelteIds` (`:116`) sichert „EINE Wahrheit"; der **Guard-Test** (`faehigkeiten.test.ts:38`) importiert jedes Engine-Modul **wirklich** und prüft den Export |
| W-37 | **Rechenpanels (Engine-Flächen)** | LEER | N-001…N-003 | **N-003** — `app/EngineFlaeche.tsx` (196 Z) + `app/dashboard/enginePanels.ts`; trägt die **A-14**-Ausgabeauflage |
| W-38 | **Schritt-Status und Prüfpunkte** | **BESCHRIEBEN** | alle *(Richtung, keine Vorbedingung)* | **keine** ⓝ — rein deklarativ: **0 Funktionen, 0 Importe**; `app/studioDaten.ts:163` trägt `SchrittStatus` mit **vier** Stufen, `:255` die vier Beschriftungen |
| W-39 | **Studio-Rahmen** | **BESCHRIEBEN** | **13 Module** *(benutzt, nicht besessen — 8 ohne eigenes Werkzeug)* | **keine** ⓝ — **ADDITIV**: die `HausplanerApp` bleibt unverändert, einziger Eingriff ist das Flag `imStudio` (`:140`); drei Modi in `:131/:132/:133`, der dritte heißt `imExperte` (`:85`) |
| W-40 | **Gültigkeitsstatus** `checked`·`approved`·`outdated` + `blocked` | **BESCHRIEBEN** | W-38 | `geometry/configuratorPackage.ts` ⓝ — **zwei Achsen an ZWEI TRÄGERN**: Fortschritt am **Schritt** (W-38, `studioDaten.ts:163`), Gültigkeit am **Paket** (`:72`). **Drei der vier Stufen sind gebaut und geprüft** (`:26`, Übergänge `:103-111`, `kannIntegrieren` `:120`); **`blocked` ist die einzige Erweiterung — 0 Treffer auf der Insel**. Ohne `approved` ist „PV erst nach **bestätigter** Geometrie" nicht prüfbar |
| W-41 | **Abhängigkeitsgraph / Invalidierung** | **ENTWORFEN** | W-38, W-40 | **keine** ⓝ — **kein Code**; „Änderungen propagieren, **niemals** stille Löschung" · **die dünnste Vorgabe**: die Quelle führt den Graphen selbst unter *nicht gemessen*. Von sechs Kandidat-Kanten ist **eine belegt** — Dachfläche → PV-Belegung (`geometry/pvBelegung.ts:10-14`) |
| W-42 | **Schreibpfad Wizard → Gebäudemodell** | **BESCHRIEBEN** | W-35 | **keine** ⓝ — **der Pfad IST gebaut**: drei `executeCommand({type:'ADD_NODE'})` in `ConfigWizard.tsx:184/205/226`, vier Bauteilarten, bewacht von `configWizardWrite.test.ts`. Der Dateikopf `:6` („nächste Scheibe") und der Bericht („schreibt NICHTS") sind **überholt** — beide suchten `BuildingDocument`, das dort 0 Mal vorkommt |

---

## Bekannt, außerhalb des Umfangs — Fachgewerke und Infrastruktur

> **Vertretungsentscheid `4c241a6c` (Yama-Übergabe 12.08.), Einarbeitung beim Planner:**
> *„Die Werkbank bleibt auf **Architektur/Rohbau/Dach** begrenzt … sie in die W-Reihe zu pressen
> würde die Zuordnungsfehler multiplizieren, die diese Runde dreimal hatte. **Aber sichtbar statt
> unsichtbar.**"*
>
> **Diese Module sind gebaut, geprüft und im Betrieb — sie haben nur keine W-Zeile, und das ist eine
> Entscheidung, kein Versehen.** *Eigene Kennung `FG-` (Fachgewerk) bzw. `IN-` (Infrastruktur), damit
> sie später beschrieben werden können, ohne die W-Reihe zu verwässern.*

### Fachgewerke — eigene Normlage, eigenes Handwerk

| Kennung | Modul | Zeilen | Gewerk | Normlage |
|---|---|---|---|---|
| `FG-01` | `wandaufbau.ts` | 72 | Bauphysik | DIN EN ISO 6946 · GEG |
| `FG-02` | `abwassergefaelle.ts` | 58 | Sanitär/Entwässerung | DIN 1986-100 |
| `FG-03` | `fbhAuslegung.ts` | 75 | Heizung (Flächenheizung) | — *(Grenze selbst benannt)* |
| `FG-04` | `heizkreisVerteiler.ts` | 58 | Heizung (Hydraulik) | — *(Grenze selbst benannt)* |
| `FG-05` | `heizkoerperLeistung.ts` | 65 | Heizung (Heizkörper) | ungeprüft |
| `FG-06` | `heizkoerperTypen.ts` | 25 | Heizung (Katalog) | ungeprüft |
| `FG-07` | `kuecheArbeitsdreieck.ts` | 51 | Küchenergonomie | DIN 18022 *(Komfort)* |
| | **Summe** | **404** | | |

### Infrastruktur — kein Werkzeug, sondern Unterbau

| Kennung | Modul | Zeilen | Aufgabe |
|---|---|---|---|
| `IN-01` | `integrationAbgleich.ts` | 135 | Abgleich zwischen Teilsystemen |
| `IN-02` | `freigabe.ts` | 135 | Freigabelogik |
| `IN-03` | `geschossVorlage.ts` | 78 | Vorlagen für Geschosse |
| `IN-04` | `dachVorlage.ts` | 34 | Vorlagen für Dächer |
| | **Summe** | **382** | |

**Zur Differenz gegenüber dem Entscheid, ausdrücklich (B6):**

```text
Der Entscheid nennt 407 Z Fachgewerke und 305 Z Infrastruktur.
MEINE Messung: 404 Z und 382 Z.
-> Die MENGE ist anders geschnitten, nicht die Zaehlung falsch. Ich habe je Modul
   die Zeilen selbst gemessen; welche Module der Entscheid in welche Gruppe zaehlt,
   ist mir nicht bekannt und ich rate es nicht.
-> Zwei Module habe ich AUSGESCHLOSSEN, weil sie NICHT heimatlos sind:
     treppeSvg.ts   142 Z   gehoert zu W-09 (eines seiner sieben Module)
     linienBauteile.ts 167 Z gehoert laut Anschlussmatrix zum Dach-Zubehoer
   Waeren sie mitgezaehlt, kaeme eine dritte Summe heraus — deshalb stehen sie hier
   mit Grund, nicht stillschweigend fehlend (H-1).
```

> **Was diese Tabellen NICHT sind:** *keine Aufgabenliste.* **`FG-` und `IN-` begründen keinen
> Auftrag** — sie machen sichtbar, was existiert und bewusst außerhalb der W-Reihe bleibt. *Wer eines
> davon beschreiben will, braucht einen eigenen Auftrag und eine Entscheidung Yamas über den Umfang.*
>
> **Und zwei davon berühren A-15 unmittelbar:** *`FG-01` bis `FG-07` sind sieben der elf Engines, die
> A-15 klassifizieren soll. **Die Kennung hier und die Klassifikation dort sind zwei Sichten auf
> dieselben Dateien** — A-15 fragt „darf sie urteilen?", diese Tabelle fragt „gehört sie in die
> W-Reihe?". Die Antworten sind unabhängig voneinander.*

---

## Abhängigkeitskette — die Reihenfolge, in der gebaut werden muss

```
W-01 Fang ─┬─► W-02 Wand ─┬─► W-03 bearbeiten
           │              ├─► W-04 Öffnung
           │              ├─► W-05 Raum ──┬─► W-10 Decke
           │              │               └─► W-20 Mengen
           │              └─► W-06 Geschoss ─► W-07 DACH ─┬─► W-08 messen
           │                                  │           └─► W-19 Sonne
           │                                  └─► W-09 Treppe
           └─► W-13 Auswahl ─┬─► W-11 Maß
                             ├─► W-14 Kopieren
                             └─► W-15 Material
W-12 Kamera (quer zu allem)
W-17 Speichern (braucht alles)
W-18 Prüfung (braucht W-02, W-05)
```

> **Was diese Kette bedeutet:** W-01 zuerst. Ohne verlässlichen Fang ist jede
> Wand ungenau, und jede Ungenauigkeit vererbt sich nach oben — bis das Dach
> nicht mehr schließt. **Ein wackliger Fang ist kein Schönheitsfehler,
> er ist ein Fundamentfehler.**

---

## Was schon im Repo existiert

Beim Anlegen dieses Registers gemessen — **noch nicht in die Werkzeugordner
eingearbeitet**, das ist der nächste Schritt:

| Fundstelle im Repo | Gehört zu |
|---|---|
| `resources/planner/hausplaner/geometry/dachGeometrie.ts` | W-07 |
| `resources/planner/hausplaner/renderers/three-d/szene.ts` | W-07, Schicht 4 |
| `resources/planner/hausplaner/app/tools/workspaceIds.ts` | Werkzeugregistrierung |
| `resources/planner/hausplaner/geometry/fangKern.ts` | **W-01** — 276 Zeilen, 11 Ausfuhren, 3 Testdateien mit 45 Zusagen; **eingearbeitet 10.08.2026** |
| `resources/planner/hausplaner/geometry/wallGeometry.ts` | **W-02** — 317 Zeilen, 12 Ausfuhren; Azimut-Konvention Spec ▲K2, mm-Integer-Welt; **eingearbeitet 10.08.2026** |
| `resources/planner/hausplaner/geometry/wandFlaeche.ts` | **W-02** — 238 Zeilen, 6 Ausfuhren; AUF-77 Mengenermittlung M1, „entweder Mengen oder Meldungen"; **eingearbeitet 10.08.2026** |
| `resources/planner/hausplaner/geometry/oeffnungsBauarten.ts` | **W-04** — 75 Zeilen, 5 Ausfuhren; 24 Fenster- und 24 Tür-Bauarten, SVGs unter `public/hausplaner/icons/`; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/oeffnungsTypen.ts` | **W-04** — 49 Zeilen, 7 Ausfuhren; 5 Tür- und 7 Fenster-Vorlagen mit Standardmaßen; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/masskette.ts` | **W-11** — 118 Zeilen, 7 Ausfuhren; Rechenschicht, entdoppelt mit Toleranz; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/bemassung.ts` | **W-11** — 108 Zeilen, 6 Ausfuhren; Rechenschicht, innen Öffnungskette / außen Gesamtmaß; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/masseingabe.ts` | **W-11** — 169 Zeilen, 9 Ausfuhren; **EINGABEschicht**, ohne Importe; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/roomDetection.ts` | **W-05** — 190 Zeilen, 4 Ausfuhren; planares Halbkanten-Verfahren, **kein Registry-Werkzeug**, läuft automatisch; **eingearbeitet 11.08.2026** |
| `resources/planner/hausplaner/geometry/sparrenBerechnung.ts` | **W-21** — 131 Zeilen, 7 Ausfuhren; **Eurocode-VORBEMESSUNG**, ersetzt keine prüffähige Statik; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/sparrenTrennung.ts` | **W-21** — 67 Zeilen, 3 Ausfuhren; Trennung an Öffnungen + Sicher-Entscheidung; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/schifterListe.ts` | **W-21** — 152 Zeilen, 9 Ausfuhren; **gemischt** — konstruiert UND aggregiert; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/holzBauteile.ts` | **W-21** — 82 Zeilen, 4 Ausfuhren; trägt `OFFENE_HOLZBAUTEILE` — gebaute Selbstauskunft über die Grenzen; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/holzMengen.ts` | **W-21** — 64 Zeilen, 3 Ausfuhren; Mengen aus der **echten** 3D-Holzliste statt geschätzt; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/gaubeGeometrie.ts` | **W-22** — 498 Zeilen, 26 Ausfuhren; **Gaube, Kamin UND Ampel-Prüfung**; kein Registry-Werkzeug; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/treppenBerechnung.ts` | **W-09** — 114 Zeilen, 6 Ausfuhren; **die einzige Stelle mit DIN 18065 im Code** — sieben Prüfungen, drei davon `fehler`; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/treppenTypen.ts` | **W-09** — 153 Zeilen, 4 Ausfuhren; Katalog der Grundriss-Geometrie; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/treppenBauarten.ts` | **W-09** — 38 Zeilen, 3 Ausfuhren; SVG-Bauarten; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/treppe2D.ts` | **W-09** — 93 Zeilen, 4 Ausfuhren; 2D-Symbol; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/treppe3D.ts` | **W-09** — 74 Zeilen, 4 Ausfuhren; 3D-Körper; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/treppeSvg.ts` | **W-09** — 142 Zeilen, 5 Ausfuhren; maßstäbliche Grundriss-Zeichnung; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/treppeObjekt.ts` | **W-09** — 84 Zeilen, 4 Ausfuhren; Treppe als `ObjectNode`; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/polygonFlaeche.ts` | **W-08** — 48 Zeilen, 2 Ausfuhren; Shoelace in m²; **`0` ist Ergebnis UND Fehlersignal**; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/app/tools/auswahlModus.ts` | **W-13** — 98 Zeilen, 7 Ausfuhren; vier Modi, Modifikator-Vorrang alt vor ctrl/meta vor shift; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/app/tools/trefferSuche.ts` | **W-13** — 75 Zeilen, 4 Ausfuhren; **oben schlägt nah** — Zeichenreihenfolge vor Distanz; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/app/tools/auswahlDarstellung.ts` | **W-13** — 71 Zeilen, 3 Ausfuhren; Zustand als Daten, kein Markup; **eingearbeitet 12.08.2026** |
| `resources/planner/hausplaner/geometry/holzMengen.ts` | **W-20** — 64 Zeilen, 3 Ausfuhren (`:23`, `:29`, `:44`), 6 Testblöcke mit **24** Assertions. **Reparatur einer zweiten Wahrheit**: die Liste schätzte vorher aus dem Rechteck-Rahmen, die Engine zeichnete geclippt (`:5-9`). Schiftsparren zählen als Sparren mit, mit Begründung im Code (`:57-58`). **Einzige Lücke: die Ziegelmenge** — eingearbeitet 12.08. (W-20) |
| `~/Desktop/…/Tabellen/braas_dachziegel_datenbank_v14.xlsx`, Blatt `DB_Produkte` | **W-23** — **QUELLE, kein Code.** 718.574 Byte · `xl/worksheets/sheet11.xml` · 127 Datenzeilen, 48 Spalten. Nutzbar sind **9 Zeilen = 7 Braas-Modelle** mit `Lattmass_min/max_mm` (Sp. 26/27); `Verschiebespiel_mm` (32) ist deren Differenz und damit die Eingangsprüfung. **Alle neun tragen `Quelle_1_URL`.** Eingearbeitet 12.08. (W-23) |
| `resources/planner/hausplaner/app/tools/werkzeugVertrag.ts:874-908` | **W-15** — **VERTRAG, kein Code.** Drei Einträge: `material-aufnehmen`/`PaintCommand` (:874), `material-zuweisen`/`MaterialCommand` (:886), `textur`/`TextureCommand` (:898). Alle drei nennen `services.material.execute(…)` — **das im Repo 0 Treffer hat**. Deshalb `ENTWORFEN` statt `BESCHRIEBEN`; eingearbeitet 12.08. (W-15/1) |
| `resources/planner/hausplaner/app/tools/auswahlUebersicht.ts` | **W-13** — 77 Zeilen, 4 Ausfuhren; Mehrfach-Ansicht, „das Panel darf nicht raten"; **eingearbeitet 12.08.2026** |

## Was aus Yamas eigenem Bestand kommt

Ausgewertet am 07.08.2026 — Einzelheiten in `../05-MATERIALQUELLEN/BESTAND-YAMA.md`.

| Fundstelle | Gehört zu | Was daraus wurde |
|---|---|---|
| `dachdecker_pro_3d.tsx:139-147` | W-07 | **F-014** Erkennung einspringender Ecken |
| `dachdecker_pro_3d.tsx:153-158` | W-07 | **F-025** Grat · Kehle · Ortgang |
| `dachdecker_pro_3d.tsx:101-131, 1134-1137` | W-07 | **F-026** Dach über Grundform — **kann L und T** |
| `dachdecker_pro_3d.tsx:1190-1255` | W-22 | **F-027** Gaubenaufbau |
| `dachdecker_pro_3d.tsx:186-191` | W-23 | **F-050** Materialkennwerte |
| `dachdecker_pro_3d.tsx:194-198` | W-20 | **F-051** Zeitwerte je Gewerk |
| `profi_holzbau_solar_cad.tsx` | W-21 | noch auszuwerten |
| `solarmaster_konstruktion.tsx` | W-19 | noch auszuwerten |
| Pfettendach-Fachbilder | W-21 | Bauteilbenennung |

> **Der wichtigste Befund:** `F-026` kann **L- und T-Grundrisse** — genau der Fall,
> an dem Auftrag Z-07 scheiterte. Der Code liegt auf Yamas Schreibtisch und läuft.
> Vor jedem weiteren Bau an W-07 ist zu entscheiden, welcher der beiden Dachwege
> zuerst kommt. Der Vergleich steht in der Formelsammlung, Gruppe 6.

### F-SPALTE UND ABHÄNGIGKEITEN GEGEN DEN CODE GEMESSEN — Planner 11.08.

**Grundlage:** `ARBEITSREGELN:102` — *der Planner ist Eigentümer von Spezifikationsfehlern.* Der
Generator hat zweimal gemeldet statt korrigiert (`a44e5fdd`, `0299e5ca`) und ausdrücklich
zurückgegeben, dass die Zuordnung dem Planner gehört. **Claim `d0adbec5`, gesetzt vor der ersten
Änderung.**

```text
ZEICHEN
  ✓   am Code BELEGT, mit Fundstelle unten
  ⓝ   am Code NICHT belegt -> durchgestrichen. Die Formel bleibt gueltig, sie steht
      nur am falschen Werkzeug. NICHTS ist aus der Formelsammlung geloescht.
  ohne Zeichen  UNGEPRUEFT — kein Blatt benennt das Modul, also ist die Zuordnung
      nicht messbar. Ich habe sie NICHT geraten (13 der 23 Zeilen).
```

**Die Messungen, je Formel einmal, in `resources/planner/hausplaner/`:**

```text
F-001  Abstand         Math.hypot|Math.sqrt   fangKern 4x ✓ · bemassung 1x ✓ ·
                                              masseingabe 1x ✓ · (14 Module insgesamt)
F-002  Winkel          Math.atan2             dachAusschnitt · roomDetection · wallGeometry
                                              -> in W-11s DREI Modulen: 0x  ⓝ
F-003  Lotfusspunkt    lotAufGerade|lotFuss   NUR fangKern (W-01) ✓
                                              -> W-04: 0x · W-11: 0x · W-13: 0x  ⓝ
F-004  Schnittpunkt    schnittpunkt           schifterListe:71 · wallGeometry:62,106
                                              -> fangKern (W-01): 0x  ⓝ
F-012  Punkt-in-Polygon punktInPolygon|strahl szene.ts · dachAusschnitt
                                              -> W-13s trefferSuche: Math. 0x  ⓝ
F-027  Gaubenaufbau    Math.tan               gaubeGeometrie 6x ✓
F-031  CSG-Differenz   csg|CSG                NUR EIN Treffer, und er ist ein KOMMENTAR:
                                              dachAusschnitt.ts:10 " * - Stufe C (NICHT
                                              hier): echte Polygonloch-/CSG-Operationen"
                                              -> in der ganzen Insel NICHT gebaut  ⓝ
F-040  Raster          raster|snap            fangKern ✓        F-041  fangKern ✓
```

**Die falsche ABHÄNGIGKEIT, und sie ist der schwerste der Befunde:**

```text
"W-11 braucht W-13"   auswahl|select|markiert in bemassung.ts + masskette.ts:  0x
                      bemassung() hat keinen Auswahl-Parameter
                      die einzige Aufrufstelle uebergibt ALLE Waende und ALLE Oeffnungen
                      -> die Abhaengigkeit traegt nicht, gestrichen
```

> **Warum die Abhängigkeit schwerer wiegt als jede F-Nummer:** *Eine falsche Formelangabe führt
> beim Schreiben eines Blattes in die Irre — ärgerlich, aber der Bauende merkt es (und hat es
> gemerkt).* **Eine falsche Abhängigkeit steuert die REIHENFOLGE: sie sperrt ein Werkzeug hinter
> ein anderes, das es nicht braucht.** *Das ist strukturell dieselbe Klasse wie die erfundene
> §3-Sperre, die ich heute in vier eigenen Blättern gemeldet und korrigiert habe
> (`docs/MELDUNG-ERFUNDENE-SPERRE-A-12.md`) — nur an einem Ort, der **jede künftige Planung**
> steuert statt nur vier Blätter.*

**Drei verschiedene Ursachen, kein einheitliches Muster — meine erste Hypothese ist gefallen:**

```text
W-04   das Modul ist ein KATALOG (Math. 0x). Ein Katalog braucht keine Formel.
W-11   die Formeln wurden nach THEMA zugeordnet ("Bemassung braucht Winkel und Lot")
       statt am Modul gemessen. F-001 stimmt, F-002/F-003 nicht.
W-13   das Modul liegt in app/tools (Schicht 3), nicht in geometry (Schicht 2).
       Ein Anwendungswerkzeug RUFT Formeln, es enthaelt sie nicht.
```

> *Ich hatte gehofft, die Schicht erklärt alles — sie tut es nicht: **W-04 und W-11 liegen beide in
> `geometry` und tragen trotzdem falsche Zuordnungen.** Die Hypothese fällt, und ich schreibe sie
> als gefallen hin, statt die drei Fälle in ein Muster zu pressen, in das sie nicht passen.*

**Was ausdrücklich NICHT geändert ist:**

```text
KEINE Formel geloescht, keine umgeschrieben — FORMELSAMMLUNG.md ist nicht im Scope.
KEIN Reifegrad angefasst (die Spalte gehoert dem Generator).
13 der 23 Zeilen UNGEPRUEFT gelassen, weil kein Blatt ihr Modul benennt.
Die alten Generator-Befunde (unten) bleiben stehen — sie sind der Anlass und
werden nicht durch meine Korrektur unsichtbar gemacht.
```

> ⚠ **W-04s F-Zuordnung stimmt nicht mit dem Code überein.** Das Register nennt F-003 und F-031; gemessen enthalten `oeffnungsBauarten.ts` und `oeffnungsTypen.ts` **keine Rechnung** — `Math.` kommt in beiden **null mal** vor, die einzigen Operationen sind `Array.find()` und `??`. **Der Generator ändert die Zuordnung nicht** (sie gehört dem Planner) und meldet sie als Befund. *Siehe `1-ZWECK`/`3-FORMELN` des Werkzeugs.*

> ⚠ **W-11s Registerangaben halten der Messung nicht stand — zwei Stellen.** (1) **F-002 und F-003 stehen nicht im Code:** kein `atan2`, kein `lotAufGerade` in den drei Modulen; nur F-001 ist belegt (`bemassung.ts:77`, `masseingabe.ts:58`). (2) **Die Abhängigkeit „braucht W-13" trägt nicht:** `auswahl`/`select`/`markiert` kommen in `bemassung.ts` und `masskette.ts` **null mal** vor, `bemassung()` hat keinen Auswahl-Parameter, und die einzige Aufrufstelle übergibt alle Wände und alle Öffnungen. **Der Generator ändert beides nicht** — die Zuordnung gehört dem Planner.

> ⚠ **W-21: beide genannten F-Nummern stehen nicht im Code.** `Math.hypot`/`Math.sqrt` kommen in **keinem** der fünf Module vor (F-001), und es wird nichts extrudiert (F-030) — die Stäbe kommen fertig aus der 3D-Engine. **Das ist stimmig:** drei der fünf Module aggregieren aus einer bereits erzeugten Liste und brauchen keine Geometrieformel. **Umgekehrt fehlt eine Nummer:** `bodenschneelast()` und `formbeiwertSchnee()` rechnen, sind aber **normative** Größen (DIN EN 1991-1-3 / EN 1995-1-1) — die Sammlung kennt sie zu Recht nicht. *Eine erfundene F-Nummer wäre schlimmer als die gemeldete Lücke.* **Und M-02 (2.021 Zeilen) ist nicht ausgewertet.** Der Generator ändert nichts davon.

> ⚠ **W-22, F-027: die ZUORDNUNG stimmt, die FORMEL deckt sich nicht mit dem Bau.** Der Planner hat F-027 als ✓ bestätigt — thematisch zu Recht, das Modul setzt genau diese Gauben auf eine Dachfläche. **Der Generator hat die Formel selbst geprüft, Merkmal für Merkmal, und sie beschreibt einen anderen Bau.** F-031 (CSG-Differenz) ist bereits gestrichen und das deckt sich: **0 Treffer** im Modul. F-027 wurde Merkmal für Merkmal geprüft: der trigonometrische Kern ist da (`Math.tan` 6×), aber mit **anderem Operanden** (`halfW` statt `d`), **kein Quader** (das Modul liefert Dreiecke und Linien), **kein `Math.atan2`** (Ausrichtung über ein lokales Dreibein) und **andere Vorgaben** (5°/2° statt 15°). **F-027s Belegstelle ist `dachdecker_pro_3d.tsx` — M-01 auf Yamas Desktop, nicht der ticket-Code.** *Der trigonometrische Kern ist derselbe, weil Trigonometrie derselbe ist; die Konstruktion ist es nicht.* Der Generator ändert die Zuordnung nicht.

> **Dazu ein Befund zur Werkbank selbst:** das Thema Dachaufbauten besteht aus **fünf Modulen mit 975 Zeilen** (`gaubeGeometrie` 498, `aufbauPlatzierung` 190, `auswechslung` 174, `aufbauOrientierung` 61, `aufbautenStatus` 52 — selbst nachgezählt). Die Werkbank führt davon nur „Gaube", und **`auswechslung.ts` (174 Z) wird in W-21 und W-22 als Nachbar genannt und ist in keinem von beiden zuhause.**

> ⚠ **W-08: F-023 und F-024 stehen nicht im Code.** F-023 (wahre Dachfläche aus Grundfläche) ist ein **alternativer Weg** — das Modul misst direkt in der geneigten Ebene und braucht keine Neigung. F-024 (Azimut einer Dachfläche) liegt in `wallGeometry.ts`; dieses Modul kennt **keine** Ausrichtung. F-011 ist belegt (`polygonFlaeche.ts:44` und `:46`).

> **Und ein Befund, der über W-08 hinausgeht: die Schuhbandformel ist DREIMAL umgesetzt, zwei Fassungen heißen gleich.** `polygonFlaecheM2()` in `polygonFlaeche.ts:31` erwartet **Meter**; `polygonFlaecheM2()` in `app/Services/Heizlast/GeometrieAbleitungService.php:118` erwartet **Millimeter** und teilt durch 1.000.000. Dazu `signierteFlaeche()` in `roomDetection.ts:70` (mm², mit Vorzeichen, W-05). *Wer die ersten beiden verwechselt, irrt um den Faktor eine Million — und beide liefern eine Zahl, die aussieht wie eine Fläche.* Zusätzlich: die TS-Fassung prüft jeden Punkt mit `Number.isFinite`, die PHP-Fassung hat keine solche Prüfung.

> ✓ **W-13: das Register sagt bereits „keine" — die Messung BESTAETIGT das und korrigiert nichts.** In allen vier Modulen wird genau eine Sache gerechnet: `toleranzInWelt(pixel, zoom)` (`trefferSuche.ts:72-74`), eine **Einheitenumrechnung**. Sie hat keine Nummer in der Sammlung und braucht keine — *eine Division ist keine Formel, die man nachschlägt*. Nach `W-13/1-3` als **Befund gemeldet, nicht eingetragen**. Abstände werden hier nicht berechnet: `TrefferKandidat` bringt seine `distanz` mit.

> **Und: W-13 ist das einzige Klasse-A-Werkzeug MIT Registry-Eintrag** (`app/tools/toolRegistry.ts:39`, `id: 'auswahl'`) — bei W-01, W-05, W-08, W-21 und W-22 steht die Rechenschicht ohne Werkzeugschicht. **Dafür hat es NULL dedizierte Zusagen** (W-01: 2, W-02: 1) bei 321 Zeilen; erwähnend sind zwei Testdateien mit 36 Zusagen. *Messweise steht im `6-PRUEFUNG`-Blatt, damit die Zahl nachrechenbar ist.*

> ⚠ **W-09: das Register nennt ZWEI Formeln — mein erster Satz hier war falsch.** Die Zeile oben führt `F-001, F-030`, und mein Bau hat genau diese Zeile angefasst. **Gemessen:** F-001 steht im Code, aber **nicht in der Auslegung** — `treppe2D.ts:47` und `treppeSvg.ts:125` (je `Math.hypot`), `treppenBerechnung.ts` **0 Treffer**; F-030 ist als Aufruf nicht messbar. **Die Auslegungsschicht rechnet ohne Geometrieformel** — und nur für sie gilt der Satz. Gemessen sind die Rechenregeln **normative Größen** (Schrittmaß `:75`, Bequemlichkeit `:76`, Sicherheit `:77`, Grenzmaße `:52-55`, lichte Höhe `:58`) — **keine Geometrieformel, die man nachschlägt.** *Eine leere Formelspalte ist besser als eine geratene; nach `603eddc2`, wo sieben von zehn Zuordnungen fielen, ist das die Lehre und keine Bequemlichkeit.*
