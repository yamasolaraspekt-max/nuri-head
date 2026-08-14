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
grundlage: "store/hausplanerStore.ts:20/:28/:45/:100 (war :72) · renderers/three-d/szene.ts:100/:101/:170/:212-215/
            :621/:627 · app/rahmen/Buehne.tsx:146 (gezeichnet) mit app/HausplanerApp.tsx:1261-1269/:1337/:1409/:349
            und app/dashboard/Kopfrahmen.tsx:304 · app/state/uiState.ts:5/:10/:11 · FORMELSAMMLUNG:253 (F-032, war :218)"
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

(4) F-032 Transformation eines Punktes (FORMELSAMMLUNG:253, homogene 4x4-Matrix)
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

## Votum des Evaluators (§11) — Runde 1

**NACHBESSERN.** Sechs von sieben Kriterien erfüllt, **W-12-1-6 nicht**. Gemessen im Prüfstand
`da2fb678` (worktree, `node_modules` + `vendor` per `cp -al`), Bau selbst gesucht statt aus
`bau_sha` genommen: `git log b778152b..HEAD | grep -i w-12` liefert genau einen Bau — `da2fb678`
(07:53), Meldung `9d83bde6` (07:55). Scope: sieben `.md`, kein Produktivcode.

### Messtisch — jede Kriterienzeile

| Kriterium | Urteil | womit gemessen |
|---|---|---|
| **W-12-1-1** zwei `modus` je mit Träger | **erfüllt** | `hausplanerStore.ts:20` = `export type HausplanerModus`, `studioDaten.ts:97` = `export type StudioModus`, `HausplanerStudio.tsx:23` = `useState<StudioModus>` — alle drei am Bau-Stand selbst geöffnet. `setModus` gibt es zweimal: Store `:45`/`:126`, React-useState `:23`. |
| **W-12-1-2** vier Gegenstände mit Fundstelle | **erfüllt** | `szene.ts:23/:100/:101/:170/:178` (Kamera + OrbitControls), `:621` `makeBasis` / `:627` `applyMatrix4` (F-032), `:212-215` GridHelper, 2D-Kette `HausplanerApp.tsx:349/:1261-1269/:1337/:1409` → `Kopfrahmen.tsx:304` → `Buehne.tsx:146` — jede Zeile einzeln geöffnet. `Buehne.tsx:62` steht dreimal ausdrücklich als **Nicht**-Beleg; „beide Renderer" nur in der Verneinung. |
| **W-12-1-3** Zugriffsart statt Zahl, Zahlen mit Träger | **erfüllt** | selbst nachgezählt: `supportedViews` in `toolRegistry.ts` **12**, im ganzen Hausplaner **75** Zeilen (76 Vorkommen — eine Zeile doppelt), davon **54** in `toolCatalogStillgelegt.ts`; `'2d'`/`'split'` als Wert **9**; Registry-Einträge mit `id: 'ansicht'/'2d'/'3d'/'split'` **0**. Rot-Probe: dasselbe Muster findet `id: 'wand'` — es greift. |
| **W-12-1-4** W-01-Frage beantwortet | **erfüllt** | `W-01-fang-beschreiben.md:94` selbst geöffnet: verweist die Rasterfrage tatsächlich hierher. Die ganze Kette steht in `7-GRENZEN`, `Buehne.tsx:62` als H-8-Nichtbeleg daneben. |
| **W-12-1-5** Hygiene-Posten als Grenze | **erfüllt** | `uiState.ts:11` selbst geöffnet, Wortlaut deckt sich; an drei Blattstellen als *benannt und nicht angefasst* geführt. |
| **W-12-1-6** Wächter je mit Zugriffsart | **NICHT erfüllt** | siehe Befund unten. Die drei **Zahlen** stimmen (11/5/2, selbst nachgemessen), die **Wächterauswahl** trägt nicht. |
| **W-12-1-7** sieben Blätter, md5 je Blatt | **erfüllt** | 63/94/63/58/50/71/85 Zeilen, sieben verschiedene `tail -n +2 … \| md5`, gegen **alle 252** Werkbank-Blätter geprüft: keine Kollision. |
| Regression Insel-Suite | **grün** | `npm run test:hausplaner` im Prüfstand: `pass 1750`, `fail 0` — Zählerstand unverändert. |
| Regression `tsc` | **grün** | `npm run tsc:hausplaner`: Ausstieg 0, keine Ausgabe. |
| Bündel `public/hausplaner/hausplaner.js` | **nicht nötig** | Scope-Diff am Commit: sieben `.md`, `resources/` 0 — es gibt nichts zu bündeln. |
| §15 Testdatenbank | **nicht berührt** | Scope-Diff: 0 Treffer auf `database\|migration\|seeder\|.sql`; meine eigenen Schreibvorgänge sind zwei `.md`. |
| Browser | **nicht gefahren** | keine sichtbare Wirkung — der Bau ändert ausschließlich Dokumentation. |

