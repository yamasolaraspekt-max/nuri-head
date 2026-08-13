# Fahrplan Werkzeugkasten — alle 42 Zeilen, eine Reihenfolge, messbare Abschlüsse

```yaml
art: "Fahrplan des Planners ueber die GANZE Tafel. Ersetzt FAHRPLAN-KLASSE-A.md."
erstellt: "12.08."
basis_sha: 21972085
anlass: "Yamas Frage: 'warum erstellst du dir dafuer nicht den Fahrplan'. Berechtigt."
verhaeltnis_zu_yamas_liste: "Yamas Reihenfolge (A-13, SELECTs, W-07N, Extraktoren, M-02,
   Bruecke, Klasse A, Barrieren) ist die AUFTRAGSLISTE und hat Vorrang. Dieser Fahrplan
   ist die WERKZEUG-Landkarte: welches Werkzeug wann, und was es entsperrt.
   ZWEI Ebenen, keine zwei Wahrheiten — dieser Plan ordnet keinen Auftrag um."
```

## Warum der alte Fahrplan versagt hat — und es ist ein Bauartfehler, kein Versehen

**`FAHRPLAN-KLASSE-A.md` war ein Runden-Plan für zehn Werkzeuge. `W-09 Treppe` passte in keine der
drei Runden. Ich habe daraufhin eine Zeile geschrieben:**

```text
FAHRPLAN-KLASSE-A.md:148   "NICHT IN A   W-09 (Treppe, 698 Z) — war nie in den drei Runden"
```

> **Ich habe die Lücke notiert und den Plan nicht geändert.** *Und weil sie notiert war, sah sie
> erledigt aus — sie stand ja da.* **Eine Notiz über eine Lücke ist kein Plan für die Lücke.** *Das
> ist derselbe Mechanismus wie bei `konterlattungMm` und bei `auswechslung.ts`: benannt, in zwei
> Blättern erwähnt, in keinem zuhause.*
>
> **Die Bauartfolge: ein Plan, der eine feste Zahl von Runden hat, wirft alles aus, was nicht in die
> Runden passt.** *Dieser Fahrplan hat deshalb keine Runden, sondern **Stufen mit Eintrittsbedingung**
> — und eine Zeile, die in keine Stufe passt, ist ein Befund gegen den Plan, nicht gegen das
> Werkzeug.*

## Vollerhebung — Menge definiert, Summe daneben (B6)

```text
MENGE     alle Zeilen '^| W-[0-9]+ |' in docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md

STAND 10.08. (diese Zaehlung, historisch — NICHT loeschen, sie datiert den Fortschritt):
SUMME     42 Zeilen
  BESCHRIEBEN      7   W-01 W-02 W-04 W-05 W-11 W-21 W-22
  6/7 BLAETTER     1   W-07
  LEER            34

STAND 13.08., HEUTE NACHGEMESSEN (Reifegrad-Spalte je Zeile ausgezaehlt):
SUMME     43 Zeilen
  BESCHRIEBEN     21
  LEER            19
  ENTWORFEN        2
  GEBAUT           1

  -> BESCHRIEBEN von 7 auf 21, LEER von 34 auf 19. In drei Tagen.
  -> Und das ist der Grund, warum diese Zaehlung ein Datum braucht: wer mit der
     Zahl 34 plant, haelt die Restarbeit fuer fast doppelt so gross wie sie ist.
     Yamas DAUERregel „Postenlisten nur aus frischer Messung" gilt auch fuer die
     Zaehlungen in diesem Dokument — die alte Zahl war am 10.08. richtig.

  ACHTUNG BEI DER LESART VON 'LEER', und das hat mich am 13.08. selbst erwischt:
  die Spalte heisst REIFEGRAD und meint die SIEBEN BLAETTER, nicht den Code.
  REGISTER.md:6 definiert LEER als „nur Ordner", und :87 sagt es woertlich:
  „LEER heisst hier kein Blatt gefuellt, nicht kein Code vorhanden."
  Die Gegenprobe steht in derselben Tabelle: mehrere LEER-Zeilen tragen im
  Nachbarfeld „stark gebaut" (W-29) oder nennen Module mit Zeilenzahl (W-37:
  app/EngineFlaeche.tsx, 196 Z.). Eine Spalte, die LEER sagt und daneben
  „stark gebaut", kann nicht den Code meinen.
```

## Klassifikation — NEU, mit drei Korrekturen an der Fassung vom 10.08.

```text
KORREKTUR 1   W-17 Export und Speichern: von C nach A.
              VIER dedizierte Module gemessen — paketSpeichern · arbeitsbereichSpeicher ·
              schienenSpeicher · speicherAnzeige. Das ist eintragen, nicht bauen.
KORREKTUR 2   W-09 Treppe gehoert zu A und war nie eingeplant. Sieben Module, 698 Zeilen —
              nach W-07 das zweitgroesste Werkzeug der Tafel.
KORREKTUR 3   die 19 neuen Zeilen (W-24…W-42) sind noch nicht klassifiziert. Sechs von
              zehn Zielbild-"Luecken" und sieben der zehn Fuehrungszeilen sind GEBAUTER
              Code ohne Blatt — also A, nicht C.
WARNUNG       ein WORTGLEICHHEITSFALL, der die Klassifikation fast verdorben haette:
              bewerteDeckung() existiert — in heizkoerperLeistung.ts:53, als
              LEISTUNGSdeckung eines Heizkoerpers, NICHT als Dachdeckung.
              Haette ich den Treffer fuer W-23 gezaehlt, waere C um eins zu klein gewesen.
```

