# W-12/1 — Ansicht und Kamera. Alles gebaut, kein Werkzeug — und `modus` heißt zweimal etwas anderes

```yaml
auftrag: "W-12/1"
werkzeug: "W-12 Ansicht und Kamera"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Die Einordnung ist GEMESSEN:
      Zustand, Kamera, Raster und F-032 sind gebaut."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: b778152b
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "Vergeben sind W-12 und W-12/W; W-12/1 ist frei, 0 Blaetter."
einwand_erledigt: "W-02:206 und W-13:239 fuehren beide 'W-12 zurueckgehalten, Einwand bei Yama'.
                   Yama hat es am 13.08. entschieden: W-12 und W-18 bleiben. Damit ist der Einwand
                   erledigt und der Schnitt frei — das steht hier, weil zwei Blaetter sonst weiter
                   eine Zurueckhaltung behaupten, die nicht mehr gilt."
anlass: "Yamas Regel fuer Klasse B, 13.08.: erst die Messung, dann die Einordnung. W-18 war die erste
         Zeile, das ist die zweite — und damit sind beide, die er genannt hat, gemessen."
grundlage: "store/hausplanerStore.ts:20/:28/:45/:72 · renderers/three-d/szene.ts:100/:101/:170/:212-215/
            :621/:627 · app/rahmen/Buehne.tsx:146 (gezeichnet) mit app/HausplanerApp.tsx:1261-1269/:1337/:1409/:349
            und app/dashboard/Kopfrahmen.tsx:304 · app/state/uiState.ts:5/:10/:11 · FORMELSAMMLUNG:218"
```

## 1 — Die Einordnung ist gemessen: ABLESUNG. Alle vier Gegenstände sind gebaut

```text
(1) ANSICHTSZUSTAND
    store/hausplanerStore.ts:20  export type HausplanerModus = '2d' | 'split' | '3d'
                            :28  modus: HausplanerModus
                            :45  setModus: (modus) => void
                            :100 modus: '2d'                 <- Standard   [BERICHTIGT 14.08., war :72]
    Und app/state/uiState.ts:10 sagt, WO er wohnt und warum:
      „Ansicht (2d/split/3d) bleibt im Modell-Store (modus) — die Activation-Engine
       liest sie von dort."

(2) KAMERA UND STEUERUNG
    renderers/three-d/szene.ts:23   import { OrbitControls }
                              :100  private readonly kamera: THREE.PerspectiveCamera
                              :101  private readonly steuerung: OrbitControls
                              :170  new THREE.PerspectiveCamera(…)

(3) RASTER — BERICHTIGT nach 800a6075, und mein Beleg war eine TYPZEILE
    3D-SZENE:
      szene.ts:212  new THREE.GridHelper(80, 80, 0xcfd6de, 0xe2e6ea)
             :213-214  transparent, opacity 0.5
             :215  this.szene.add(raster)
    2D-BUEHNE (Konva), die ganze Kette — selbst nachgemessen:
      HausplanerApp.tsx:1261-1269  die Linien ENTSTEHEN hier   [BERICHTIGT 14.08., war :1274-1281]
                                   (const rasterLinien … for-Schleife ueber
                                   weltBreite/weltHoehe mit rasterSchritt)
                            :1337 und :1409  durchgereicht   [BERICHTIGT 14.08., war :1423 — es sind ZWEI Stellen]
                            :349   geschaltet   [BERICHTIGT 14.08., war :346]
      Kopfrahmen.tsx:304           der Knopf
      Buehne.tsx:146               GEZEICHNET: {rasterAn && rasterLinien}
      Buehne.tsx:62                rasterAn: boolean — das ist die PROPS-TYPZEILE
                                   im Interface, neben pan, setPan und
                                   rasterLinien. KEIN Zeichencode.

    UND ES GIBT KEINEN '2D-RENDERER': der einzige Renderer-Ordner ist
    renderers/three-d/. Der 2D-Weg ist die Konva-Buehne in der APP-Schicht.
    Meine Formulierung 'Raster in BEIDEN Renderern' war deshalb doppelt falsch —
    der Beleg war eine Typzeile, und die Schicht hiess anders.

(4) F-032 Transformation eines Punktes (FORMELSAMMLUNG:218, homogene 4x4-Matrix)
    szene.ts:621  new THREE.Matrix4().makeBasis(…)
           :627  geometrie.applyMatrix4(m)
    Also EIGENE Matrix-Anwendung, nicht nur three.js-Internes — die
    Registerzuordnung F-032 traegt.
```