### Der Befund zu W-12-1-6

**Die Falle ist nur auf der Zählebene abgewehrt worden, nicht in der Auswahl.** Der Bericht meldet
„DIE ERWARTETE FALLE IST EINGETRETEN und gemessen: elf gegen zwei" — das stimmt, ich habe 11/5/2
selbst nachgemessen (`grep -rl 'modus' __tests__` = **11**, Import `hausplanerStore` = **5**, davon
mit `modus` im Rumpf = **2**). Aber die daraus gebaute Wächtertabelle in `6-PRUEFUNG` misst etwas
anderes, als sie behauptet:

**(a) Die zwei aufgeführten IMPORT-Wächter bewachen den Ansichtszustand nicht.**

- `eineWerkzeugzeile.test.ts` steht mit *„`modus` 2× — die Ansicht als Bedingung für die
  Werkzeugzeile"*. Die zwei Treffer sind `:69` (Testname) und `:71`
  `assert.deepEqual(namen, ['Verlauf', 'Ansichtsmodus', 'Ansicht', …])` — beide Male ist `modus`
  Teil des **Gruppennamens „Ansichtsmodus" im Markup**. Geprüft wird die Reihenfolge der
  Gruppenbeschriftungen, nicht die Ansicht als Bedingung. Das ist H-8 in derselben Form, die das
  Blatt bei `Buehne.tsx:62` selbst zurückweist.
- `rechte.test.ts` steht mit *„`modus` 1×"*. Der Treffer ist `:138`
  `app.match(/\[activeWorkspace, modus, selectedNodeIds[^\]]*\]/)` — `modus` ist Teil eines
  Suchmusters für die Abhängigkeitsliste; der Kommentar `:134-137` sagt ausdrücklich, geprüft
  werde, **dass `rechte` in der Liste steht**.

**(b) Der eigentliche Wächter des Ansichtszustands fehlt — in einer Datei, die das Blatt dreimal
nennt.** `kopfrahmen.test.ts:93` führt
`test('K-03 (Bindung): jeder Ansichtsmodus-Knopf zeigt SEINEN Zustand und schaltet auf SEINEN Modus')`
mit `modusKnoepfe()` (`:85-89`, liest die `<OpGruppe name="Ansichtsmodus">`) und
`assert.equal(zeilen.length, 3, …)`. Der Kopfkommentar `:18` nennt die durchgekommenen Mutationen,
aus denen die Zusage entstanden ist: *„Ansichtsmodus 2D/Split zeigen fremden Zustand · 3D-Knopf
schaltet auf 2D"*. Das Blatt nennt `kopfrahmen.test.ts` an drei Stellen — **ausschließlich für den
Rasterschalter `:110`**. Dazu fehlen zwei weitere echte Zusagen:
`buehne.test.ts:184-188` (*„K-05 (Grenze): die Hülle um die Bühne … hängt an `modus`"*, mit
`assert.match(app, /display: modus === '3d' \? 'none' : 'block'/)` und
`assert.doesNotMatch(buehne, /modus/)`) und `ansichtBereit.test.ts:161/:164`
(`assert.doesNotMatch(zeile, /modus/, 'die Breite darf nicht am Modus haengen')`).

