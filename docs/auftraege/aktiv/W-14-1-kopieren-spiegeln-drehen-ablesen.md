# W-14/1 — Kopieren, Spiegeln, Drehen. Drei Operationen, drei verschiedene Bezugsrahmen — und Drehen fehlt aus einem Schema-Grund

```yaml
auftrag: "W-14/1"
werkzeug: "W-14 Kopieren/Spiegeln/Drehen"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung) PLUS ein benannter Rest.
      Die Einordnung ist GEMESSEN: drei von vier Operationen gebaut, Drehen fehlt."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 78c09e1b
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "W-14/1 und W-14/W haben NULL Treffer in docs/STATUS.md; W-14 selbst sechs
                   (Registerzeile, Anschluss-Matrix, Fahrplan). Keine W-14-Blaetter in
                   docs/auftraege/aktiv/. Frei."
anlass: "Die dritte und letzte der Ablesungen, die ich Yama in der Vorlage zugesagt habe, nachdem
         Cluster 3 als Bau zurueckgezogen werden musste. W-14 war die Zeile, deren Einordnung ich
         schon einmal gemessen hatte (Bewegen/Duplizieren/Spiegeln gebaut, Drehen fehlt) und die die
         werkzeugLandkarte unabhaengig bestaetigt hat."
grundlage: "geometry/editierGeometrie.ts (75 Z., NEUN Exporte) · __tests__/editierGeometrie.test.ts
            (52 Z.) · app/tools/toolRegistry.ts:249 (loeschen) und :273 (duplizieren) ·
            app/HausplanerApp.tsx:110/:626-627/:671/:676-677/:695-696/:1343 ·
            app/sammelBefehle.ts:39/:68/:82/:103/:111 ·
            app/dashboard/Kopfrahmen.tsx:30/:91/:100/:315/:316 · app/rahmen/Buehne.tsx:40/:207-208 ·
            app/rahmen/EigenschaftenPanel.tsx:120 · commands/applyCommand.ts:143/:162/:176/:203 ·
            domain/scene.types.ts:193-196 (transform nur am ObjectNode) ·
            app/tools/werkzeugLandkarte.ts:96 (duplizieren)/:102 (loeschen)/:105 (verschieben)/
            :133 (versatz, marke 'fehlt') · REGISTER.md:67"
landkarten_verweise_berichtigt_14_08: "Das Feld nannte :63/:70/:73 — dort stehen heute
            Kommentarzeichen und eine Typzeile, KEIN Landkarteneintrag. Die vier Eintraege, die
            W-14 betreffen, einzeln geoeffnet und auf ihre heutige Zeile gesetzt."
```

## 1 — Der tragende Punkt: DREI Operationen, DREI Bezugsrahmen, DREI Erreichbarkeitswege

*Die Registerzeile heißt „Kopieren/Spiegeln/Drehen" und liest sich wie eine Gruppe. **Gemessen sind es
drei verschiedene Dinge**, und der Unterschied steckt nicht in der Geometrie, sondern darin, **worauf sie
sich beziehen** und **wie man sie erreicht:*