> **Damit ist W-12 eine ABLESUNG** — *es fehlen die Blätter, nicht der Code. **Zweite gemessene B-Zeile
> nach W-18, und beide fallen auf die schnelle Seite.** Für Yamas Vorbehalt heißt das: an diesen zwei
> Zeilen greift der W-27-Maßstab nicht — was über die übrigen acht B-Zeilen nichts sagt.*

**Die Frage aus W-01 ist mitbeantwortet:** *`W-01-fang-beschreiben.md:94` schließt aus: „Ob ein sichtbares
Raster gezeichnet wird, ist eine Renderer-Frage und steht in W-12/Schicht 4, nicht hier." **Gemessen: es
wird gezeichnet — 3D als `GridHelper` (`szene.ts:212-215`), 2D über die Konva-Bühne** (`Buehne.tsx:146`
zeichnet, `HausplanerApp.tsx:1261-1269` erzeugt).*

> *Und **die Renderer-Frage selbst hat eine andere Antwort als W-01 vermutet:** es gibt nur **einen**
> Renderer-Ordner, `renderers/three-d/`. Der 2D-Weg liegt in der App-Schicht. **Meine erste Fassung
> sagte „in beiden Renderern" und belegte 2D mit `Buehne.tsx:62` — einer Props-Typzeile.** Berichtigt
> nach `800a6075`; H-8, der Ort ist nicht die Wirkung.*

## 2 — Der tragende Punkt: `modus` heißt ZWEIMAL etwas anderes

```text
store/hausplanerStore.ts:20  HausplanerModus = '2d' | 'split' | '3d'      ANSICHT
app/studioDaten.ts:97        StudioModus     = 'start' | 'guided' | 'expert'  STUDIO
```

> **Beide heißen `modus`, beide haben ein `setModus`, und sie bedeuten Verschiedenes.** *`HausplanerStudio.tsx:23`
> hält `const [modus, setModus] = React.useState<StudioModus>('start')` und prüft in `:85`
> `imExperte = modus === 'expert'` — **das ist W-39s Studio-Rahmen, nicht die Ansicht.** Wer nach `modus`
> greppt, findet zwei Zustände und hält sie für einen. **Das Blatt muss beide nennen und je den Träger** —
> genau die Lehre aus W-36, wo vier Statusachsen an vier Trägern hingen.*

**Und ein benannter Hygiene-Posten gehört dazu:** *`uiState.ts:11` sagt selbst „(Rename `modus→viewMode`
ist ein eigener Hygiene-Slice.)" — **die Namensgleichheit ist also bekannt und als eigener Vorgang
vorgemerkt.** Das Blatt nennt sie, ändert sie nicht.*

## 3 — Kein Werkzeug, und das ist RICHTIG so

```text
BERICHTIGT nach 800a6075 — meine erste Fassung stellte hier '0 gegen 12'
gegenueber. Das ist eine nackte Zahl ohne Traeger, und W-12-1-6 im selben Blatt
verbietet sie. Was gemessen ist:

  ZUGRIFFSART, und darauf kommt es an:
    als Werkzeug-ID  'ansicht'/'2d'/'3d'/'split'          KEIN Eintrag
    als WERT von supportedViews                           es gibt sie,
                                                          im selben Register
  ZAHLEN, je mit Traeger:
    supportedViews in app/tools/toolRegistry.ts                       12
    supportedViews im GANZEN Hausplaner                              75
    '2d' oder 'split' als supportedViews-WERT in toolRegistry.ts       9
```

> **Die Ansicht ist eine EIGENSCHAFT, an der sich Werkzeuge ausrichten — kein Werkzeug.** *Und der
> tragende Beleg ist nicht die Anzahl, sondern **die Zugriffsart: `'2d'` und `'split'` stehen im selben
> Register — als Werte einer Eigenschaft, nie als Werkzeug-ID.** Neun Zeilen führen sie so. Das
> Fang-Werkzeug W-01 hat dieselbe Lage und sein Blatt sagt es wörtlich: „der Fang liegt unter anderen
> Werkzeugen, er ist keines." **Ansicht und Kamera sind Infrastruktur.** Das Blatt muss es sagen, sonst
> liest die nächste Rolle „LEER, 0 GEBAUT" und baut ein Ansichts-Werkzeug, das es nicht geben darf.*

