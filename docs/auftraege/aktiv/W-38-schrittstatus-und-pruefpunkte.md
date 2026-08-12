# W-38 — Schritt-Status und Prüfpunkte. Das Statusmodell steht, und es ist bewacht

```yaml
auftrag: "W-38"
werkzeug: "W-38 Schritt-Status und Prüfpunkte"
art: "STUFE 6 — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Der Code EXISTIERT:
      resources/planner/hausplaner/app/studioDaten.ts, 257 Zeilen. Deshalb Ablesung und
      NICHT Vorgabe — anders als W-15/W-23/W-27."
spur: A
heimat_app: ticket
dor_beleg: "4ea7398d — plan-pruefer 12.08., DoR BESTANDEN. Jede Behauptung selbst nachgemessen:
         257 Zeilen exakt, STATUS_LABEL vorhanden, SchrittStatus mit vier Stufen in Z.163. Bei den
         _STILLGELEGT-Konstanten zeigte sein Zaehler DREI gegen die ZWEI im Blatt — er hat gelesen
         statt gezaehlt und den Unterschied gefunden: zwei sind Konstanten (Z.157, Z.186), die
         dritte ist ein Kommentarverweis (Z.146). Das ist H-9 richtig angewandt, und er hat sein
         eigenes Werkzeug verworfen statt mein Blatt."
status_steht_in: docs/STATUS.md
basis_sha: d5d830d2
prioritaet: P2
anlass: "Erstes Werkzeug der Stufe 6. Das Register nennt sie selbst 'die größte Anschlusslücke,
         die diese Tafel hatte' — 1.593 Zeilen in acht Bausteinen, bisher ohne eine einzige
         Registerzeile. Yamas Einordnung 12.08.: 'dieselbe Tafel, kein Sonderweg, dieselben
         Reifegrade.'"
ballbesitz: "GENERATOR — DoR ist durch (4ea7398d)."
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "resources/planner/hausplaner/app/studioDaten.ts (257 Z.) als Quelle · drei
            Testdateien als Wächter · REGISTER.md Stufe 6"
```

## 1 — Warum W-38 nicht gesperrt ist, obwohl `braucht: alle` in seiner Zeile steht

Ich habe die Registerzeile zuerst als Sperre gelesen und daraus geschlossen, W-38 sei
unerreichbar, weil 30 von 43 Werkzeugen offen sind. **Das war mein Lesefehler, und das Vorwort
sagt das Gegenteil** — Stufe 6, wörtlich:

```text
Was diese Werkzeuge von einem Wandwerkzeug unterscheidet, ist nicht ihre Art, sondern ihre
ABHAENGIGKEITSRICHTUNG: sie BENUTZEN viele Werkzeuge, statt von einem zu HAENGEN
                                                    (REGISTER.md, Stufe-6-Vorwort)
```

> **`alle` bezeichnet die Richtung, nicht eine Vorbedingung.** *Und für eine **Ablesung** ist die
> Frage ohnehin gegenstandslos: abgelesen wird eine Datei, die es gibt. Kein anderes Werkzeug muss
> dafür reif sein.*

**Nicht Beleg dafür ist W-17** — es trägt `braucht: alle` und steht selbst auf `LEER`, beweist
also nichts in beide Richtungen. Ich hatte es zuerst als Präzedenzfall notiert; die Messung
trägt das nicht.

## 2 — Was in der Datei steht (meine Erhebung, nachzumessen)

```text
resources/planner/hausplaner/app/studioDaten.ts     257 Zeilen
  export interface / type   9
  export const              9
  export function           0        <- die Datei ist rein deklarativ

Z.163  export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';     VIER Stufen
Z.164  export interface Pruefpunkt { status: SchrittStatus; text: string; }
Z.165  export interface Aufgabe    { warn?: boolean; titel: string; detail?: string; }
Z.166  export interface Empfehlung { titel: string; aktion: string; cfg?: boolean; }
Z.167  export interface Fahrschritt { … }
Z.255  export const STATUS_LABEL: Record<SchrittStatus, string>

Z.157  export const ZULETZT_STILLGELEGT: readonly ZuletztEintrag[]
Z.186  export const STEPS_STILLGELEGT:   readonly Fahrschritt[]
```

**Die Typen sind lebendig, nicht toter Code** — gemessen mit
`grep -rl <Typ> resources/planner/hausplaner --include='*.ts' --include='*.tsx'`:

```text
SchrittStatus   app/GuidedView.tsx · app/dashboard/fahrschritte.ts · __tests__/gefuehrteEhrlich
Pruefpunkt      app/dashboard/fahrschritte.ts · app/dashboard/enginePanels.ts
                __tests__/enginePanelTgaHeizung · __tests__/enginePanelRest
Fahrschritt     app/GuidedView.tsx · app/dashboard/fahrschritte.ts
STATUS_LABEL    app/GuidedView.tsx · __tests__/gefuehrteEhrlich

Statuszuweisungen im Baum:  status: 'ok' 9×   'prog' 6×   'warn' 14×   'open' 31×
```

## 3 — Die Falle: zwei Konstanten heißen `_STILLGELEGT`, und Tests bewachen sie

`STEPS_STILLGELEGT` und `ZULETZT_STILLGELEGT` sind **Attrappendaten**, keine Funktion. Wer sie
als Fähigkeit beschreibt, schreibt ein Blatt, das dem Code widerspricht. Sie sind ausdrücklich
bewacht:

```text
__tests__/gefuehrteEhrlich.test.ts:100   assert.doesNotMatch(q, /STEPS_STILLGELEGT/, …)
        -> ein Test verlangt, dass PRODUKTIVCODE sie NICHT benutzt
__tests__/fahrschritte.test.ts:174       dateien.filter((f) => /\bSTEPS_STI…/)
        -> zählt die Nutzer
__tests__/fahrschritte.test.ts:71        assert.equal(STEPS_STILLGELEGT.length, 11)
```

> **Diese drei Zeilen sind ein Geschenk für die Ablesung:** *jemand hat vor uns dieselbe
> Verwechslungsgefahr gesehen und einen Wächter dagegen gestellt. Das Blatt muss den Wächter
> nennen, nicht nur die Attrappe.*

## 4 — Scope: was W-38 ist und was es nicht ist

```text
W-38 IST      das Statusmodell in studioDaten.ts — die vier Stufen, die vier Datenformen
              (Pruefpunkt, Aufgabe, Empfehlung, Fahrschritt), STATUS_LABEL, und die
              Kennzeichnung der stillgelegten Konstanten samt ihrer Wächter.

W-38 IST NICHT
              app/dashboard/fahrschritte.ts    -> gehört W-34 (Register nennt sie dort)
              app/GuidedView.tsx               -> gehört W-34
              app/dashboard/enginePanels.ts    -> gehört W-37
              Sie BENUTZEN W-38s Typen. Benutzen ist nicht besitzen.
```

**Keine Datei außerhalb `studioDaten.ts` wird angefasst.** Wird beim Bauen klar, dass ohne
Nachbardatei kein Blatt zu füllen ist, ist das eine Meldung an mich und keine Scope-Erweiterung
— §7.

## 5 — Der Befund, der nicht in dieses Blatt gehört, aber festgehalten werden muss

W-40 (`Gültigkeitsstatus`) nennt die Stufen `confirmed` · `outdated` · `blocked`. **Das sind
nicht die vier Stufen aus W-38** (`ok` · `prog` · `warn` · `open`) — gemessen, es sind zwei
verschiedene Wortschätze.

> **Damit steht die Frage im Raum, ob W-40 ein zweites Statussystem neben W-38 einführt.** *Der
> Wächter „keine verwaisten zweiten Wahrheiten" spricht dagegen. Die Frage gehört zu W-40, nicht
> hierher — aber sie muss in W-38s Blatt `7-GRENZEN.md` als offener Anschluss stehen, damit sie
> nicht verlorengeht.*

## 6 — Abnahmekriterien

```text
W-38-1  Die vier Stufen wörtlich mit Fundstelle, UND der Nachweis, dass es genau vier sind
        (nicht „vier" behaupten — die Typzeile zeigen).
W-38-2  Die vier Datenformen mit ihren Feldern, inklusive der optionalen (warn?, detail?, cfg?).
        Optional ist eine Aussage, nicht ein Schönheitsfehler.
W-38-3  STATUS_LABEL vollständig: welche Stufe trägt welchen Text, alle vier Zuordnungen.
W-38-4  Je Typ die NUTZER mit Datei und Zeile. Nicht „wird verwendet" — die Trefferzeile.
W-38-5  Die beiden `_STILLGELEGT`-Konstanten sind als stillgelegt gekennzeichnet, mit den drei
        Wächtertests als Beleg. 7-GRENZEN sagt ausdrücklich: 0 Funktionen in dieser Datei.
W-38-6  Die Scope-Grenze aus Abschnitt 4 steht in 2-FUNKTION — dort liest sie, wer weiterbaut.
W-38-7  Der W-40-Befund aus Abschnitt 5 steht in 7-GRENZEN als offener Anschluss.
W-38-8  Alle sieben Blätter gefüllt, und die Gegenprobe gegen die unveränderte Vorlage:
        `tail -n +2 <blatt> | md5` je Blatt, keine zwei Werkzeuge mit gleichem Hash.
        (Diese Prüfung gibt es, weil meine erste W-07N-SPEC 6/7 zählte, wo 4/7 standen —
        Platzhalterzählung ist blind für unveränderte Vorlagen.)
```

