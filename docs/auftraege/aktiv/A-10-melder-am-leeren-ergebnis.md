# A-10 — Der Melder greift am Wurf, nicht am leeren Ergebnis

```yaml
auftrag: A-10
titel: "Ein Dach, das KEINE Flaeche liefert, wird gemeldet - auch ohne Ausnahme"
basis_sha: d58b220e
prioritaet: P2
art: "Folgeauftrag nach §12.5 - A-01 bleibt RELEASE_FREI, hier wird nichts rueckwirkend geaendert"
anlass: "b29bb79d (A-05-Abnahme), Gegenprobe E4b des Evaluators"
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
claim: "planner 10.08. 18:2x — Claim VOR der Nacharbeit gesetzt. Das Blatt lag 45 h unberuehrt
        (mtime 08.08. 14:52), ballbesitz stand auf planner, KEIN Claim lag darauf; die zweite
        Planner-Instanz hat A-09 geclaimt, A-10 nicht. Zwei Restpunkte der 1. DoR-Runde
        (9c63da13) eingearbeitet plus die dort ausdruecklich als 'kein Blocker' genannte
        Empfehlung."
```

## Der Befund

**A-01-4 hat den stillen Ausfall beseitigt — aber nur auf einem der beiden Wege dorthin.**

```text
WURF-PFAD    dachFlaechen wirft DachGeometrieUngueltig
             -> szene.ts faengt, nichtDarstellbar() meldet, die 3D zeigt den Hinweis   GEBAUT

LEER-PFAD    l-shape ohne `anbau` wirft NICHT - es liefert { dreiecke: [] }, 0 Flaechen
             -> kein Wurf, kein Fang, Melder []                                        LUECKE
```

> **Ein Dach, das nichts zeigt und nichts sagt** — genau der Zustand, gegen den A-01-4 gebaut
> wurde. *Dass die Lücke auf dem anderen Pfad liegt, macht sie nicht kleiner; sie macht den Melder
> unvollständig.*

**Rot-Beleg, dreifach unabhängig gemessen:**

```text
Generator (9e97d274)       dachMeshWelt -> {dreiecke:[]}, dachflaechen -> 0 Flaechen
Generator (e0fae829)       A-05-3: laedt schema-gueltig, bleibt still leer
Evaluator (b29bb79d, E4b)  eigene Wegwerf-Zusage 12/12: Melder []
                           - und der Melder greift am Wurf-Pfad, die Luecke ist spezifisch
```

## Wiederverwendungsprüfung (§5, Fassung 1.2.2)

```text
renderers/three-d/szene.ts     nichtGezeichnet[] + nichtDarstellbar() - der Melder EXISTIERT,
                               beide Faenger (Z.498 / Z.544) sind gebaut
app/DreiDBereich.tsx           die Hinweis-Anzeige (role="status") existiert, mit T-Tokens
__tests__/dachAusKontur.test.ts  A-01s Zusagen - must_preserve
geometry/dachGeometrie.ts:87   die Absage auf dem Wurf-Pfad - unangetastet
```

**Es wird nichts Neues gebaut.** *Der Melder bekommt eine zweite Eingangsbedingung — leeres
Ergebnis statt nur Ausnahme. Anzeige, Tokens und Zusagen stehen bereits.*

## Akzeptanzkriterien

**A-10-1 (P1):** Liefert die Dachberechnung **null Flächen**, ohne zu werfen, meldet
`nichtDarstellbar()` das Dach mit einem lesbaren Grund. *Rot heute: Melder `[]`.*

**A-10-2 (`must_preserve`-KONTROLLE — von der Rot-Pflicht nach §5 AUSGENOMMEN):** Ein Dach, das
**Flächen liefert**, wird **nicht** gemeldet. *Ohne dieses Kriterium wäre „melde immer" grün.*

> **Warum ausgenommen statt gestrichen — Restpunkt 1 der 1. DoR-Runde (`9c63da13`).** Der
> Plan-Prüfer hat richtig gemessen: dieser Fall ist an der Basis **grün**, und zwar trivial —
> heute wird *überhaupt nichts* gemeldet, also auch kein Flächen-Dach. Als P1 geführt hätte das
> Blatt gegen „kein Kriterium ist bereits erfüllt" verstoßen.
>
> Es bleibt stehen, weil es der **Gegenhalter zu A-10-1** ist: ohne es wäre „melde immer" eine
> vollständig grüne Lösung. Gleiche Bauart wie `A-01-2`, `A-02-1` und `A-08-2` — das ist inzwischen
> das vierte Kriterienpaar dieser Form, und das Muster ist damit belegt, nicht geraten.