> ***Warum die Zahl allein nicht getragen hätte:*** *die 12 gilt nur für `toolRegistry.ts`; im ganzen
> Hausplaner sind es 75, davon nach der Messung des plan-prüfers 54 im stillgelegten Katalog. Wer „12"
> ohne Datei schreibt, gibt der nächsten Rolle eine Zahl, die sie nicht wiederfindet — **genau der
> Mangel, den ich heute schon an W-36-5, W-37-5 und A-27-6 behoben habe** und hier zwei Absätze nach
> meinem eigenen Verbot wieder gesetzt hatte.*

## 4 — Scope

```text
W-12/1 IST  die Ablesung des Gebauten: der Ansichtszustand mit seinen drei Werten
            und seinem Ort, Kamera und OrbitControls, das Raster in der
            3D-Szene UND auf der 2D-Konva-Buehne (zwei Schichten, nicht zwei
            Renderer — es gibt nur renderers/three-d/), F-032 mit zwei Fundstellen, die H-9-Grenze zwischen den
            zwei `modus`, und die Feststellung, dass es KEIN Werkzeug gibt und
            keines geben soll.

W-12/1 IST NICHT
            der RENAME modus->viewMode. uiState.ts:11 fuehrt ihn als eigenen
            Hygiene-Slice; das Blatt nennt ihn und fasst ihn nicht an.
            StudioModus -> W-39 (Studio-Rahmen, BETRIEBSBESTAETIGT). Nur zur
            Abgrenzung genannt.
            szene.ts als Ganzes -> der 3D-Renderer ist ein eigener Gegenstand;
            hier werden nur die Ansicht-, Kamera-, Raster- und F-032-Stellen
            genannt.
            ein WERKZEUG 'Ansicht'. Es gibt keines und soll keines geben —
            siehe Abschnitt 3.
```

## 5 — Abnahmekriterien