**Die drei Klassen, neu belegt:**

```text
KLASSE A  EINTRAGEN — Code existiert, Blatt fehlt
  fertig      W-01 W-02 W-04 W-05 W-11 W-21 W-22                        (7)
  im Bau      W-07 (6/7, W-07N geschnitten) · W-08 · W-13               (3, Blaetter da)
  NICHT       W-09 Treppe                                               (1, KEIN Blatt)
  GESCHNITTEN
  neu dazu    W-17 · W-25 W-29 W-30 W-31 · W-33…W-39                    (12, keine Blaetter)
KLASSE B  ANSCHLIESSEN — teils gebaut, Anschluss unklar
  W-03 W-06 W-10 W-12 W-14 W-16 W-18 · W-24 W-26 W-28                   (10)
KLASSE C  BAUEN — kein Modul, gemessen
  W-15 Material/Farbe · W-19 Sonne/Verschattung · W-20 Mengen (nur Holz
  vorhanden) · W-23 Deckung/Material · W-27 Kantentypen · W-32
  Giebelbindung · W-40 W-41 W-42 (Fuehrung)                             (9)
```


---

## ⚑ MESSUNG 13.08. — DREI FUNDAMENTE SCHALTEN 14 WERKZEUGE FREI. Und W-03 ist keine Klasse C

**Fund:** `resources/planner/hausplaner/app/tools/werkzeugLandkarte.ts` (211 Z., aus AUF-50 Stufe 1)
führt **je Vertrag eine Marke**, ob das Gebäudemodell den Werkzeug-Befehl heute leistet. Gemessen:
**41 `deckt` · 21 `fehlt` · 43 `ohne-modell` · 6 `stillgelegt` = 111**, und `WERKZEUG_LANDKARTE.length`
ist per Test an `WERKZEUG_VERTRAEGE.length` gekoppelt (`__tests__/werkzeugLandkarte.test.ts:53`,
**12 Tests grün, selbst gefahren**).

> **Was diese Datei ist und was nicht:** *sie ist eine **Selbstauskunft**, keine unabhängige Messung —
> H-6, Wort ≠ Beleg. Deshalb habe ich ihre zwei Kernbehauptungen **am Code nachgemessen**, bevor ich
> darauf plane (unten). Und sie beantwortet eine **andere Frage als Klasse B**: „braucht es einen neuen
> **Modellbefehl**", nicht „ist das **Werkzeug** gebaut". Wer sie als Bau-Stand liest, liest sie falsch.*

**Die 21 `fehlt`-Marken fallen in drei Cluster — und zwei davon sind EIN Bau, nicht sechs:**