**A-10-3 (`must_preserve`):** **Alle A-01-Zusagen bleiben grün**, insbesondere der Wurf-Pfad und
die Fußleisten-Absage. *§7: keine Abschwächung bestehender Tests.*

**A-10-4 (Sichtkette, P2):** Die Browserabnahme zeigt den Hinweis über einem geladenen
`l-shape`-Dokument. **Hier gehört die Sicht-Ebene hin** — *in A-05 war sie zwecklos, weil dort
nichts gebaut wurde; hier belegt sie, dass der Nutzer die Meldung wirklich sieht.*

**A-10-5 (P1, Mutationsprobe — Empfehlung der 1. DoR-Runde, aufgenommen):** Mindestens **drei**
Mutationen fallen: **die neue Leer-Bedingung entfernt** · ihr Ergebnis ignoriert (geprüft, aber
nicht gemeldet) · die Bedingung so verengt, dass sie nur bei `dreiecke.length === 0` greift und
`dachflaechen === 0` übersieht.

> **Warum aufgenommen, obwohl ausdrücklich „kein Blocker".** Nach dem Vorbild `A-08-6`: eine
> Bedingung ohne Mutationszusage ist **stumm entfernbar** — jemand nimmt sie im nächsten Umbau
> heraus, die Suite bleibt grün, und der Befund kommt in Monaten zurück. Genau diese Lage hatte
> A-08 (dort war `&&` → `||` die eine Mutation, die alle Schutzbedingungen gleichzeitig entwertete
> und jeden Einzeltest grün ließ). Die Zusage kostet wenig und macht die Bedingung dauerhaft
> sichtbar.

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten     KEINE
Produktivcode    renderers/three-d/szene.ts (+ ggf. app/DreiDBereich.tsx)
Bundle           JA - Insel, `npm run build:hausplaner` gehoert ins Tor
Testdaten-Ziel   KEINES (Fixture a01-bestandsdokument-l-dach.json liegt bereits)
Prozessbindung   Browserabnahme nach §8 - Anker-Regel gilt, Buehne nur ueber browser-buehne.sh
Werkzeuge        npm run test:hausplaner - vorhanden UND in Gebrauch
```

**Konfliktprüfung (§5) — Restpunkt 2 der 1. DoR-Runde (`9c63da13`), hier nachgetragen:**

*Selbst an den Blättern nachgemessen, nicht aus der DoR-Notiz übernommen — zwei Angaben dort waren
ungenau:*

```text
A-04  IN_ARBEIT     scripts/buehnen-waechter.sh + __tests__/buehnenWaechter.test.mjs
                    NICHT commit-pruefen.sh — noch weiter entfernt als angenommen
A-07  ENTWURF       scripts/commit-pruefen.sh + commitPruefen.test.mjs
A-09  ENTWURF       scripts/commit-pruefen.sh + commitPruefen.test.mjs
A-10  DIESES        renderers/three-d/szene.ts (+ ggf. app/DreiDBereich.tsx)
-> disjunkt zu allen drei. A-10 darf PARALLEL laufen, kein aktives Blatt teilt eine Datei.

ABER — vom Plan-Pruefer nicht genannt und hier ergaenzt:
A-01  RELEASE_FREI  beruehrt DIESELBE Datei: "szene.ts — die zwei Faenger melden statt zu
                    schlucken" (A-01 Z.56, Faenger an szene.ts:498 und :544).
                    A-10 baut auf genau diesem Bau auf. Kein PARALLEL-Konflikt (A-01 ist
                    abgeschlossen), aber der Grund, warum A-10-3 kein Formalismus ist:
                    dieselbe Datei, derselbe Mechanismus, eine Stelle weiter.
```

> **Warum die Zeile trotz Disjunktheit ins Blatt gehört.** Der Nachweis, dass *kein* Konflikt
> besteht, ist genauso Arbeit wie der Nachweis eines Konflikts — und er fehlte schon zweimal:
> bei A-08 hat der Plan-Prüfer die Kollision mit A-07 selbst ergänzt (*„fehlte in BEIDEN
> Dokumenten"*), und daraus wurde die festgelegte Baureihenfolge. Ohne die Zeile muss die nächste
> Rolle sie erneut herleiten.
>
> *Nebenbei beim Messen aufgefallen, gehört nicht in dieses Blatt: **weder A-07 noch A-10 haben
> einen eigenen `## Scope`-Abschnitt.** Bei A-10 steht der Produktivcode im §5-Block, bei A-07
> ist er nur über die Konfliktprüfung von A-08 belegt. Kein Mangel dieses Auftrags, aber eine
> Formlücke, die das Messen von Konflikten jedes Mal teurer macht als nötig.*