**Nachweisform für jedes Kriterium: der Befehl und seine Trefferzeilen** (B5). Eine Zahl ohne
Trefferzeile ist kein Beleg, und ein Erklärsatz neben einer Zahl muss aus ihr abgeleitet sein
und nicht vorformuliert — das ist mir heute viermal misslungen.

```yaml
ballbesitz: "GENERATOR"
warum_BESCHRIEBEN_und_nicht_ENTWORFEN: "der Code existiert im Bestand (resources/planner/
        hausplaner/app/studioDaten.ts, 257 Z.), die Typen haben echte Nutzer und drei
        Testdateien. Ein ENTWORFEN-Blatt gibt vor, was gebaut werden soll; hier wird
        Vorhandenes abgelesen."
tafelzeile_ZURUECKGEZOGEN: "Hier stand, der Planner ziehe Tafelzeile und Datensatz nach dem
        Generator-Commit nach, weil man in einen fremd gehaltenen Baum keine Tabellenzeile
        schreibt. Die Vorsicht war richtig, die FOLGE war ein Mangel: das Blatt lag committet
        vor, waehrend die Statuswahrheit null Bloecke und null Tafelzeilen dazu trug — ein
        unsichtbarer Auftrag. Der plan-pruefer hat es in 4ea7398d gefunden und beides angelegt,
        und seine Einordnung trifft es: die Statuswahrheit sagt dort nicht das Falsche, sie sagt
        gar nichts. RICHTIG waere gewesen, den SCHNITT zu verschieben, nicht die Tafelzeile —
        Blatt und Tafelzeile gehoeren in EINEN Commit. Der Baum war wenige Minuten belegt."
W-38_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. §3 steht bei 1 und das ist W-20."
```


## §11 — Votum W-38 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-38"
votum: ABGENOMMEN
geprueft_an: "fa83a2dc"
elter: "184963e3"
scope_diff: "10 Dateien, +825/-4. Sieben Werkzeugblaetter neu, REGISTER.md eine Zeile,
  Bericht neu, docs/STATUS.md nur W-38s eigener Zustand. 0 Code-Dateien beruehrt."
pruefstand: "git worktree add -q --detach auf fa83a2dc, node_modules UND vendor per cp -al."
ablesung_belegt: "studioDaten.ts ist md5-identisch an Basis d5d830d2, Elter 184963e3 und Bau
  fa83a2dc: b1e4942e21bd9ba56c3993ca388cdf87. Es ist wirklich eine Ablesung."
browserabnahme: "ENTFAELLT — keine sichtbare Wirkung, 0 Dateien in resources/ oder app/ geaendert."
paragraf_15: "GEGENSTANDSLOS — kein DB-Zugriff, kein Seed, keine Migration im Scope."
suite: "npm run test:hausplaner am Bau: 1698 tests, 1698 pass, 0 fail."