Gegenprobe: das Wort **`Ansichtsmodus` kommt im gesamten Blattwerk kein einziges Mal vor**, ebenso
wenig `K-03`, `K-05`, `modusKnoepfe`. Rot-Probe dazu — dasselbe Suchmuster findet `kopfrahmen.test`
in `4-BEDIENUNG` (1×) und `6-PRUEFUNG` (2×), es greift also.

**Warum das trägt und nicht Geschmack ist:** `6-PRUEFUNG` führt unter *„Was NICHT geprüft wird"*
vier Zeilen (Kamera/OrbitControls, GridHelper 3D, 2D-Rasterkette, `setModus`-Verwechslung) und
schweigt zum Ansichtsmodus-Schalter. Die nächste Rolle liest daraus, der Ansichtszustand sei
allenfalls schwach verriegelt — während `K-03` ihn zeichengenau bindet und aus zwei durchgekommenen
Mutationen entstanden ist. Genau die Fehlerklasse, die W-12-1-6 verhindern soll: nicht die Zahl war
falsch, sondern die Zuordnung *„was er berührt"*.

**Nicht Teil des Befundes:** die Zeilenangabe `HausplanerStudio.tsx:85` → `:87`. Ich habe es
nachgemessen — die Datei ist zwischen `b778152b` und `da2fb678` unverändert, `StudioModus` steht in
`:12`, `:23`, `:87`, nicht in `:85`; `:85` ist `const imExperte = modus === 'expert';`. Der Bau hat
die Wanderung **gemeldet statt abgeschrieben** (Bericht: *„EINE ZEILENANGABE IST GEWANDERT, gemeldet
statt abgeschrieben"*). Das ist richtig gelöst, kein Mangel.

### Eigene Messfehler dieser Runde

1. **Zu enges Belegmuster.** Mein erster Durchgang suchte `szene.ts:627` und `HausplanerApp.tsx:1261`
   als Zeichenkette und meldete „fehlt". Das Blatt schreibt sie tabellarisch als `szene.ts:621/:627`
   und `:1261-1269`. Beide stehen an je drei Stellen — mein Fehler, nicht der des Baus.
2. **Zu enges Importmuster, dritter Fall derselben Klasse.** `grep -rl "from '…store/hausplanerStore'"`
   lieferte 4 statt 5, weil `rechte.test.ts` den Pfad nicht in dieser Form auf einer Zeile trägt.
   Erst `grep -rl 'store/hausplanerStore'` (ohne `from '…'`) ergab die richtigen **5**. Denselben
   Fehler hatte ich bei W-37 Runde 2 schon einmal gemacht.
3. **Falsche Wortgrenze.** `grep -rlw 'modus'` ergab 6, `grep -rli` 16 — die Blattzahl **11** trifft
   `grep -rl 'modus'` (Teilwort, groß-/kleinschreibungsgenau). Erst der Vergleich von fünf Mustern
   hat das geklärt; ich hätte sonst eine richtige Zahl als falsch gemeldet.

### Beifang, zum sechsten Mal

Mein `claim_abnahme` (07:56, `docs/STATUS.md`) ist in den fremden Commit `ef273926` (planner, 07:56)
gewandert, bevor ich selbst committen konnte. Ich habe nichts daran geändert und trage es hier nur
nach; die Ursache — ein Pfad, fünf Rollen, zeilenweise — gehört dem Planner, nicht diesem Auftrag.

### Nachtrag zum Votum: ein vierter eigener Fehler, und dieser war beinahe teuer

Beim Nachziehen des `zustand:`-Feldes (die A-26-Warnung des Tors hatte zu Recht gemeldet, dass ich
in Commit `5ac659bf` nur die Tafelzeile und `ballbesitz` gesetzt hatte, nicht `zustand` im
Datensatz) habe ich `docs/STATUS.md` mit einem Python-Skript gelesen, im Speicher geändert und
ganz zurückgeschrieben. Zwischen Lesen und Schreiben lag fremde Arbeit: der 54-zeilige
plan-prüfer-Block `fernstand_ist_heute_auch_unvollstaendig`. Mein Rückschreiben hat ihn gelöscht —
`git diff --numstat` zeigte **+1 −55**.

Aufgefallen ist es nur, weil ich A-20-4 (entfernte Zeilen müssen 0 sein) **nach** dem Schreiben
gemessen habe statt es zu unterstellen. Wiederhergestellt über `git checkout HEAD -- docs/STATUS.md`
(der Block lag in `5ac659bf`, weil er dort als Beifang mitgekommen war), danach dieselbe Änderung
mit **Anker** gesetzt: `perl -0777` auf `zustand: CODE_FERTIG\nbau_sha: da2fb678`, Treffer genau 1×,
md5 vorher/nachher verglichen. Ergebnis jetzt **+1 −1**, der fremde Block steht.

**Die Lehre gegen mich selbst:** *Read-Modify-Write auf eine Datei, in die fünf Rollen gleichzeitig
schreiben, ist kein Werkzeug — es ist die Beifang-Klasse mit umgekehrtem Vorzeichen.* Bisher war ich
sechsmal ihr Opfer; hier wäre ich einmal ihr Verursacher gewesen. Ab sofort nur noch Anker mit
Treffer-genau-1× und md5-Vergleich, auch für eine einzelne Zeile.

## Votum des Evaluators (§11) — Runde 2 (Wiederabnahme)

**ABGENOMMEN.** Alle sieben Kriterien neu gefahren (§12.3/§12.4 — nicht nur das beanstandete),
gemessen im Prüfstand `c1060bab` (worktree, `node_modules` + `vendor` per `cp -al`). Die
Nachbesserung selbst gesucht statt aus dem Feld genommen: `git log 5ac659bf..HEAD | grep -i w-12`
liefert `c1060bab` (10:01) und die Meldung `8346b5fe` (10:02); `git log … -- W-12-Werkbank` bestätigt
`c1060bab` als einzigen Bau. Scope: **eine** Datei, `6-PRUEFUNG.md` (+53 −8) — §12.2 eingehalten.

### Messtisch — jede Kriterienzeile, Runde 2

| Kriterium | Urteil | womit gemessen (am Stand `c1060bab`) |
|---|---|---|
| **W-12-1-1** zwei `modus` je mit Träger | **erfüllt** | `hausplanerStore.ts:20`, `studioDaten.ts:97`, `HausplanerStudio.tsx:23` erneut geöffnet, Wortlaut unverändert; 8 Belegstellen im Blattwerk. |
| **W-12-1-2** vier Gegenstände mit Fundstelle | **erfüllt** | `szene.ts:621`/`:627`/`:212-215`, `Buehne.tsx:146`, `:1261-1269`, `OrbitControls` in 2–6 Blättern; `Buehne.tsx:62` weiter in 3 Blättern als **Nicht**-Beleg. |
| **W-12-1-3** Zugriffsart statt Zahl | **erfüllt** | selbst nachgezählt: **12** / **75** (davon **54** in `toolCatalogStillgelegt.ts`) / **9** / **0**. Rot-Probe: `id: 'wand'` = 1 — das Muster greift. |
| **W-12-1-4** W-01-Frage beantwortet | **erfüllt** | `7-GRENZEN.md:43` nennt `W-01-fang-beschreiben.md:94` weiterhin mit der ganzen Kette. |
| **W-12-1-5** Hygiene-Posten als Grenze | **erfüllt** | `uiState.ts:11` geöffnet, in 3 Blättern als *benannt und nicht angefasst*. |
| **W-12-1-6** Wächter je mit Zugriffsart | **erfüllt** *(war der Befund)* | siehe unten — jede der drei Zusagen selbst geöffnet. |
| **W-12-1-7** sieben Blätter, md5 | **erfüllt** | 63/94/63/58/**116**/85/50 Zeilen, sieben verschiedene Hashes, gegen **alle 252** Werkbank-Blätter geprüft: keine Kollision. |
| Regression Insel-Suite | **grün** | `pass 1750`, `fail 0` — Zählerstand unverändert gegenüber Runde 1. |
| Regression `tsc` | **grün** | Ausstieg 0, keine Ausgabe. |
| Bündel `public/hausplaner/hausplaner.js` | **nicht nötig** | Scope-Diff am Commit: eine `.md`, `resources/` 0. |
| §15 Testdatenbank | **nicht berührt** | 0 Treffer auf `resources/\|database\|migration\|seeder\|.sql`. |
| Browser | **nicht gefahren** | reine Dokumentation, keine sichtbare Wirkung. |
| A-20-4 am fremden Bau | **geprüft** | die 8 entfernten Zeilen sind **genau** die beanstandeten Fehlaussagen plus die `setModus`-Zeile, die wortgleich wieder eingesetzt ist. Eine widerlegte Behauptung zu streichen ist hier richtig — sie steht als Fehltreffer im Wortlaut wieder im Blatt, also ist nichts verloren. |

### Der Befund aus Runde 1 — nachgemessen, nicht nachgelesen

**(a) Die zwei Fehltreffer sind weg und als solche benannt.** Der Abschnitt führt sie jetzt im
Wortlaut auf (`eineWerkzeugzeile.test.ts:71` = Gruppenname im Markup, Reihenfolge der
Beschriftungen; `rechte.test.ts:138` = `modus` im Suchmuster, geprüft wird `rechte`) mit dem Satz
*„Keiner der beiden bewacht die Ansicht."*

**(b) Die drei echten Wächter stehen — jede Zusage selbst geöffnet:**

- `kopfrahmen.test.ts:93` **K-03**: `:98` `assert.equal(zeilen.length, 3, …)`, dann je Knopf
  `:100` `label="2D"/"Split"/"3D"`, `:102` `aktiv={modus === '2d'|'split'|'3d'}`, `:103`
  `setModus('2d'|'split'|'3d')`. `modusKnoepfe()` `:85-89` schneidet die
  `<OpGruppe name="Ansichtsmodus">` heraus und bricht mit *„die Zusage misst Leere"* ab. Das Blatt
  zitiert den Mutationskommentar `:94-96` **wörtlich richtig** — dort stehen tatsächlich **drei**
  Mutationen und der Satz *„Die Knöpfe sahen unverändert aus und zeigten den falschen Zustand an."*
- `buehne.test.ts:184-188` **K-05**: `assert.match(app, /display: modus === '3d' \? 'none' : 'block'/)`
  und `assert.doesNotMatch(buehne, /modus/)` — die Gegenrichtung, wie das Blatt sie beschreibt.
- `ansichtBereit.test.ts:159-161`: die Zeile `const breite = buehnenBreite(` darf `modus` nicht
  nennen; `:160` heißt wörtlich *„die Bestimmung der Buehnenbreite ist fort"*. Die Blattformulierung
  „**Bühnen**breite" ist damit genau, nicht ungefähr.

**(c) Die Rubrik stimmt jetzt.** Die Zuordnung zu **QUELLE** ist keine Umbenennung, sondern
gemessen: `grep -c 'store/hausplanerStore'` ergibt für alle drei Dateien **0**, während sie
`readFileSync`/`zerlegteApp` 4× / 1× / 4× benutzen.

**(d) Die Folge ist mitbehoben.** Unter *„Was NICHT geprüft wird"* steht jetzt ein eigener Abschnitt
*„Was hier ausdrücklich NICHT steht"* mit der Begründung, die ich in Runde 1 als Wirkung benannt
hatte: ein Blatt, das eine vorhandene Verriegelung verschweigt, lädt dazu ein, sie zu entfernen.

**Gegenprobe zu meiner eigenen Runde-1-Messung, beidseitig gefahren:** `Ansichtsmodus`, `K-03`,
`modusKnoepfe` kamen am Stand `5ac659bf` **0×** im Blattwerk vor (per `git grep` am alten Commit),
jetzt 4× / 3× / 2×; dazu `K-05` 2× und `ansichtBereit` 1×. Der Nachbesserungsbericht nennt dieselbe
Gegenprobe — ich habe sie unabhängig gefahren, bevor ich ihn gelesen habe.

### Eigene Messfehler dieser Runde

**Keine.** Die drei Fehler aus Runde 1 (zu enges Belegmuster, zu enges Importmuster, falsche
Wortgrenze) sind nicht wieder aufgetreten, weil ich die Muster diesmal von vornherein breit gefahren
und gegen eine Rot-Probe gestellt habe. Der vierte — das Read-Modify-Write auf `docs/STATUS.md` —
ist durch den Anker-Weg ersetzt, der hier erneut verwendet wird.

### Nachtrag: Beifang IN MEINEM Commit — zum zweiten Mal bin ich der Verursacher

`039aa7c4` hat neben meinen W-12/1-Zeilen auch den Zustandswechsel **W-16/1 `BEREIT` → `IN_ARBEIT`**
des Generators mitgenommen, an **beiden** A-20-Orten (Tafelzeile Z.73 und Datensatzfeld). Gemessen
nach dem Commit mit `git show 039aa7c4 -- docs/STATUS.md`.

**Wie es passieren konnte:** Ich habe §18 vor dem Schreiben gefahren und `M docs/STATUS.md` gesehen
— aber ab dem Moment, in dem mein eigener `claim_abnahme_r2` in der Datei stand, war „M" mein
eigener Normalzustand, und ich habe **nicht mehr getrennt gemessen, welche Blöcke fremd sind**. Die
Regel verlangt genau das: *weicht es ab, messen wer und was*. Ich habe die Abweichung gesehen und
sie mir selbst zugeschrieben.

**Ich mache nichts rückgängig.** Der mitgenommene Zustand ist inhaltlich der des Generators — er hat
W-16/1 tatsächlich begonnen; ein Zurückdrehen wäre das Nachtragen eines fremden Zustands und damit
genau das, was meine Rolle verbietet. Der Eintrag steht hier, damit der Vorgang der Zeile des
Generators zugeordnet bleibt und nicht als meine Entscheidung gelesen wird.

**Was ich ab sofort anders mache:** vor dem Tor nicht `git status` lesen, sondern den Blockvergleich
`HEAD:docs/STATUS.md` gegen den Arbeitsbaum fahren und **jeden geänderten Auftragsblock namentlich
prüfen** — den eigenen erwarte ich, jeder weitere ist fremd und der Commit wartet. Dasselbe Werkzeug
benutze ich in jedem Takt zur §18-Messung; ich habe es vor dem eigenen Commit nur nicht angewandt.

---

## Nachtrag: ein fünfter eigener Fehler, gefunden vom Release-Prüfer — nachgemessen und bestätigt

*Die zweite Release-Prüfer-Instanz meldet in
[`docs/BEFUND-ZWEI-RELEASE-PRUEFER-UND-DER-FEHLENDE-RUECKFLUSS.md`](../../BEFUND-ZWEI-RELEASE-PRUEFER-UND-DER-FEHLENDE-RUECKFLUSS.md),
meine Hash-Belegzeile ordne **116 Zeilen dem Blatt `5-CODE` zu, gemessen gehören sie zu
`6-PRUEFUNG`**. **Ich habe es nicht geglaubt, sondern selbst nachgezählt — es trifft.**

```text
$ git show c1060bab:…/W-12-ansicht-und-kamera/<blatt>.md | wc -l
  1-ZWECK 63 · 2-FUNKTION 94 · 3-FORMELN 63 · 4-BEDIENUNG 58
  5-CODE/LIESMICH 50 · 6-PRUEFUNG 116 · 7-GRENZEN 85
```

**Meine Zeile aus Runde 2, überholt** *(nicht gelöscht — A-20-4)*:

> *„63/94/63/58/**116**/85/50 Zeilen"*

**Richtig ist:** `63/94/63/58/50/116/85`. *Die drei letzten Werte stehen rotiert: `116` sitzt auf
dem Platz von `5-CODE`, `85` auf dem von `6-PRUEFUNG`, `50` auf dem von `7-GRENZEN`.*

### Woher der Fehler kam — und warum genau hier

**Meine Zeile aus Runde 1 war richtig**, am damaligen Stand nachgemessen:

```text
$ git show da2fb678:… | wc -l   ->  63 94 63 58 50 71 85
  meine R1-Zeile:                   63/94/63/58/50/71/85      ✓ deckungsgleich
```

> ***Die Nachbesserung hat `6-PRUEFUNG` von 71 auf 116 wachsen lassen** — dort sind die vier neuen
> Wächter hinzugekommen, die den Befund der Runde 1 beheben.* **Ich habe die neue Zahl richtig
> gemessen und beim Schreiben an die falsche Stelle der alten Reihe gesetzt.** *Die Menge stimmt,
> die Zuordnung nicht.*

**Das ist meine wiederkehrende Fehlerklasse in ihrer leisesten Form:** *nicht „falsch gemessen",
sondern **richtig gemessen und falsch zugeordnet**. Eine Zahl ohne Träger ist erkennbar wertlos —
eine Zahl am falschen Träger sieht aus wie ein Beleg.* **Und sie ist genau dort entstanden, wo ich
am wenigsten aufgepasst habe: in der Wiederholung einer Zeile, die beim ersten Mal gestimmt hat.**

### Was das Kriterium selbst angeht: es trägt weiter

*Selbst neu gerechnet am Abnahmestand `c1060bab`, `tail -n +2 <blatt> | md5`:*

```text
1-ZWECK          fb0251984d93541b0307cfde7e3879bd
2-FUNKTION       f3ed75ebedf0040744de7a8d2f2d4cd6
3-FORMELN        cb6cd1cf7f62d7ff2c912f4e80a1248f
4-BEDIENUNG      7d4d6ab3fd65e52c2958e860422ebc22
5-CODE/LIESMICH  a6c18488100aa242a4f2e8b0d0e3c5eb
6-PRUEFUNG       a0d8d26b313ee589fab6313c8b5316cc
7-GRENZEN        717a951ea8492530b7555e64144cc2f6
```

> **Sieben Blätter, sieben verschiedene Hashes** — *`W-12-1-7` bleibt erfüllt. **Der Fehler steckt im
> Beleg, nicht im Ergebnis**, und deshalb ändert dieser Nachtrag am Votum nichts.*

**Ich fasse den Zustand nicht an.** *W-12/1 steht hier auf `ABGENOMMEN` beim Release-Prüfer und auf
der Release-Linie bereits auf `BETRIEBSBESTAETIGT` (`3a68909a`, gemessen: **nicht** in der Historie
dieses Checkouts). Eine abgeschlossene Freigabe zurückzuholen, weil ihr Prüfer einen Fehler von mir
gefunden hat, wäre das Gegenteil dessen, was der Fund wert ist.* **Berichtigt wird der Beleg, nicht
die Entscheidung.**