**Erstnutzer** (§5 1.2.2 — der Melder ist vorhanden, die Bedingung ist neu): **der Evaluator bei
der nächsten Browserabnahme eines Bestandsdokuments.**

## Nicht-Ziele

- **Keine L-, T-, U-Dächer bauen.** *A-01s Nicht-Ziel bleibt (Entscheidung `bd1383c8`, gestützt auf
  die Achtpunkt-Lückenliste). A-10 macht die Lücke **sichtbar**, nicht kleiner.*
- **Keine Änderung an `dachGeometrie.ts:87`** — der Wurf-Pfad ist gebaut und abgenommen.

## Rückweg

Eine Bedingung in `szene.ts`, `git revert` genügt. **Kein Zustand außerhalb des Repos betroffen.**

---

## §11-Bericht des Generators (10.08.)

```yaml
auftrag: A-10
basis: d58b220e          # Blatt-Basis; Bau-Basis (HEAD bei Uebernahme) 8343f206 — Scope-Diff d58b220e..8343f206 leer
commit: dbb7ff66         # Bau: nichtDarstellbar.ts + dachAusKontur.test.ts + Bundle
scope:
  - resources/planner/hausplaner/renderers/three-d/nichtDarstellbar.ts
  - resources/planner/hausplaner/__tests__/dachAusKontur.test.ts
  - public/hausplaner/hausplaner.js          # Bundle — §5: gehoert ins Tor, frisch gebaut
  - docs/auftraege/aktiv/A-10-melder-am-leeren-ergebnis.md
  - docs/STATUS.md
tests:
  statisch: pass          # npm run tsc:hausplaner — exit 0
  unit: "1692/1692"       # npm run test:hausplaner; an der Basis selbst gefahren: 1689/1689
  backend: nicht_anwendbar
  schema: pass            # schema:hausplaner:check laeuft im Vorspann von test: und build:
  build: pass             # npm run build:hausplaner; Bundle traegt den neuen Grund (grep -c = 1)
  browser: pass           # Buehne NUR ueber browser-buehne.sh (Port 8099), Waechter vorab, Anker dreistufig
abweichungen:
  - "szene.ts und DreiDBereich.tsx UNVERAENDERT — die zweite Eingangsbedingung sitzt vollstaendig in
     nichtDarstellbar.ts, dem EINEN Ort aus A-01-4; das '+ ggf.' des Blatts wurde nicht gebraucht."
  - "Mutation 3 (verengt auf nur dreiecke.length===0) ist am heutigen Code im VERHALTEN nicht von der
     vollstaendigen Fassung zu trennen: dreiecke==0 erzwingt dachflaechen()==0, beide lesen dieselbe
     Quelle dachRoh. Die Zusage 'A-10-5 ZEUGEN' haelt sie darum STRUKTURELL fest (liest Quelltext,
     Grenze offen benannt — Vorbild 'A-01-4 OBERFLAECHE'). Die Gegenrichtung ist doppelt gedeckt:
     &&->|| faellt an A-10-2 am VERHALTEN, denn ein l-shape MIT anbau hat 10 Dreiecke und
     dachflaechen()=0 (gemessen) — eine Oder-Fassung meldete dieses zeichenbare Dach."
  - "Probedaten fuer die Browserabnahme in ticket_testing angelegt (new_leads 8160, Objekt 10229,
     Testnutzer a10-test@example.test, HausplanerDocument 36: das a01-Fixture mit roofType 'l-shape',
     OHNE anbau). Grund: die Blatt-Zeile 'Testdaten-Ziel KEINES' verweist auf das a01-Fixture — das
     traegt aber roofType 'sattel' und zeigt im Browser den WURF-Pfad, nicht den Leer-Pfad. Nur
     Testdatenbank, keine Produktivdaten, kein Scope-Code."
offene_akzeptanz: []
```

### Kriterienstand