```text
(1) DUPLIZIEREN — Bezug AUSWAHL, erreichbar als REGISTRY-WERKZEUG
    toolRegistry.ts:273        id: 'duplizieren'
    HausplanerApp.tsx:671      Aufruf: else if (tool.id === 'duplizieren') dupliziere()
                       :676      function dupliziere()                    [war :671]
                       :677      executeCommands(befehleDuplizieren(...))  [ersetzt die
                                 fruehere for-Schleife :674, die es NICHT MEHR GIBT]
    sammelBefehle.ts:68        befehleDuplizieren() — die RECHNUNG liegt seit A-31 HIER
                    :82          versetzteWand(n.start, n.end, 500, 500)  [war HausplanerApp:679;
                                 in HausplanerApp.tsx kommt versetzteWand NULL Mal vor]
                                 -> die Kopie liegt 500/500 versetzt
    Landkarte: nicht als eigene fehlt-Marke; ADD_NODE deckt das Anlegen.

(2) LOESCHEN — Bezug AUSWAHL, erreichbar als REGISTRY-WERKZEUG
    toolRegistry.ts:249        id: 'loeschen'
    HausplanerApp.tsx:626      function loescheAuswahl()                  [war :621]
                       :627      executeCommands(befehleLoeschen(selectedNodeIds, nodes))
    sammelBefehle.ts:39        befehleLoeschen() — die RECHNUNG liegt seit A-31 HIER
    applyCommand.ts:162        case 'REMOVE_NODE'                          [richtig]
    Landkarte :102             { 'loeschen', 'deckt', 'REMOVE_NODE' }      [war :70]
    -> steht NICHT in der Registerzeile, ist aber Teil derselben Knopfgruppe.

(3) SPIEGELN — Bezug GANZER GRUNDRISS, erreichbar als KOPFRAHMEN-KNOPF.
    KEIN Registry-Eintrag: 'spiegeln' kommt in toolRegistry.ts nicht vor.
    Kopfrahmen.tsx:315  <OpBtn title="Grundriss links/rechts spiegeln"
                               icon="mirror-h" disabled={waende.length === 0}
                  :316  <OpBtn title="Grundriss oben/unten spiegeln"
                               icon="mirror-v"
                  :91   Props-Typ  spiegeleGrundriss: (achse: Achse) => void
                  :30   import type { Achse }
    HausplanerApp.tsx:695  function spiegeleGrundriss(achse: Achse)   [war :703]
                     :696    executeCommands(befehleSpiegeln(waende, achse))
                     :1343   durchgereicht                            [war :1356]
    sammelBefehle.ts:103   befehleSpiegeln() — die RECHNUNG liegt seit A-31 HIER
                    :111    spiegelteWand(w.start, w.end, achse, pos)  [war HausplanerApp:708]
    ^^^^ BERICHTIGT 14.08.: A-31 hat versetzteWand/spiegelteWand/bbox/achsenMitte samt
         Befehlslisten nach `app/sammelBefehle.ts` gezogen (HausplanerApp.tsx:110-111 sagt es
         selbst). Das Blatt beschrieb den Stand DAVOR — nicht nur verschobene Zeilen,
         sondern ein anderer Ort.
    -> UND DAS IST DER UNTERSCHIED, DER INS BLATT GEHOERT: der Knopf ist an
       `waende.length` gebunden, nicht an `selectedNodeIds`. Er spiegelt den
       GANZEN Grundriss. Duplizieren und Loeschen arbeiten auf der Auswahl.

(4) VERSCHIEBEN — Bezug wechselt, KEIN Werkzeug, DREI Wege
    Buehne.tsx:40/:207-208     Ziehen auf der Buehne: versetzteWand -> MOVE_NODE
    EigenschaftenPanel.tsx:120 Feld im Panel -> MOVE_NODE
    HausplanerApp.tsx:604      Wand-Laenge exakt setzen   [war :601] -> Wandende entlang der
                               Achse verschieben (MOVE_NODE)
    Landkarte :105             { 'verschieben', 'deckt', 'MOVE_NODE' }   [war :73]
```

> **Ein Blatt, das die vier in einen Topf wirft, verschweigt genau das, was beim Bauen wehtut.** *Wer
> „Spiegeln" als Auswahl-Operation erwartet — weil W-13 der Nachbar ist und weil Duplizieren es ist —
> **findet einen Knopf, der den ganzen Grundriss spiegelt**, und hält es für einen Fehler. Es ist
> keiner: es ist eine andere Operation mit demselben Namen. **H-8, der Ort ist nicht die Wirkung** —
> und dieselbe Klasse wie das Raster bei W-12/1, dessen Knopf ebenfalls im Kopfrahmen sitzt.*

## 2 — Drehen fehlt, und der Grund ist ein Schema-Grund (kein Vergessen)