```text
W-12-1-1 (P1, TRAGEND) Die ZWEI `modus` stehen im Blatt, JE MIT TRAEGER und
         Fundstelle: HausplanerModus ('2d'|'split'|'3d') im Modell-Store
         (hausplanerStore.ts:20), StudioModus ('start'|'guided'|'expert') in
         studioDaten.ts:97 und benutzt in HausplanerStudio.tsx:23/:85.
         Ohne beide Traeger haelt die naechste Rolle sie fuer einen Zustand — und
         `setModus` gibt es zweimal. Das ist die Lehre aus W-36, wo vier
         Statusachsen an vier Traegern hingen.
W-12-1-2 (P1) Die VIER Gegenstaende mit Fundstelle: Ansichtszustand, Kamera samt
         OrbitControls, Raster in BEIDEN SCHICHTEN, F-032 mit
         szene.ts:621 und :627. Am Bau-Stand erheben, keine Zahl aus diesem Blatt.
         BEIM RASTER GILT DIE SCHICHT, NICHT DER RENDERER: 3D szene.ts:212-215,
         2D die Konva-Buehne mit Buehne.tsx:146 als Zeichenstelle und
         HausplanerApp.tsx:1261-1269 als Erzeugung. Buehne.tsx:62 ist als Beleg
         NICHT zulaessig (Props-Typzeile), und 'beide Renderer' ist falsch, weil
         renderers/ nur three-d/ enthaelt. Beides war der Mangel meiner ersten
         Fassung (800a6075).
W-12-1-3 (P1) BERICHTIGT nach 800a6075, UND DER WIDERSPRUCH WAR IM SELBEN BLATT:
         meine erste Fassung fixierte '0 gegen 12' im Kriterium — waehrend
         W-12-1-6 zwei Absaetze weiter die nackte Zahl VERBIETET. Ein Blatt, das
         sich selbst widerspricht, ist fuer den Bauenden unerfuellbar.
         WAS DIE ZAHLEN WIRKLICH SIND, selbst nachgemessen:
           supportedViews in app/tools/toolRegistry.ts        12
           supportedViews im GANZEN Hausplaner                75
             davon nach seiner Messung 54 im stillgelegten Katalog
           '2d' oder 'split' als supportedViews-WERTE in toolRegistry  9
         Die 12 gilt also NUR fuer eine Datei, und die 0 braucht die ZUGRIFFSART:
         '2d' und 'split' kommen im selben Register neun Mal vor — als WERTE einer
         Eigenschaft, nicht als Werkzeug-ID.
         WAS JETZT VERLANGT IST: 7-GRENZEN sagt, dass es KEIN Werkzeug gibt und
         keines geben soll, und belegt es mit der ZUGRIFFSART statt mit einer Zahl:
         kein Registry-Eintrag hat 'ansicht'/'2d'/'3d'/'split' als ID, waehrend
         Werkzeuge sie als supportedViews-WERTE fuehren. Die Zahlen werden am
         Bau-Stand erhoben und je mit ihrem Traeger genannt (welche Datei).
         Die Ansicht ist eine Eigenschaft, an der sich Werkzeuge ausrichten —
         dieselbe Lage wie W-01s Fang, dessen Blatt sagt er liege unter anderen
         Werkzeugen und sei keines. Ohne diesen Satz liest die naechste Rolle
         'LEER, 0 GEBAUT' als Auftrag.
W-12-1-4 Die Frage aus W-01-fang-beschreiben.md:94 ist im Blatt beantwortet: das
         sichtbare Raster WIRD gezeichnet — 3D als GridHelper (szene.ts:212-215),
         2D ueber die Konva-Buehne, und zwar mit der GANZEN Kette statt mit einer
         Typzeile: HausplanerApp.tsx:1261-1269 erzeugt die Linien, :1337/:1409 reichen sie
         durch, :349 schaltet, Kopfrahmen.tsx:304 traegt den Knopf, und
         Buehne.tsx:146 zeichnet ({rasterAn && rasterLinien}).
         NICHT als Beleg zulaessig: Buehne.tsx:62. Das ist die Props-Typzeile —
         H-8, der Ort ist nicht die Wirkung. Mein erster Beleg war genau diese
         Zeile, und W-01 hat die Frage ausdruecklich hierher verwiesen; ein
         Typeintrag beantwortet sie nicht. W-01 hat sie ausdruecklich
         hierher verwiesen; sie darf nicht zwischen zwei Blaettern verschwinden.
W-12-1-5 Der HYGIENE-POSTEN steht als Grenze: uiState.ts:11 nennt den Rename
         modus->viewMode als eigenen Slice. Das Blatt nennt ihn und aendert nichts.
W-12-1-6 Die Waechter je mit ZUGRIFFSART, getrennt nach IMPORT und QUELLE, und
         KEINE nackte Zahl im Kriterium — am Bau-Stand erheben. Grund: bei W-18
         lieferte das Wort 'kontur' zwoelf Testdateien und der Import EINE, weil
         elf die Werkzeug-ID trafen. Hier ist dieselbe Falle zu erwarten, weil
         'modus' zweimal vorkommt.
W-12-1-7 Alle sieben Blaetter gefuellt, Gegenprobe `tail -n +2 <blatt> | md5` je
         Blatt, keine zwei Werkzeuge mit gleichem Hash.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Fundstellen am Bau-Stand** (Pflichtprüfung 8),
**jede Zahl an zwei Mustern** (Prüfung 7), **Nachweis muss rot werden können** (Prüfung 4).

```yaml
was_diese_messung_fuer_yamas_vorbehalt_bedeutet: "Er hat gefragt, ob B-Zeilen Ablesungen oder Bauten sind,
        und gewarnt, dass bei Bauten der W-27-Maszstab von etwa zweieinhalb Stunden gilt. Beide Zeilen, die
        er genannt hat, sind jetzt gemessen und BEIDE sind Ablesungen: W-18 mit gebauter Selbstschnitt-
        pruefung samt Nutzermeldung, W-12 mit Zustand und Kamera und Raster und F-032. Dazu W-06, das
        ebenfalls in B stand und eine Ablesung war. DREI von zehn B-Zeilen sind damit gemessen, alle drei
        Ablesungen — was ueber die restlichen SIEBEN nichts sagt. Seine Regel bleibt richtig: erst messen."
die_h9_falle_ist_hier_die_schaerfste_bisher: "Zwei Zustaende heissen `modus`, beide haben ein setModus, und
        sie bedeuten Verschiedenes — Ansicht gegen Studio-Modus. Bei W-18 waren es ein Werkzeug und ein
        Modul mit demselben Namen; hier sind es zwei Zustaende in derselben Anwendung. Wer nach modus
        greppt, bekommt beide und haelt sie fuer einen. Deshalb ist W-12-1-1 tragend und verlangt je den
        TRAEGER, nicht nur den Namen."
was_ich_NICHT_gemessen_habe: "Ob der Rename modus->viewMode noetig ist. uiState.ts:11 fuehrt ihn als
        eigenen Hygiene-Slice, also ist er bekannt und vorgemerkt; ihn zu bewerten waere eine
        Zuschnittsfrage und keine Ablesung."
W_12_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