```text
A-10-1  GRUEN  Zusage 'A-10-1' (Verhalten): l-shape ohne anbau -> 1 Meldung, nodeId korrekt, Grund
               lesbar (nennt die fehlende Flaeche). An der BASIS-Fassung rot gemessen (Melder []).
A-10-2  GRUEN  Zusage 'A-10-2 KONTROLLE' (must_preserve-KONTROLLE, laut Blatt von der Rot-Pflicht
               ausgenommen — an der Basis wie deklariert gruen): Sattel-Rechteck NICHT gemeldet UND
               l-shape MIT anbau NICHT gemeldet (der scharfe Fall: dreiecke>0, dachflaechen()=0).
A-10-3  GRUEN  Alle A-01-Zusagen gruen — an Basis-Fassung UND Bau-Fassung selbst gefahren (13/13
               bzw. 15/15 im File, Suite 1689/1689 -> 1692/1692, +3 = exakt die neuen Zusagen).
               Wurf-Pfad und Fussleisten-Absage unangetastet; dachGeometrie.ts:87 NICHT geaendert
               (Datei nicht im Diff). Keine bestehende Testzeile veraendert, nur ergaenzt.
A-10-4  GRUEN  Browserabnahme, Rohausgaben unten: Hinweis ueber dem geladenen l-shape-Dokument in
               1440/1024/375, role="status"; Gegenprobe u-dach-Fixture OHNE Hinweis.
A-10-5  GRUEN  Drei Blatt-Mutationen + Zugabe einzeln eingespielt, Suite je Lauf, byte-identisch
               zurueckgesetzt (md5 746b68c2 vor und nach jeder Probe).
```

### Rohausgaben (Auszug; Befehle im Wortlaut)

```text
BASIS-SUITE   npm run test:hausplaner @ 5fc9c9e2 (vor dem Bau)   tests 1689  pass 1689  fail 0
BAU-SUITE     npm run test:hausplaner @ dbb7ff66                 tests 1692  pass 1692  fail 0
TSC           npm run tsc:hausplaner                              exit 0
BUILD         npm run build:hausplaner                            "built in 1.17s"; grep -c "liefert keine einzige Fläche" public/hausplaner/hausplaner.js = 1

ROT-LAGE (§7, ZWEIMAL identisch, eigene Probe am Basis-Code):
  dachMeshWelt.dreiecke.length = 0   dachflaechen.length = 0   nichtDarstellbareDaecher = []
  MIT anbau: dreiecke = 10 | dachflaechen = 0 | melder = []

BASIS-ROT der neuen Zusagen (Basis-nichtDarstellbar.ts eingespielt, Testfile des Baus):
  ✖ A-10-1 (Melder [])   ✔ A-10-2 (wie deklariert)   ✖ A-10-5   — alle 12 A-01-Zusagen ✔
MUTATIONEN (je: einspielen -> Lauf -> md5-Wiederherstellung 746b68c2):
  M1 Bedingung entfernt            -> ✖ A-10-1  ✖ A-10-5          (pass 13, fail 2)
  M2 geprueft, nicht gemeldet      -> ✖ A-10-1  ✖ A-10-5          (pass 13, fail 2)
  M3 verengt auf nur dreiecke==0   -> ✖ A-10-5                    (pass 14, fail 1)
  M4 Zugabe && -> ||               -> ✖ A-10-2 (VERHALTEN) ✖ A-10-5 (pass 13, fail 2)

BROWSER (Buehne: bash scripts/browser-buehne.sh --port 8099 — "Datenbank am Kindprozess geprueft:
ticket_testing"; Pflichtschritt vorab: bash scripts/buehnen-waechter.sh -> "ALLE BUEHNEN OK,
1 geprueft, Datenbank jeweils 'ticket_testing'" [die bekannte verwaiste PID 48098, unangetastet]):
  HTTP 200 · TITEL "SA-DESK - Hausplaner — Objekt #10229"
  ANKER-1 {"root":true,"szene":true,"szeneRoofType":"l-shape"} · Expertenmodus geklickt · canvas 0->2
  MESSUNG-1440 {"canvas":2,"status":["Ein Dach wird hier nicht gezeigt: die Berechnung der Dachform
    „l-shape\" liefert keine einzige Fläche — es gibt nichts zu zeichnen"]}
  MESSUNG-1024 identisch · MESSUNG-375 identisch + der bestehende Schmalbild-Hinweis (Bestand)
  GEGENPROBE-U-DACH (studio?fixture=u-dach, gleicher Weg) {"canvas":2,"status":[]}
  Konsole: KEIN Hausplaner-/three-/WebGL-Fehler; das uebrige Rauschen (Reverb-WS 6001, HR-Widget,
  404-Legacy) erscheint auf jeder CRM-Seite und ist vom Diff unberuehrt (Bestand).
  Screenshots: a10-1440.png / a10-1024.png / a10-375.png / a10-gegenprobe-u-dach.png (Scratchpad
  der Generator-Sitzung; der Evaluator faehrt die Kette ohnehin selbst).

Ich baue und melde CODE_FERTIG — ich nehme NICHT ab.
```

---

## Evaluator-Votum (§11) — 10.08.2026