```text
app/tools/werkzeugLandkarte.ts:95, Begruendung VOLLSTAENDIG gelesen:   [war :63]
  { werkzeugId: 'drehen', marke: 'fehlt', begruendung: 'Braucht Drehung um
    einen Bezugspunkt. `UPDATE_NODE` kann `transform.rotation` eines ObjectNode
    setzen, aber Wände/Öffnungen/Zonen haben keine Rotation — ihre Punkte
    müssten mitgedreht werden.' }

AM SCHEMA NACHGEMESSEN, nicht uebernommen:
  domain/scene.types.ts:193-196   transform: { position, rotation, scale }
                                  — steht am ObjectNode.
  WallNode traegt start/end (Punkte), keine Rotation.
  applyCommand.ts:203             MOVE_NODE wirft CommandAbgelehnt für Typen,
                                  die nicht definiert sind ('nicht definiert (P0)')
  -> Die Begruendung TRAEGT. Drehen ist keine fehlende Verdrahtung, sondern
     eine fehlende Operation auf Punktmengen.
```

> **Das ist der Rest, den dieses Blatt benennt und nicht baut.** *Und es ist ein **anderer** Rest als
> bei Cluster 3: dort fehlt eine Geometrie-Funktion, hier fehlt die Antwort auf eine Modellfrage —
> **um welchen Bezugspunkt wird gedreht, und was passiert mit angedockten Öffnungen?** Beides gehört
> nicht in eine Ablesung.*

## 3 — Die Geometrie: neun Exporte, und zwei davon gehören woandershin

```text
geometry/editierGeometrie.ts  75 Zeilen, NEUN Exporte (vor dem Scope gezaehlt):
  :7   Punkt              Typ
  :12  Achse              'vertikal' | 'horizontal'
  :15  versetzePunkt      Translation eines Punktes
  :20  versetzteWand      Translation einer Wand (BEIDE Endpunkte, gleicher
                          Vektor) — NICHT Parallelversatz, siehe A-29
  :34  spiegelePunkt      an einer Achse
  :46  spiegelteWand      an einer Achse
  :55  Bbox               Typ
  :63  bbox               Bounding-Box einer Punktmenge
  :73  achsenMitte        Mitte einer Bbox entlang einer Achse
TEST  __tests__/editierGeometrie.test.ts  52 Zeilen

UND DIE ZWEI, DIE NICHT ZU W-14 GEHOEREN — mit Verbraucher belegt:
  bbox         -> app/dashboard/einpassen.ts:21/:87   (Ansicht einpassen, AUF-62)
  achsenMitte  -> app/sammelBefehle.ts:23 (Einfuhr) und :108 (Aufruf)
                  sowie __tests__/editierGeometrie.test.ts:12/:43/:46/:47
  ^^^^ BERICHTIGT 14.08. — die zwei alten Verweise waren BEIDE falsch, und der erste
       auf eine Art, die jede Marke-in-der-Zeile-Pruefung durchlaesst:
       HausplanerApp.tsx:110 traegt heute den A-31-Kommentar, der woertlich sagt, dass
       achsenMitte WEGGEZOGEN ist — der Zeiger trifft das Wort und behauptet das
       GEGENTEIL dessen, was er belegen soll. Kopfrahmen.tsx:30 ist schlicht falsch:
       achsenMitte kommt in der Datei NULL Mal vor, dort steht ein Typ-Import von Achse.
       Die Scope-Begruendung war damit UNBELEGT (nicht zwangslaeufig falsch) — sie ist es
       jetzt: der echte Verbraucher ist sammelBefehle.ts, also genau das Modul, das A-31
       angelegt hat, und keiner der beiden gehoert zu W-14.
  Das steht so auch im Nachtrag von docs/WERKBANK-ANSCHLUSS.md, den ich am
  13.08. berichtigt habe: der alte Satz sagte 'bbox/achsenMitte brauchen BEIDE
  (W-13 und W-14)', und W-13 war der falsche Zweite — seine drei
  Auswahl-Module importieren editierGeometrie NULL Mal.
```

## 4 — Scope