```text
CLUSTER 1 — es fehlt EIN Befehl, der MEHRERE Knoten in EINEM umkehrbaren Schritt aendert
  betroffen: ausrichten (:62) · verteilen (:74) · teilen (:76) · verbinden (:78)
             erkennung-bestaetigen (:110)                              = FUENF
  AM CODE NACHGEMESSEN, nicht uebernommen:
    MOVE_NODE   commands/applyCommand.ts:176  nimmt EIN command.nodeId
    UPDATE_NODE                        :208  nimmt EIN command.nodeId
    die einzigen Mehrfach-Befehle sind SET_NODES_SICHTBAR (:231) und
    SET_NODES_GESPERRT (:239) — beide setzen nur FLAGS, keine Geometrie.
  -> Die Behauptung der Landkarte traegt. Ein Fundament, fuenf Werkzeuge.

CLUSTER 2 — es fehlen OBJEKTTYPEN, sonst deckt ADD_NODE schon
  betroffen: pumpe (:152) · leuchte (:165) · schalter (:166) · steckdose (:167)
             verteiler (:168) · pv-modul (:170)                        = SECHS
  AM CODE NACHGEMESSEN: domain/scene.types.ts:178 fuehrt ELF Werte —
    radiator, heat_pump_indoor, heat_pump_outdoor, buffer_tank,
    hot_water_tank, battery, inverter, wallbox, furniture, sanitary, stair.
    Keine Pumpe, keine Leuchte, kein Schalter, keine Steckdose, kein
    Verteiler, kein PV-Modul. inverter und battery sind da — genau wie die
    Landkarte bei pv-modul schreibt.
  -> Die Behauptung traegt. Aber: ein neuer objectType ist eine SCHEMA-
     Entscheidung und geht nach den Schutzgrenzen NICHT still durch.
     Das ist eine Vorlage an Yama, kein Auftrag den ich schneide.

CLUSTER 3 — BERICHTIGT AM 13.08., meine erste Fassung war FALSCH
  betroffen: trimmen (:77) · verlaengern (:79) · versatz (:80)         = DREI

  ICH HABE ZUERST GESCHRIEBEN: „die Geometrie IST DA, es fehlt nur der
  ANSCHLUSS, das ist Klasse B und sofort machbar." DAS TRAEGT NICHT. Ich hatte
  die Landkarten-Begruendungen ABGESCHNITTEN gelesen (cut auf 158 Zeichen) und
  die genannten Funktionen NICHT GEOEFFNET — Pflichtpruefung 7 verlangt genau
  das, „mindestens eine Stelle geoeffnet". Nachgemessen:

  (a) versetzteWand ist KEIN Parallelversatz, sondern eine TRANSLATION.
      geometry/editierGeometrie.ts:20 — der Rumpf ist
        { start: versetzePunkt(start,dx,dy), end: versetzePunkt(end,dx,dy) }
      also BEIDE Endpunkte um DENSELBEN Vektor. Der Doc-Kommentar in :19 sagt
      es selbst: „Wand-Endpunkte um (dx,dy) versetzen (bewegen/duplizieren mit
      Versatz)". Ein Parallelversatz erzeugt eine Wand im SENKRECHTEN Abstand d
      und braucht die Normale — andere Rechnung.
      -> Die Landkarten-Begruendung bei versatz nennt die FALSCHE Funktion.
         Ursache: H-9. „versetzen" (bewegen) und „Versatz" (offset) sind
         dasselbe Wort und zwei Sachen.

  (b) EINE AUFRUFBARE GERADENSCHNITT-FUNKTION EXISTIERT NICHT.
      Gegenprobe: grep auf exportierte schnitt/intersect/kreuz-Funktionen im
      ganzen Hausplaner liefert nur berechneAusschnitt und flaechenBilanz
      (geometry/dachAusschnitt.ts) — Dachausschnitt, nicht Geradenschnitt.
      Was es gibt, ist gehrungsEcken (geometry/wallGeometry.ts:110,
      NICHT EXPORTIERT), und die loest einen ANDEREN Fall: sie bekommt einen
      GEMEINSAMEN Scheitel V uebergeben und rechnet ueber die
      Winkelhalbierende (t = einheit(p+q), len = h/sinHalb) mit GLEICHER
      Halbdicke h. Fuer `trimmen` braucht man den Schnittpunkt zweier Waende,
      die sich NICHT beruehren — genau den Fall, den sie nicht kennt.
      -> DIE LANDKARTE HAT RECHT: „der Befehl, der es rechnet, fehlt."

  UND MEIN FEHLER WAR EIN FALSCHZITAT MEINES EIGENEN BLATTES: ich habe meine
  W-18-Notiz „F-004 als Gehrungsdetail" als „F-004 ist gebaut" gelesen. Das
  Blatt sagt das GEGENTEIL, wortwoertlich in W-18-1:128: „der Satz, dass F-004
  NICHT als Topologie-Formel gebaut ist, sondern als [Gehrungsdetail]". Und
  drei weitere Stellen sagen es auch: REGISTER.md:35 fuehrt F-004 bei W-01
  DURCHGESTRICHEN, W-02/7-GRENZEN.md:14 sagt „F-004 ist im Code nicht
  angebunden", UEBERNAHME-PLAYGROUND-DACH.md:88 sagt „die Datei nennt F-004
  null mal". Der Bestand war also einstimmig richtig; die einzige falsche
  Stelle war die, die ich heute geschrieben habe.

  WAS CLUSTER 3 WIRKLICH IST: ein BAU, und ein lohnender.
    F-004 als aufrufbare Funktion herausziehen  -> schaltet trimmen +
                                                   verlaengern frei
    Parallelversatz neu rechnen (Normale, Abstand d) -> schaltet versatz frei
  Das ist kein Anschluss und laeuft nicht „sofort". Es ist aber auch keine
  Erfindung: die Gehrungsrechnung zeigt, dass das Umfeld (EPS, Miter-Limit,
  Entartungsfaelle) im Haus schon durchdacht ist.
```

### Was das für die B-Zeilen heißt — W-03 ist neu eingeordnet

> **W-03 „Wand bearbeiten" bleibt BAU — meine Zwischenfassung „gemischt, teils Anschluss" ist
> zurückgezogen.** *Sie stützte sich auf Cluster 3, und Cluster 3 war falsch gemessen (siehe oben).
> **Gemessen ist W-03 durchgehend Bau**, aber in **zwei verschiedenen Abhängigkeiten:** `trimmen`,
> `verlaengern`, `versatz` brauchen **Geometrie, die nicht existiert** (Geradenschnitt als Funktion;
> Parallelversatz); `teilen` und `verbinden` brauchen den **Mehrfach-Befehl aus Cluster 1**. **Ein
> Auftrag „W-03 bauen" würde also zwei verschiedene Fundamente in einen Zug legen** — die Warnung
> gilt weiter, nur mit dem richtigen Grund.*

> **W-14 ist damit unabhängig bestätigt.** *Meine Messung sagte „Bewegen/Duplizieren/Spiegeln gebaut,
> **Drehen fehlt**". Die Landkarte sagt es aus dem Code heraus und nennt den Grund: `drehen` (`:63`)
> — „`UPDATE_NODE` kann `transform.rotation` eines ObjectNode setzen, aber Wände …". **Zwei
> unabhängige Wege, dasselbe Ergebnis** (Prüfung 7).*