```yaml
auftrag: A-10
commit: 47c0aa73          # Pruef-SHA; Bau dbb7ff66, Elter 165239e5
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "Rot-Lauf der neuen Zusagen gegen den Elter-Melder · drei eigene Mutationen ·
  Bundle frisch gebaut und md5 verglichen · eigene Browserabnahme 1440/1024/375 mit Sichtpruefung
  im Viewport statt Existenzpruefung"
browser: 1440/1024/375 — Hinweis in ALLEN drei Breiten im Fenster sichtbar
datenbank: ticket_testing (am Kindprozess belegt, browser-buehne.sh)
befunde: []
```

### Selbst gefahren

```text
Scope-Diff dbb7ff66   nichtDarstellbar.ts +29 · dachAusKontur.test.ts +67 · public/…/hausplaner.js
tsc (tsconfig.hausplaner)  Ausgang 0
Suite Pruefstand      tests 1692  pass 1692  fail 0
Suite Elter 165239e5  tests 1689  pass 1689  fail 0
Rot am Elter          A-10-1 und "A-10-5 ZEUGEN" fallen; A-10-2 ist die deklarierte
                      must_preserve-KONTROLLE und dort gruen — korrekt so
```

**Bundle-Nachweis (§8) — das getrackte Artefakt stammt aus den Quellen:**

```text
md5 mitgeliefert   57314651a743ef689b0d788c23db7493
npm run build:hausplaner  -> Ausgang 0
md5 danach         57314651a743ef689b0d788c23db7493   BYTE-GLEICH
```

**Drei Mutationen aus A-10-5, Anker je genau 1×, `md5` vor und nach identisch:**

```text
M1 Leer-Bedingung entfernt                 fail 2   GEFANGEN (A-10-1 + ZEUGEN)
M2 Ergebnis ignoriert (geprueft, nicht gemeldet)  fail 2   GEFANGEN
M3 auf dreiecke.length === 0 verengt       fail 1   GEFANGEN (ZEUGEN)
```

### A-10-4, die Sichtkette — und mein eigener Messfehler dabei

**Erst der Wächter, wie A-04 es dem Evaluator als Erstnutzer vorschreibt:**

```text
bash scripts/buehnen-waechter.sh
  BUEHNE OK  PID 48098 (php -S) — 'ticket_testing' (aus ticket-a01/.env.testing via APP_ENV=testing)
  ALLE BUEHNEN OK   1 geprueft            exit 0
bash scripts/browser-buehne.sh --port 8123
  BUEHNE   Datenbank am Kindprozess geprueft: ticket_testing
```

**Gemessen wurde die SICHTBARKEIT im Fenster, nicht die Existenz** — Lehre aus A-01-4:

```text
Objekt #10229, Dokument 36, szeneRoofType "l-shape", Expertenmodus, dann 3D
MESSUNG-1440  canvas 2 · status "…die Berechnung der Dachform „l-shape" liefert keine
              einzige Fläche — es gibt nichts zu zeichnen"   imFenster: true
MESSUNG-1024  identisch, imFenster: true
MESSUNG-375   identisch, imFenster: true  + der dokumentierte Schmalbild-Hinweis (Bestand)
Screenshot 1440 gesichtet: der Hinweis steht orange umrandet ueber der 3D-Flaeche.
```

> **Mein Messfehler, offengelegt:** mein erster Lauf blieb in der **2D**-Ansicht stehen. Dort
> trägt das `role="status"`-Element den Text, hat aber **0×0 px** — ich hatte bereits die
> Geometrie vermessen und stand kurz davor, „Hinweis vorhanden, aber unsichtbar" als P1 zu
> melden. **Das wäre falsch gewesen:** der Melder gehört zum 3D-Renderer, und in 3D ist der
> Hinweis sichtbar. *Erst der Kontrollblick auf den Screenshot hat es gezeigt — dieselbe Lehre
> wie am 03.08. beim `#hausplaner-scene`: der Zustand vor dem entscheidenden Schritt misst nichts.*

### In eigener Sache

**Testdaten:** Für den Login habe ich in `ticket_testing` einen eigenen Nutzer angelegt —
`evaluator-a10@example.test`, **id 269**, `is_admin=1`. *Schreibziel vor dem Schreiben belegt
(`SCHREIBZIEL: ticket_testing`).* **Er ist nicht gelöscht** — §15 und die Dauerregel; er gehört
zusammen mit dem Generator-Nutzer (id 268) in eine Aufräumung mit eigenem Auftrag.

**Prüfstand:** `node_modules` **und** `vendor` verlinkt, Prüfstand mit `-q` angelegt und
Scope-Diff mit `--pretty=format:''` — die beiden Barrieren aus der §13-Prüfung, damit mir der
Bericht nicht vor der Messung vor Augen steht.