```text
W-14/1 IST  die Ablesung des Gebauten, ueber alle Schichten und mit dem
            Bezugsrahmen je Operation:
            1-ZWECK/2-FUNKTION  die VIER Operationen, je mit ihrem Bezug
                                (Auswahl / ganzer Grundriss / einzelne Wand)
            5-CODE              editierGeometrie.ts mit ALLEN NEUN Exporten und
                                Zeilenzahl, der Test, die zwei Registry-
                                Eintraege, die Kopfrahmen-Knoepfe, die
                                Handler in HausplanerApp, die drei
                                Verschiebe-Wege, die Befehle
            3-FORMELN           AM CODE erheben. Die Registerzeile nennt F-032
                                (Transformation, homogene 4x4-Matrix) — das ist
                                zu PRUEFEN: Translation und Achsenspiegelung
                                arbeiten hier auf Punktpaaren, ohne Matrix.
                                Fehlt eine Nummer, wird die LUECKE gemeldet.
            7-GRENZEN           Drehen fehlt, mit dem Schema-Grund; Spiegeln
                                bezieht sich auf den ganzen Grundriss; die
                                Kopie liegt fest um 500/500 versetzt.

W-14/1 IST NICHT
            der BAU von Drehen. Der Rest ist benannt, nicht gebaut — und er
            haengt an einer Modellfrage (Bezugspunkt, angedockte Oeffnungen),
            die keine Ablesung entscheidet.
            eine Aenderung an Produktivcode. NULL.
            die Frage, ob Spiegeln auf die AUSWAHL umgestellt werden soll. Der
            BEFUND steht im Blatt (Abschnitt 1), die Entscheidung nicht — sie
            aendert Verhalten und gehoert Yama.
            W-13 -> Auswahl und Griffe, eigenes Blatt. Nur abgrenzen, und dabei
            den berichtigten Anschluss-Nachtrag nennen (W-13 importiert
            editierGeometrie NULL Mal).
            der Parallelversatz -> Cluster 3 im Fahrplan, eigener Vorgang.
            versetzteWand ist AUSDRUECKLICH kein Parallelversatz (A-29).
            bbox und achsenMitte als Gegenstand -> sie liegen in dieser Datei,
            ihre Verbraucher sind die Ansicht-Einpassung. Als Nachbarn nennen,
            nicht beschreiben.
```

## 5 — Abnahmekriterien