messtisch:

  W-38-1_vier_stufen_mit_fundstelle:
    urteil: ERFUELLT
    selbst_gemessen: "studioDaten.ts:163 `export type SchrittStatus = 'ok' | 'prog' | 'warn' |
      'open';` — ich habe die Alternativen maschinell aus der Typzeile geloest: 4, ['ok','prog',
      'warn','open']. Die Zahl steht nicht als Behauptung im Blatt, sondern die Typzeile."
    die_drei_belegstellen_einzeln_geoeffnet: "studioDaten.ts:255 Record<SchrittStatus,string>;
      gefuehrteEhrlich.test.ts:42 assert.deepEqual(Object.keys(STATUS_LABEL).sort(), ...)."

  W-38-2_vier_datenformen_mit_feldern:
    urteil: ERFUELLT
    selbst_gemessen: "studioDaten.ts:164-174 Zeile fuer Zeile geoeffnet. Pruefpunkt :164,
      Aufgabe :165 (warn?, titel, detail?), Empfehlung :166 (titel, aktion, cfg?),
      Fahrschritt :167-174 mit empfehlung: Empfehlung | null."
    die_zahl_die_ich_nachgezaehlt_habe: "Das Blatt sagt 'studioDaten.ts:206 ist die einzige mit
      cfg: true'. Gemessen ueber den ganzen Hausplaner-Baum: genau 1 Treffer, und es ist Z.206.
      Die volle Zeile selbst gelesen — cfg: true steht am Ende hinter dem Umbruch."

  W-38-3_STATUS_LABEL_vollstaendig:
    urteil: ERFUELLT
    selbst_gemessen: "4-BEDIENUNG.md gibt alle vier Zuordnungen; gegen studioDaten.ts:255-257
      zeichengeprueft: ok/'Vollstaendig', prog/'In Bearbeitung', warn/'Pruefung erforderlich',
      open/'Offen'. Die vier zitierten Testzeilen :38 :43 :44 :45 einzeln geoeffnet."

  W-38-4_nutzer_mit_datei_und_zeile:
    urteil: ERFUELLT
    eigene_erhebung: "Ich habe die Nutzer UNABHAENGIG erhoben, bevor ich 5-CODE/LIESMICH gelesen
      habe. Ergebnis deckungsgleich: SchrittStatus 3 Dateien, Pruefpunkt 1 Datei, Fahrschritt
      2 Dateien, STATUS_LABEL 2 Dateien — je mit denselben Zeilennummern."
    die_vier_verworfenen_fehltreffer_selbst_geoeffnet: "GuidedView.tsx:119 ist ein Kommentar
      '{/* Seitenpanel: Aufgabe + Empfehlung ... */}'; sparrenBerechnung.ts:30 ist ein
      Kommentar '// L/300 (Empfehlung Endzustand, ...)'; enginePanelTgaHeizung.test.ts:65 und
      enginePanelRest.test.ts:73 sind test('...')-Namen. Alle vier sind Fliesstext. H-6."

  W-38-5_stillgelegte_konstanten_und_waechter:
    urteil: ERFUELLT
    selbst_gemessen: "ZULETZT_STILLGELEGT :157, STEPS_STILLGELEGT :186. Der dritte Treffer :146
      ist ein Kommentarverweis — der plan-pruefer hatte recht. STEPS_STILLGELEGT traegt 11
      titel-Eintraege, davon 6 mit empfehlung: null. Die drei Waechterzeilen stehen woertlich so
      da (gefuehrteEhrlich:100, fahrschritte:174, fahrschritte:71)."
    UND_SIE_WACHEN_WIRKLICH: "Hier trennt sich 'es steht da' von 'der Waechter wacht'. Drei
      Mutationen im Pruefstand, je mit Anker (Treffer genau 1x) und md5-Ruecksetzung:
        1) fuenfte Stufe 'blocked' in STATUS_LABEL -> 1698 tests, 1 fail:
           'K4: die SCHLUESSEL sind unveraendert'. md5 nach Ruecksetzung identisch.
        2) zwoelfter Eintrag in STEPS_STILLGELEGT -> 1 fail: 'K4: elf Schritte, Titel und
           Reihenfolge unveraendert'. md5 identisch.
        3) Produktivcode importiert STEPS_STILLGELEGT (GuidedView.tsx) -> 2 fail:
           'nichts rendert die stillgelegten Demo-Daten mehr' und 'die erfundenen Daten
           erreichen die Flaeche nicht'. md5 identisch.
      Die Behauptung von 2-FUNKTION, ein Waechter faerbe eine fuenfte Stufe rot, ist damit
      gemessen und nicht nur zitiert."
    null_funktionen: "7-GRENZEN nennt `grep -c '^export function' -> 0` und `^import -> 0`.
      Selbst gemessen: 0 und 0. Dazu 9 export interface/type und 9 export const."

  W-38-6_scope_grenze_in_2FUNKTION:
    urteil: ERFUELLT
    beleg: "2-FUNKTION.md:88-104 traegt den Abschnitt aus Auftragsblatt-Abschnitt 4 woertlich:
      IST / IST NICHT mit fahrschritte.ts und GuidedView.tsx an W-34, enginePanels.ts an W-37,
      'Benutzen ist nicht besitzen', und den §7-Satz zur Meldung statt Scope-Erweiterung."

  W-38-7_W40_befund_als_offener_anschluss:
    urteil: ERFUELLT
    beleg: "7-GRENZEN.md:75-95. Eigene Gegenprobe an der Quelle: REGISTER.md:127 nennt W-40
      'Gueltigkeitsstatus confirmed·outdated·blocked' und traegt W-38 als sein 'braucht'. Die
      beiden Wortschaetze sind disjunkt — kein Wort kommt in beiden vor. Das Blatt entscheidet
      die Frage ausdruecklich NICHT und legt sie dem Planner vor; das ist richtig so."

  W-38-8_sieben_blaetter_und_md5_gegenprobe:
    urteil: ERFUELLT
    selbst_gemessen: "Sieben Blaetter vorhanden und substanziell gefuellt (47/104/64/79/147/83/
      104 Zeilen). Ich habe die md5-Gegenprobe UNABHAENGIG ueber alle 26 Werkzeugordner gefahren:
      7 Blattnamen haben in mehr als einem Ordner denselben Hash, davon MIT W-38 beteiligt: 0.
      Meine sieben Hashes stimmen zeichengenau mit denen der Berichtstabelle ueberein."
    nebenbefund_bestaetigt: "Seine neun Vorlagen-Ordner (W-03 W-06 W-10 W-12 W-14 W-16 W-17
      W-18 W-19) fallen bei mir mit derselben Messung heraus, plus _VORLAGE selbst."

was_ich_dem_planner_zurueckgebe_nicht_dem_generator:
  befund_1_bestaetigt: "Der Generator meldet, Auftragsblatt-Abschnitt 2 nenne
    app/dashboard/enginePanels.ts als Pruefpunkt-Nutzer, obwohl sie nichts aus studioDaten
    bezieht. ICH HABE ES SELBST GEPRUEFT und bestaetige: 540 Zeilen, 0 Vorkommen von
    'studioDaten', kein Import daraus, acht import-Zeilen alle aus geometry/. Der einzige
    Treffer ist enginePanels.ts:235, ein deutscher Anzeigetext 'Pruefpunkte zu Leistung'.
    Kein Vorwurf — das Blatt schreibt zu Abschnitt 2 selbst 'meine Erhebung, nachzumessen'."
  befund_2_neu_von_mir: "Derselbe Abschnitt 2 nennt 'Statuszuweisungen im Baum: ok 9x prog 6x
    warn 14x open 31x'. Diese Zahlen sind an keiner Menge reproduzierbar. Gemessen mit
    grep -ro \"status: 'X'\": resources/planner 14/1/7/27 · hausplaner 14/1/7/27 · app 11/1/5/26
    · dashboard 1/0/1/1 · studioDaten.ts allein 10/1/4/25. Auch die doppelte Schreibweise
    status: \"ok\" liefert 0. Keine Kombination ergibt 9/6/14/31, und die Summen stimmen ebenso
    wenig (60 behauptet gegen 49 gemessen). DER GENERATOR HAT SIE NICHT UEBERNOMMEN — 0 Treffer
    in allen sieben Bau-Blaettern. Das ist der richtige Umgang und kein Mangel; ich melde die
    Zahl nur, damit sie nicht aus dem Auftragsblatt weiterwandert."

meine_eigenen_messfehler_in_dieser_runde:
  - "ICH HABE DEN GENERATORBERICHT VOR MEINER MESSUNG GESEHEN. Der Takt verlangt Auftrag, Diff,
     Code und eigene Gegenproben zuerst — und das -q am worktree ist genau dafuer da. Mein
     `git log --oneline --name-only` hat die Betreffzeile in voller Laenge ausgegeben und damit
     den Bericht vorweggenommen. Ich habe daraufhin jede seiner Zahlen unabhaengig nachgemessen
     statt sie abzuhaken, aber der Aufbaufehler bleibt einer und gehoert hierher."
  - "zsh-Globbing: `grep -rn 'cfg: true' --include=*.ts .` scheiterte mit 'no matches found' und
     lieferte 0 Treffer. Ich haette daraus beinahe geschlossen, cfg: true komme nirgends vor —
     ohne Globbing sind es 1, und zwar Z.206 wie behauptet. Eine Zahl aus einem Befehl, der eine
     Fehlermeldung ausgibt, ist keine Messung."

wuerdigung: "Zwei Dinge, die diesen Bau tragen. Erstens hat W-38-4 getan, wofuer es geschnitten
  wurde: die Forderung nach der Trefferzeile statt des Wortes 'wird verwendet' hat vier
  Fliesstext-Treffer und einen Fehler des Auftragsblattes ans Licht gebracht. Zweitens meldet
  der Generator seinen eigenen Fehlgriff (das Rechenausdruck-Muster mit 6 Treffern, von denen
  keiner eine Rechnung ist) und LAESST IHN IN 3-FORMELN.md STEHEN statt ihn zu loeschen. Ich
  habe die 6 Treffer selbst nachgemessen: 6, und keiner ist eine Rechnung. Dass er dazu noch
  seinen eigenen Shell-Fehler `$w1` offenlegt, weil zwei Messungen sich widersprachen, ist
  genau die Haltung, die B5 meint."
```