> **W-24 „Bodenplatte" ist kleiner als gedacht.** *Ich hatte „Indikation BAU" notiert (kein Modul,
> „Bodenplatte" nur als Tooltip in `toolRegistry.ts:147`). Die Landkarte führt `boden` (`:117`) als
> **`deckt` — `ADD_CEILING`**. Die **Modellseite ist da**; was fehlt, ist der Werkzeug-Anschluss.
> Auch das rückt von C näher an B — **aber es bleibt zu messen**, weil `deckt` über den Befehl
> spricht und nicht über die Oberfläche.*

**Reihenfolge-Folgerung für die B-Session — nach der Berichtigung:** *die drei Fundamente sind
**billiger als die 14 Einzelwerkzeuge** und sie sind Voraussetzung, nicht Beigabe. **Aber keines ist
„sofort":** Cluster 1 ist ein echter Bau (W-27-Maßstab, ~2,5 h) und schaltet **fünf** Zeilen frei;
Cluster 3 ist **auch** ein Bau — zwei Geometrie-Funktionen — und schaltet **drei** frei; **Cluster 2
geht nicht ohne Yama** (Schema). **Was wirklich sofort läuft, sind die Ablesungen**, und die sind der
schnelle Teil der B-Session.*



### W-26 und W-28 gemessen — damit sind ALLE ZEHN B-Zeilen gemessen

**W-26 Dachschichten — BAU, und zwar eine ÜBERTRAGUNG statt eines Neuentwurfs**

```text
DER GEGENSTAND FEHLT IM SCHEMA. RoofNode traegt (domain/scene.types.ts, Felder
einzeln gelesen): polygon, roofType, neigungGrad, firstAzimutGrad, ueberstandMm,
traufhoeheMm, aufbauten?, anbau?  —  KEIN schichten.

ABER DAS FELDMUSTER STEHT SCHON ZWEIMAL, wortgleich:
  scene.types.ts:133  WallNode.schichten?:    Array<{ materialId?; dickeMm }>
  scene.types.ts:357  CeilingNode.schichten?: Array<{ materialId?; dickeMm }>
  und :115 sagt es selbst: „Feldgleich mit CeilingNode.schichten".
-> Kein Neuentwurf. Ein drittes Mal dasselbe Muster.

UND EINE NAMENSFALLE, DIESELBE KLASSE WIE `modus` BEI W-12/1:
  'Aufbau' heisst hier DREI verschiedene Dinge —
    RoofAufbau (scene.types.ts:265, Doku :244)  STEHENDE Aufbauten auf der
                                                Dachflaeche: Gauben, Dachfenster,
                                                Kamin. Also OBJEKTE.
    WallNode.schichten / CeilingNode.schichten  die SCHICHTENFOLGE (AUF-76, M0)
    geometry/wandaufbau.Schicht                 ein RECHENTYP mit dicke+lambda
  Und das Schema WARNT SELBST davor, bei :126: „Nicht geometry/wandaufbau.Schicht:
  das ist ein Rechentyp mit dicke und lambda, dies ist [ein anderes]".
-> WER W-26 MIT ADD_ROOF_AUFBAU BAUT, BAUT DAS FALSCHE. Der Befehl haengt
   Gauben ans Dach (applyCommand.ts:332, roof.aufbauten.push) und hat mit
   Schichten nichts zu tun. Die Landkarte bestaetigt es aus der anderen
   Richtung: dachfenster -> 'deckt', Begruendung ADD_ROOF_AUFBAU (:119).

DER TOTE VERTRAG IST BESTAETIGT, mit Gegenprobe:
  konterlattungMm steht an DREI Stellen und hat NULL Leser —
    geometry/dachformVorlagen.ts:122   die Typzeile
                             :1384   Wert [24, 48]
                             :1410   Wert [0, 0]
  Gegenprobe: grep ueber alle .ts/.tsx ausserhalb dieser drei Zeilen = LEER.
-> Er ist der Rest eines Dachschicht-Gedankens, der nie im Schema angekommen ist.
   Das ERKLAERT die Registerzeile und ist selbst kein Bauteil.

SCHUTZGRENZE: ein neues Schema-Feld ist eine Datenbank-/Modellentscheidung und
geht nicht still durch. VORLAGE AN YAMA, kein Auftrag den ich schneide. Der
Vorschlag ist billig zu prüfen, weil er nur ein vorhandenes Muster wiederholt.
```

**W-28 Dachentwässerung — BAU, und die Bemessung ist eine FACHFRAGE**

```text
GEMESSEN, alle drei Wege:
  geometry/linienBauteile.ts:22   'dachrinne' ist ein WERT im Linientyp-Union
                                  (neben 'firstlinie', 'modulsperrlinie')
  app/tools/werkzeugVertrag.ts    KEIN Vertrag mit rinne/entwaesser
  linienBauteile.ts:83            die einzige Platzierungsfunktion heisst
                                  platziereSchneefang — Schneefang, nicht Rinne
  dachformVorlagen.ts:1391        entwaesserungHinweis: 'Vorgehaengte Rinne +
                                  Fallrohr, Bemessung nach Dachflaeche
                                  (Richtwert)'  <- PROSA, keine Rechnung

-> Es gibt einen Linientyp-WERT und einen Hinweistext. Kein Werkzeug, kein
   Vertrag, keine Bemessung. Die Registerzeile („Bemessung fehlt") traegt.

UND DIE BEMESSUNG IST NICHT MEIN OPERAND: Rinnen- und Fallrohrquerschnitt nach
Dachflaeche ist eine NORMGROESSE (DIN 1986-100 / EN 12056). Nach den
Schutzgrenzen wird eine solche Groesse nicht still gesetzt — sie braucht Yamas
Operanden oder einen ausdruecklich bestaetigten Vorschlag. Dasselbe Gate, an dem
W-21L als DECISION_BLOCKED steht.
```

> **Bilanz der zehn B-Zeilen — Yamas Kriterium ist damit erfüllt:** *er hat gesagt, bei B gelte zuerst
> die Messung, und erst danach stehe fest, ob eine Zeile Ablesung oder Bau ist. **Alle zehn sind jetzt
> gemessen.** **Ablesung (schnell):** W-06, W-18, W-12 — je gemessen, zwei davon als Blatt schon
> geschnitten. **Indikation Ablesung:** W-16, W-10. **Anschluss:** W-24 (modellseitig gedeckt, nur Oberfläche fehlt) — **W-03 gehört nach der
> Berichtigung nicht mehr hierher.**
> **Bau:** W-03 (zwei Fundamente, siehe Berichtigung), W-26, W-28 — und W-14 fehlt nur `drehen`.
> **W-26 und W-28 gehen nicht ohne dich:** Schema-Feld bzw. Normgröße.*


## YAMAS VORBEHALT ZU KLASSE B, 13.08. — erst die Messung, dann die Einordnung

**Sein Wortlaut:** *„bei B gilt laut Fahrplan zuerst die Messung: was ist gebaut, was fehlt. Erst danach
steht fest, ob eine B-Zeile eine Ablesung (schnell) oder ein Bau (langsam) wird. … Wenn sich mehrere
B-Zeilen als Bauten entpuppen, gilt eher der W-27-Maßstab: dieser eine Bau hat etwa zweieinhalb Stunden
gebraucht."*

**Der Vorbehalt ist berechtigt, und die Klassendefinition sagt es selbst:** *Klasse B heißt
**„ANSCHLIESSEN — teils gebaut, Anschluss unklar"**. **Unklar heißt: nicht entschieden.** Wer eine
B-Zeile für eine Ablesung hält, hat die Klasse mit einer Zusage verwechselt.*

**Und es gibt einen gemessenen Datenpunkt in JEDE Richtung:**

```text
W-06 Geschoss verwalten   KLASSE B, und es war eine ABLESUNG.
                          Gemessen 13.08.: DREI Module, 355 Zeilen, zehn Exporte,
                          alle angeschlossen — es fehlten nur die Blaetter.
                          Geschnitten, DoR in der ersten Runde erteilt.
W-27 Kantentypen          KLASSE C, und es war ein BAU: etwa zweieinhalb Stunden
                          (Yamas Maszstab), und danach OHNE Aufrufer — die
                          Registerzeile sagt es seit 13.08.
```

> **B ist also nicht „schnell" und nicht „langsam", sondern UNGEMESSEN.** *Das ist der Grund, warum jede
> Zeitschätzung über B ohne Messung eine Vermutung ist — und der W-27-Maßstab ist die richtige Obergrenze
> für den Fall, dass eine B-Zeile sich als Bau entpuppt.*

### ALLE ZEHN B-ZEILEN durchgemessen, 13.08. — und Yamas Sorge ist berechtigt

**Stand nach der Durchmessung.** *Drei Zeilen sind zu Ende gemessen und geschnitten, sieben liegen als
Indikation vor. **Vier davon deuten auf BAUTEN** — damit greift der W-27-Maßstab, und die Annahme
„B = Ablesung" trägt für die Hälfte der Zeilen nicht.*

```text
GEMESSEN UND GESCHNITTEN — ABLESUNG (drei):
  W-06  Geschoss verwalten    3 Module, 355 Z., 10 Exporte, alle angeschlossen
  W-18  Topologie pruefen     F-013 gebaut MIT Nutzermeldung (kontur.ts:63),
                              F-004 als Gehrungsdetail (wallGeometry.ts:62/:106)
  W-12  Ansicht und Kamera    Zustand + Kamera + Raster in BEIDEN Renderern + F-032

INDIKATION ABLESUNG (zwei) — Modul bzw. Verzeichnis vorhanden:
  W-16  Grundriss unterlegen  app/unterlage/ mit DREI Dateien:
                              UnterlagenEbene.tsx · UnterlagenWerkzeuge.tsx ·
                              kalibrierung.ts
  W-10  Decke und Boden       renderers/three-d/deckenMesh.ts

OFFEN (eine) — verstreut, kein Modul:
  W-14  Kopieren/Spiegeln/    'spiegele|mirror|rotiere' -> reineHelfer.tsx,
        Drehen                HausplanerApp.tsx, werkzeugVertrag.ts
                              'dupliziere|kopiere'      -> HausplanerApp.tsx,
                              EngineFlaeche.tsx, werkzeugThemen.ts
                              -> vorhanden, aber ohne Ort. Vollmessung noetig.

INDIKATION BAU (vier) — und hier greift der W-27-Maszstab:
  W-03  Wand bearbeiten       'wandTeilen|wandVerschieben|wandLoeschen' -> 0 Treffer.
                              Kein Modul, keine verstreuten Funktionen dieses Namens.
  W-24  Fundament/Bodenplatte kein Modul. UND EIN ZUORDNUNGSBEFUND: das Register
                              nennt ein Registry-Werkzeug 'Bodenplatte' — gemessen
                              ist das der TOOLTIP des DECKEN-Werkzeugs
                              (toolRegistry.ts:147, „Decke / Bodenplatte"). W-24 hat
                              kein eigenes Werkzeug, es teilt eines mit W-10.
  W-26  Dachschichten         kein Modul; konterlattungMm in dachformVorlagen ist
                              laut Register ein TOTER VERTRAG.
  W-28  Dachentwaesserung     linienBauteile fuehrt 'dachrinne' als Linientyp,
                              die BEMESSUNG fehlt.
```

> **Was das für eine Zeitannahme bedeutet, ohne selbst eine zu machen:** *drei Zeilen sind Ablesungen,
> zwei wahrscheinlich, eine offen — **und vier deuten auf Bauten.** Bei Yamas Maßstab von etwa
> zweieinhalb Stunden je Bau ist das eine andere Größenordnung als ein Ablesungs-Takt. **Ich nenne keine
> Gesamtzahl:** vier mal ein Maßstab ist eine Multiplikation, keine Messung, und die vier sind
> Indikationen und keine Einordnungen.*

**Und ein Nebenbefund, der einer Zeile ihren Gegenstand nimmt:** *`W-24`s „Registry-Werkzeug Bodenplatte"
existiert nicht als eigenes Werkzeug — **es ist der Tooltip des Decken-Werkzeugs** („Decke /
Bodenplatte", `toolRegistry.ts:147`). Wer die Registerzeile liest, hält ein Werkzeug für vorhanden, das
einem anderen gehört. **Dieselbe Klasse wie `auswechslung.ts`** (W-21/2): ein Gegenstand ohne eigenes
Zuhause.*

### Erste Messung an den zwei Zeilen, die Yama nennt — INDIKATION, keine Einordnung

```text
W-12 Ansicht und Kamera   Registerzeile LEER · KEIN dediziertes Modul ·
                          0 Registry-Eintraege fuer 'ansicht'/'kamera'.
                          ABER der Zustand IST gebaut: app/state/uiState.ts:11
                          nennt ihn und sagt woertlich „(Rename modus→viewMode ist
                          ein eigener Hygiene-Slice)". Dazu 'split' in 7 Dateien,
                          zoom in 11, PerspectiveCamera/OrbitControls in 1.
                          -> deutet auf ABLESUNG plus einen benannten Hygiene-Posten.
W-18 Topologie pruefen    Registerzeile LEER · F-004 und F-013 genannt.
                          F-013 IST GEBAUT UND WIRKT, Stelle geoeffnet:
                          geometry/kontur.ts:47 fuehrt 'selbstschnitt' als
                          KonturGrund, :140 gibt ihn zurueck, und :63 traegt eine
                          LESBARE Nutzermeldung — „Die Kontur ueberschneidet sich
                          selbst — zieh den letzten Punkt so, dass sich keine zwei
                          Kanten kreuzen."
                          -> deutet auf ABLESUNG; offen bleibt, ob ein WERKZEUG
                             'Topologie pruefen' fehlt oder die Vorpruefung genuegt.
```

**Was diese Messung IST und was sie NICHT ist:** *ich habe je zwei Stellen **geöffnet** statt nur
gezählt — das ist mehr als eine Musterzählung und weniger als die Vollmessung, die W-06 bekommen hat
(alle Module, alle Exporte, alle Aufrufer). **Für den Schnitt reicht sie nicht;** sie sagt nur, in welche
Richtung die Einordnung wahrscheinlich fällt.*

**Die Regel, die daraus für jede B-Zeile gilt:**

```text
1  Module und Exporte zaehlen, VOR jeder Einordnung.
2  Die Aufrufer messen — und zwar mit ZWEI Mustern: Import UND String,
   weil eine Verdrahtung ueber Strings fuer ein Import-Muster unsichtbar ist
   (belegt an W-31s fuenfter Bedienstelle).
3  Erst dann sagen: Ablesung oder Bau. Vorher steht nur die Klasse fest.
4  Und wenn Bau: der W-27-Maszstab gilt, nicht der Ablesungs-Takt.
```

## Die Reihenfolge — nach ENTSPERRUNG, nicht nach Aufwand

```text
STUFE 1 · KLASSE A ZU ENDE                     Eintrittsbedingung: keine
  1.1  W-07N bauen         wartet auf DoR. Schliesst W-07 auf BESCHRIEBEN.
  1.2  W-08/1 bauen        wartet auf DoR. Blatt seit 10.08. geschnitten.
  1.3  W-13/1 bauen        wartet auf DoR. Blatt seit 10.08. geschnitten.
  1.4  W-09 SCHNEIDEN      << DAS LOCH. Sieben Module, 698 Z. Noch kein Blatt.
  1.5  W-09 bauen
  ABSCHLUSS MESSBAR:  grep -cE '^\| W-(0[1-9]|1[0-3]|21|22) .*BESCHRIEBEN' REGISTER.md
                      -> Ziel 11 (die zehn A-Werkzeuge + W-01)
                      heute 7. Die Zahl steigt nur durch Bauten, nicht durch Schnitte.

STUFE 2 · W-23 DECKUNG UND MATERIAL            Eintrittsbedingung: Fachdaten von Yama
  Warum VOR den anderen C-Werkzeugen: W-21L (Lattung) ist am OPERANDEN blockiert und
  wartet ausdruecklich auf W-23s Ziegeltabelle. W-23 loest einen blockierten Auftrag;
  W-15, W-19 und W-20 loesen nichts.
  BLOCKIERT DURCH: keine Deckungsart-/Lattweiten-Daten im Repo (0 Treffer gemessen).
                   M-04 traegt laut BESTAND-YAMA.md ein Dachziegel-Schema mit
                   verified/source_url -> das ist die Quelle.
  ABSCHLUSS: W-23 BESCHRIEBEN + W-21L nicht mehr blockiert.

STUFE 3 · DIE ZWOELF NEUEN A-ZEILEN EINTRAGEN  Eintrittsbedingung: Stufe 1 durch
  W-17 · W-25 W-29 W-30 W-31 · W-33…W-39
  Je Werkzeug: Anschluss MESSEN (welche Module, welche Formeln), dann Blatt schneiden.
  KEIN Sammelblatt. Ein Auftrag je Werkzeug, zwei Stufen, wie bisher.
  W-31 bleibt gesperrt bis F-028 🟢 (PV-Belegung).
  W-37 traegt A-14s Ausgabeauflage — es ist die Ausgabestelle von N-003.

STUFE 4 · KLASSE B ANSCHLIESSEN                Eintrittsbedingung: Stufe 3 durch
  W-03 W-06 W-10 W-12 W-14 W-16 W-18 · W-24 W-26 W-28
  Hier gilt zuerst die Messung: was ist gebaut, was fehlt. Erst danach A oder C.
  W-12 und W-18 stehen unter Yamas offener Streichfrage — nicht anfassen, bevor sie
  entschieden ist.

STUFE 5 · KLASSE C BAUEN                       Eintrittsbedingung: je Werkzeug eigen
  W-27 Kantentypen      -> braucht F-025/F-026, beide 🟢 nach A-12. FREI.
  W-32 Giebelbindung    -> braucht W-03 (Stufe 4). GESPERRT bis dahin.
  W-15 Material/Farbe   -> braucht W-13 (Stufe 1). Danach frei.
  W-19 Sonne            -> braucht W-07 + W-08 (Stufe 1) und beruehrt F-028. Vorsicht.
  W-20 Mengen           -> braucht W-05 + W-08. holzMengen ist der Anschluss.
  W-40 W-41 W-42        -> die Fuehrungsluecken. W-42 (Schreibpfad) ist die wichtigste:
                           solange der Wizard JSON herunterlaedt, arbeiten Wizard und
                           Expertenmodus nicht auf denselben Objekten (Yamas Teil 5).
```

## Was BLOCKIERT ist — und durch wen

```text
W-21L Lattung        OPERANDEN-GATE: keine Deckungsart-Daten. -> Yama oder W-23.
W-31 PV-Belegung     F-028 🔴. -> Umrechnung an der Systemgrenze (Yamas Schritt 9).
W-12 · W-18          Yamas offene Streichfrage.
N-003-Ampel          DAUERGELB, entschieden. Nicht blockiert, dauerhaft begrenzt.
W-07s acht Formeln   ALLE ungeprueft. Nach 603eddc2 (sieben von zehn fielen) ist das
                     ein eigener Auftrag, und er gehoert VOR W-07s Abschluss.
```

## Was dieser Fahrplan NICHT tut

```text
- Er ordnet KEINEN Auftrag um. Yamas Liste hat Vorrang; dieser Plan sagt, welches
  WERKZEUG wann kommt, nicht welcher Auftrag heute laeuft.
- Er nennt KEINE Zeitraeume. Jede Stufe hat eine Eintrittsbedingung, keine Frist —
  eine Frist waere eine Zahl ohne Messung.
- Er entscheidet NICHT ueber W-12/W-18 (Yamas Streichfrage) und nicht ueber die
  Prozessebene-Inhalte (Yamas Zielbild).
- Er klassifiziert die 19 neuen Zeilen VORLAEUFIG. Jede braucht ihre eigene
  Anschlussmessung, bevor ein Blatt entsteht — das ist Stufe 3 und Stufe 4.
```

## Der alte Fahrplan

```text
docs/FAHRPLAN-KLASSE-A.md   bleibt als BELEG stehen (drei Runden, sechs Grobzahl-
   Korrekturen, die Klasse-A-Messungen). Er ist NICHT MEHR der Plan.
   -> in seinem Kopf wird ein Verweis hierher eingetragen, damit niemand zwei
      Fahrplaene liest. Das ist Teil dieses Auftrags, nicht spaeter.
```

## REIHENFOLGE-ENTSCHEIDUNG 12.08. — W-09/1 läuft vor A-15

**Anlass: der Generator hat eine Sperre gemeldet statt sie zu umgehen** (`bd011a06`), und die
Entscheidung ausdrücklich mir überlassen: *„die Reihenfolge zweier Aufträge gehört dem Planner."*

```text
DIE SPERRE, seine Messung:
  A-15-13 verlangt einen Vorschlag fuer JEDE der elf Engines.
  A-15-11 verlangt, dass die VIER Treppen-Dateien NICHT dort gemessen, sondern aus
          W-09/1-5 uebernommen werden.
  W-09/1 steht auf BEREIT und ist NICHT gebaut -> die Zulieferung existiert nicht.
  -> beide Kriterien sind heute nicht gleichzeitig erfuellbar.
```

**ENTSCHEIDUNG: W-09/1 läuft zuerst.** *Vier Gründe, und der billigere ist nicht der erste:*

```text
1  A-15-11 ist INHALTLICH richtig. Die Auflage umzukehren hiesse, den Fehler zuzulassen,
   den sie verhindert: zwei Auftraege, die dieselbe Datei messen, erzeugen zwei Zahlen.
2  W-09/1 schliesst KLASSE A ab. Mit W-07N steigt der Zaehler von 9 auf 11 — und er
   steigt nur durch Bauten (H-3).
3  A-15 verliert NICHTS. Die sieben messbaren Zeilen stehen vollstaendig; die vier
   Treppen-Zeilen kommen als ZULIEFERUNG dazu, nicht als Nacharbeit.
4  Es kostet keine zusaetzliche Runde: W-09/1 muss ohnehin gebaut werden.
```

**Was dem Generator freibleibt** — es ist Bauweise, nicht Reihenfolge: *A-15 mit den sieben Zeilen
abschließen und die vier als ausstehende Zulieferung führen, **oder** A-15 zurückstellen, bis W-09
gebaut ist.* **Beides ist mit dieser Entscheidung vereinbar.**

**Und der Mangel, der die Sperre erst möglich gemacht hat, ist meiner:** *ich habe `A-15-11`
geschnitten, ohne A-15 in der Konfliktprüfung **hinter** W-09 zu stellen.* **Ein Blatt, das eine
Zulieferung aus einem anderen Auftrag zitiert, braucht eine Reihenfolge-Zeile — nicht nur den
Verweis.** *Das ist derselbe Vorlagen-Mangel wie der fehlende Rückweg: **die Vorlage kennt das Feld
nicht.** Beide werden in den nächsten Schnitt eingebaut, nicht rückwirkend in laufende Blätter.*

```yaml
stufen: 5
zeilen_gesamt: 42
heute_beschrieben: 7
naechster_schnitt: "W-09 Treppe — das letzte ungeschnittene Klasse-A-Werkzeug"
kern: "ein Plan mit fester Rundenzahl wirft aus, was nicht in die Runden passt.
       Dieser hat Stufen mit Eintrittsbedingung — eine Zeile, die in keine Stufe passt,
       ist ein Befund gegen den Plan."
offen_an_yama: "Deckungsart-Tabelle (Stufe 2) · W-12/W-18-Streichfrage (Stufe 4) ·
                ob W-09 sofort geschnitten wird"
```


## REGEL — keine festen Suite-Zahlen in Kriterien (aus W-01N übernommen, 12.08.)

> **Der Generator hat diese Regel in `docs/FAHRPLAN-KLASSE-A.md` eingetragen, weil Kriterium und
> Scope von W-01N sie dort verlangten — und ausdrücklich gemeldet, dass diese Datei seit 12.08.
> aufgehoben ist.** *Er hat den Widerspruch im Regeltext sichtbar gemacht statt eine Datei außerhalb
> seines Scopes anzufassen, und den Ball an den Planner gegeben. **Der SPEC-Fehler ist meiner:** ich
> habe W-01N heute überarbeitet — Zustand, Basis, §3-Verweis, Drift-Beleg — und dabei nicht bemerkt,
> dass sein Ziel eine aufgehobene Datei ist. **Hier steht sie im gültigen Plan; damit wirkt sie.***

**Die Regel:** *Ein Kriterium sagt „die Insel-Suite bleibt **unverändert** grün" — **ohne Zahl**. Die
gemessene Zahl gehört in den **Bericht**, zusammen mit dem Befehl, der sie erzeugt hat.*

**Der Fall, aus dem sie kommt, und er ist stärker als „die Zahl veraltet":**

```text
1689   steht als Kriterium in W-01/1-6
1692   gemessen bei der Abnahme — schon beim Schnitt des Blattes ueberholt
1693   Stand am 12.08., FUENFMAL unabhaengig genannt
1694   Stand nach dem A-18-Bau
1668   grep -cE '^\s*(test|it)\(' ueber 166 Testdateien
```

> **Die letzten beiden sind BEIDE richtig und messen Verschiedenes:** *`grep` zählt geschriebene
> `test(`-Aufrufe, der Lauf zählt **ausgeführte** Zusagen — parametrisierte Tests erzeugen mehr Läufe
> als Zeilen. **Eine feste Zahl ist damit nicht erst veraltet, sobald jemand einen Test schreibt,
> sondern schon unbestimmt, sobald zwei Rollen mit verschiedenen Werkzeugen messen.*** *Deshalb
> „unverändert" statt einer Ziffer: das ist prüfbar, ohne die Messmethode mitzuliefern.*

**Gilt für alle Stufen dieses Plans**, nicht nur für Klasse A — und für jedes `must_preserve`, das
eine Suite nennt.