```text
W-14-1-1 (P1, TRAGEND) Jede der VIER Operationen steht im Blatt MIT IHREM BEZUG
         und ihrem Erreichbarkeitsweg:
           duplizieren  Auswahl        Registry-Werkzeug (toolRegistry.ts:273)
           loeschen     Auswahl        Registry-Werkzeug (:249)
           spiegeln     GANZER Grundriss  Kopfrahmen-Knopf, KEIN Registry-Eintrag
           verschieben  Wand/Auswahl   kein Werkzeug, drei Wege
         Ohne den Bezug liest die naechste Rolle vier gleichartige Operationen
         und findet einen Knopf, der etwas anderes tut als erwartet.
W-14-1-2 (P1) Der SPIEGEL-BEFUND ist belegt, nicht behauptet: Kopfrahmen.tsx:315
         und :316 tragen `disabled={waende.length === 0}` und die Titel nennen
         'Grundriss'; der Handler HausplanerApp.tsx:695 laeuft ueber alle Waende,
         nicht ueber selectedNodeIds. UND der Satz, dass 'spiegeln' in
         toolRegistry.ts NULL Mal vorkommt. Am Bau-Stand gegenpruefen.
W-14-1-3 (P1) DREHEN steht in 7-GRENZEN als fehlend, MIT dem Schema-Grund und
         nicht nur als Luecke: transform mit rotation gibt es am ObjectNode
         (domain/scene.types.ts:193-196), Waende tragen start/end ohne Rotation,
         und applyCommand.ts:203 lehnt MOVE_NODE fuer undefinierte Typen ab.
         Dazu die zwei OFFENEN Fragen, die ein Bau beantworten muesste
         (Bezugspunkt; angedockte Oeffnungen) — als Fragen, nicht als Vorschlag.
W-14-1-4 Die NEUN Exporte von editierGeometrie.ts sind vollstaendig genannt, mit
         Fundstelle. Am Bau-Stand zaehlen (Pruefung 7); meine Zahl ist vom
         13.08. und ersetzt die eigene nicht.
W-14-1-5 versetzteWand ist ausdruecklich als TRANSLATION beschrieben und NICHT
         als Parallelversatz — mit Verweis auf A-29, wo genau diese Verwechslung
         eine falsche Fahrplan-Einordnung erzeugt hat. Der Rumpf ist zu zitieren
         oder zu belegen, nicht zu paraphrasieren.
W-14-1-6 bbox und achsenMitte sind als NACHBARN gekennzeichnet, mit ihren
         Verbrauchern (app/dashboard/einpassen.ts:21/:87 und
         app/dashboard/Kopfrahmen.tsx:30) — und mit dem Hinweis, dass der alte
         Anschluss-Nachtrag 'brauchen BEIDE (W-13 und W-14)' am 13.08. berichtigt
         wurde, weil W-13s Auswahl-Module editierGeometrie NULL Mal importieren.
W-14-1-7 Die FORMELN sind am Code erhoben und F-032 aus der Registerzeile ist
         GEPRUEFT statt uebernommen. Wenn Translation und Achsenspiegelung ohne
         Matrix arbeiten, steht das da; eine erfundene Nummer ist schlimmer als
         eine gemeldete Luecke (Lehre aus W-21).
W-14-1-8 Kein Produktivcode. Gegenprobe: resources/planner/** kommt im
         Bau-Commit null Mal vor.
W-14-1-9 Der vorhandene Test ist gefahren, Rohausgabe im Bericht:
         __tests__/editierGeometrie.test.ts. Selbst fahren, keine Zahl von mir
         uebernehmen.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7).

```yaml
was_diese_messung_gegen_meine_eigene_notiz_geaendert_hat: "Meine Fahrplan-Notiz sagte
        'Bewegen/Duplizieren/Spiegeln gebaut, Drehen fehlt'. Das stimmt, ist aber zu grob: es
        verschweigt, dass Spiegeln einen ANDEREN BEZUG hat als Duplizieren. Erst beim Oeffnen von
        Kopfrahmen.tsx:315 ist das aufgefallen — `disabled={waende.length === 0}` statt
        selectedNodeIds. Zum dritten Mal an diesem Tag war mein Fahrplan-Eintrag richtig und zu klein
        (nach W-16/1 und W-10/1); das Muster ist stabil und gehoert in die Bilanz."
was_ich_gemessen_habe_und_was_nicht: "SELBST GEMESSEN: die neun Exporte und 75 Zeilen, die 52 Zeilen
        Test, beide Registry-Eintraege, die zwei Kopfrahmen-Knoepfe samt disabled-Bedingung, die
        Handler-Rumpfe von dupliziere und spiegeleGrundriss, die drei Verschiebe-Wege, dass 'spiegeln'
        in toolRegistry.ts nicht vorkommt, die Landkarten-Begruendung zu drehen VOLLSTAENDIG (nicht
        abgeschnitten — die Lehre aus Cluster 3), und dass transform nur am ObjectNode steht.
        NICHT GEMESSEN: was der Test inhaltlich prueft (52 Zeilen, beim Bau zu lesen), und ob die
        feste 500/500-Versetzung der Kopie irgendwo begruendet ist — sie steht als Zahl im Rumpf, und
        ob das eine Absicht oder ein Platzhalter ist, habe ich nicht belegt. Als offene Stelle
        genannt, nicht als Befund."
W_14_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

---

## Votum des Evaluators (§11) — Runde 1

**ABGENOMMEN.** *Neun von neun Kriterien tragen, jede Fundstelle selbst geöffnet. Und das Blatt hat
an einer Stelle eine Angabe des Auftrags **berichtigt statt übernommen** — an genau der, die ich zum
Befund gemacht hätte, wenn er sie abgeschrieben hätte.*

### Der Punkt, an dem ich einen Befund erwartet habe — und keinen fand

*Kriterium `W-14-1-6` nennt als Verbraucher von `bbox` und `achsenMitte`:
`einpassen.ts:21/:87` **und `Kopfrahmen.tsx:30`**. Ich habe `:30` geöffnet:*

```text
$ sed -n '30p' app/dashboard/Kopfrahmen.tsx
import type { Achse } from '../../geometry/editierGeometrie';
$ grep -c 'achsenMitte' app/dashboard/Kopfrahmen.tsx   ->  0
```

> **Der Kopfrahmen verbraucht `achsenMitte` nicht — er importiert einen Typ.** *Wer die
> Auftragsangabe abgeschrieben hätte, hätte einen Verbraucher behauptet, den es nicht gibt.*

**Das Blatt schreibt stattdessen** (`5-CODE:16`): *„`app/dashboard/Kopfrahmen.tsx:30` `type Achse`
**(nur der Typ)**"* — **und nennt den echten Verbraucher**, den ich selbst gesucht habe:

```text
$ grep -rn 'achsenMitte' resources/planner/hausplaner/
  app/sammelBefehle.ts:23    import … achsenMitte …
  app/sammelBefehle.ts:108   const pos = achsenMitte(b, achse);
  geometry/editierGeometrie.ts:73   (die Definition)
  __tests__/editierGeometrie.test.ts   (der Wächter)
```

*Das Blatt führt `app/sammelBefehle.ts:23` als Anschluss (`5-CODE:12`).* **Vierte Stelle heute, an
der eine Auftragsangabe gemessen statt geglaubt wurde** — *nach `geradenSchnitt` (W-18/1), dem
A-32-Zustand (W-03/1) und der Registerzeile F-032 hier.*

### Messtisch — alle neun Kriterien, jede Fundstelle selbst geöffnet

| Kriterium | Ergebnis | Beleg |
|---|---|---|
| **W-14-1-1** (P1, TRAGEND) vier Operationen mit Bezug und Weg | **grün** | Registry selbst gezählt: `duplizieren` **1** (`:273`), `loeschen` **1** (`:249`), `spiegeln` **0**, `verschieben` **0`. Beide Zeilen geöffnet und bestätigt. Die Bezüge stehen in `1-ZWECK:16` (Grundriss gegen Auswahl) |
| **W-14-1-2** (P1) Spiegel-Befund belegt | **grün** | `Kopfrahmen.tsx:315/:316` geöffnet: `disabled={waende.length === 0}`, Titel *„Grundriss links/rechts spiegeln"*. `HausplanerApp.tsx:695` `function spiegeleGrundriss(achse)` → `executeCommands(befehleSpiegeln(waende, achse))` — **über `waende`, nicht `selectedNodeIds`**; `waende` ist `waendeAus(nodes)` (`:365`), also der ganze Grundriss. `'spiegeln'` in `toolRegistry.ts`: **0** |
| **W-14-1-3** (P1) Drehen mit Schema-Grund + zwei offene Fragen | **grün** | `scene.types.ts:193-196` `transform: {position, rotation, scale}` geöffnet; `WallNode` (`:98-104`) trägt `start`/`end`/`thickness`, **keine Rotation**. `commands/applyCommand.ts:203` `throw new CommandAbgelehnt('MOVE_NODE für Typ … nicht definiert (P0)')`. Die zwei Fragen stehen als **Fragen** (`7-GRENZEN:26`, `:30`) |
| **W-14-1-4** (P1) neun Exporte mit Fundstelle | **grün** | `grep -c '^export '` → **9**, Datei **75** Z; die Tabelle nennt genau diese neun mit 7/12/15/20/34/46/55/63/73 |
| **W-14-1-5** `versetzteWand` als Translation, A-29 | **grün** | Rumpf geöffnet: `{start: versetzePunkt(start,dx,dy), end: versetzePunkt(end,dx,dy)}` — **beide Endpunkte um denselben Vektor**. A-29 an vier Stellen benannt, dazu der Verweis, dass der echte Parallelversatz seit A-32 in `geradenGeometrie` liegt |
| **W-14-1-6** `bbox`/`achsenMitte` als Nachbarn | **grün** | s. o. — **besser als das Kriterium**. `einpassen.ts:21/:87` selbst geöffnet; W-13s Auswahl-Module importieren `editierGeometrie` **0×**, selbst gemessen |
| **W-14-1-7** F-032 geprüft statt übernommen | **grün** | selbst gezählt, **Vorkommen statt Zeilen**: `Math.round` **6**, `Math.max` **2**, `Math.min` **2**, `Math.cos`/`sin`/`atan2` **0**, `matrix`/`transform`/`rotation` **0**. Das Blatt nennt exakt diese Zahlen und sagt zusätzlich, **warum** F-032 plausibel aussah |
| **W-14-1-8** kein Produktivcode | **grün** | `5a2e70b1`: 8 Dateien, **0** außerhalb `docs/` |
| **W-14-1-9** Test selbst gefahren | **grün** | `tests 8 · pass 8 · fail 0`; Datei 52 Z, **8** Zusagen selbst gezählt |
| **Wächter** Insel-Suite | **grün** | `tests 1750 · pass 1750 · fail 0` |
| **Wächter** `tsc:hausplaner` | **grün** | Exit 0 |
| **sieben Blätter** | **grün** | `64/101/57/54/69/58/87` Z, **sieben verschiedene** md5 |
| **Registerzeile** | **grün** | `**keine** ⓝ (~~F-032~~)` in der Hausform, 5 Spalten wie die Nachbarn |
| **Browser** | **nicht gefahren** | *der Bau-Commit fasst ausschließlich `docs/` an* |
| **§15 Datenbank** | **nicht berührt** | *kein schreibender Lauf* |

### Mein eigener Messfehler in dieser Runde

**Ich habe Zeilen gezählt, wo Vorkommen zu zählen waren.** *Mein `grep -c 'Math\.'` auf
`editierGeometrie.ts` ergab **4** — das Blatt sagt `6+2+2 = 10`. Einen Moment sah es nach einer zu
hohen Zahl im Blatt aus.* **Nachgemessen mit `grep -o … | wc -l`: 6/2/2, das Blatt stimmt.**

> ***`grep -c` beantwortet eine andere Frage als die, die ich gestellt habe*** — *„in wie vielen
> Zeilen" statt „wie oft".* **Dieselbe Klasse, gegen die ich prüfe: ein Zählwort, das den Namen der
> Sache trägt und etwas anderes misst.** *Und es hätte diesmal den Bau getroffen, nicht mich.*

### Weitergabe

**Ball an den Release-Prüfer** (§11, ABGENOMMEN).

### Nachtrag: derselbe Beinahe-Fehler wie um 23:18 — halb behoben ist nicht behoben

**Ich hatte die Lehre gezogen und nur zur Hälfte angewandt.** *Den `zustand`-Anker habe ich
blockgenau gebunden (`auftrag`-Zeile + `zustand` in einem Muster). Den `ballbesitz`-Anker nicht — und
er hatte **zwei** Treffer:*

```text
ballbesitz: evaluator  # CODE_FERTIG 14.08. generator
  W-14/1   (meiner)
  W-10/1   (FREMD — derselbe Wortlaut, ein anderer Auftrag)
```

**Nachgewiesen, dass der richtige getroffen wurde** *(ballbesitz-Vergleich aller Blöcke, HEAD gegen
Baum)*:

```text
W-14/1  evaluator # CODE_FERTIG …  ->  release-pruefer # ABGENOMMEN …
W-10/1  unveraendert
```

> ***Und wieder hat allein die Dateireihenfolge entschieden*** — *W-14/1 steht vor W-10/1.* **Zum
> zweiten Mal in dreißig Minuten war die Sicherung nicht der Anker, sondern der Zufall.**
>
> **Was daran neu ist und weshalb es hier steht:** *ich habe die Lehre aus 23:18 gezogen und sie auf
> **ein** Feld angewandt statt auf alle. Eine Regel, die man auf die Stelle anwendet, an der man
> gestolpert ist, fängt die nächste Stelle nicht.* **Ab sofort blockgenau für jedes Feld, das ich in
> `STATUS.md` ersetze — `zustand`, `ballbesitz`, jedes weitere.**
