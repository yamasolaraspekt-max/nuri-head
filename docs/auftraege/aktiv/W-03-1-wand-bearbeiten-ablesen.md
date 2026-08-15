# W-03/1 — Wand bearbeiten. Über Eigenschaften geht es, als Geometrie-Operation nicht — und die fünf Lücken haben seit heute ihr Fundament

```yaml
auftrag: "W-03/1"
werkzeug: "W-03 Wand bearbeiten"
art: "STUFE B — Blatt schneiden, Ziel BESCHRIEBEN (Ablesung). Die Einordnung ist GEMESSEN:
      Bearbeitung ueber das Eigenschaften-Panel gebaut, geometrische Operationen nicht."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: e097e7be
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "W-03/1 und W-03/W haben NULL Treffer in docs/STATUS.md; W-03 selbst zwei
                   (Registerzeile, Anschluss-Matrix). Keine W-03-Blaetter in docs/auftraege/aktiv/.
                   Frei."
anlass: "Die letzte der zehn B-Zeilen ohne Blatt. Und der Zeitpunkt ist nicht beliebig: A-31
         (Sammel-Ausfuehrung im Store) ist ABGENOMMEN und A-32 (Geradenschnitt und Parallelversatz)
         ist GEBAUT — damit stehen beide Fundamente, an denen W-03s fuenf Werkzeuge haengen. Diese
         Ablesung beschreibt, was HEUTE da ist, und benennt die fuenf Luecken mit ihrem jetzt
         vorhandenen Unterbau."
grundlage: "app/rahmen/EigenschaftenPanel.tsx (563 Z., 2 Exporte) :108/:120/:324/:330-331/:336 ·
            app/tools/toolRegistry.ts (0 Eintraege fuer die fuenf) · app/tools/werkzeugVertrag.ts
            (je 1) · app/tools/werkzeugPaket.ts (je 1) · app/tools/werkzeugLandkarte.ts:108 (teilen; war :76-80)
            (je 'fehlt') · geometry/geradenGeometrie.ts:84 geradenSchnitt, :174 parallelVersatz   [war :157 — A-34 fuegte 17 Kommentarzeilen davor ein]
            (A-32) · store/hausplanerStore.ts:65/:145/:147 executeCommands (A-31) · REGISTER.md:44"
```

## 1 — Der tragende Punkt: „Wand bearbeiten" gibt es zweimal, und nur eine Hälfte fehlt

*Mein eigener Fahrplan-Eintrag sagte „W-03: 0 Treffer für ein Werkzeug" und ordnete die Zeile als Bau
ein. **Gemessen ist die Hälfte gebaut** — sie heißt nur nicht „Werkzeug":*

```text
(1) BEARBEITUNG UEBER EIGENSCHAFTEN — GEBAUT und erreichbar
    app/rahmen/EigenschaftenPanel.tsx (563 Z.)
      :108  aktualisiereWand(changes: Partial<WallNode>)  der generische Weg
      :324  Material         select auf construction.materialId
      :330  Wandstaerke      select, mit WANDSTAERKEN-Liste und Freiwert-Fall
      :336  Hoehe            number-Eingabe, min 100
      :120  Laenge exakt     MOVE_NODE, das Wandende entlang der Achse
    -> Wer eine Wand aendern will, kann es: Material, Staerke, Hoehe, Laenge.

(2) GEOMETRISCHE OPERATIONEN — NICHT gebaut, und zwar alle fuenf gleich weit
    je Werkzeug gemessen, vier Schichten:
                  Registry   Vertrag   Paket   Landkarte
      trimmen        0          1        1      fehlt
      verlaengern    0          1        1      fehlt
      versatz        0          1        1      fehlt
      teilen         0          1        1      fehlt
      verbinden      0          1        1      fehlt
    -> Vertrag und Paket-Eintrag stehen fuer alle fuenf, Registry und
       Modellbefehl fehlen fuer alle fuenf. Kein Werkzeug ist halb gebaut.
```

> **Ein Blatt, das nur „nicht gebaut" sagt, verschweigt die erreichbare Hälfte.** *Und wer den
> Fahrplan-Eintrag liest, plant einen Bau für etwas, das der Benutzer heute schon tun kann — nur eben
> über ein Panel statt über ein Werkzeug. **Das ist der vierte Fall an einem Tag, in dem mein eigener
> Eintrag richtig in der Klasse und zu klein im Gegenstand war** (nach W-16, W-10, W-14).*

## 2 — Die fünf Lücken haben seit heute ihr Fundament, und das gehört ins Blatt

```text
DIE LANDKARTE BEGRUENDET DIE FUENF fehlt-MARKEN, vollstaendig gelesen:
  :77 trimmen      'Braucht Schnittpunktrechnung zweier Waende und ein Kuerzen
                    auf den Schnittpunkt — UPDATE_NODE koennte das Ergebnis
                    setzen, aber der Befehl, der es rechnet, fehlt.'
  :79 verlaengern  'Wie trimmen, andere Richtung.'
  :80 versatz      'Parallelversatz erzeugt eine NEUE Wand im Abstand d …'
  :108 teilen      'einen Knoten aendern UND einen anlegen, in EINEM
                    umkehrbaren Schritt.'
  :78 verbinden    'einen aendern, einen entfernen, in EINEM Schritt.'

SEIT HEUTE STEHT DER UNTERBAU FUER BEIDE GRUPPEN:
  geometry/geradenGeometrie.ts:84   geradenSchnitt(a,b,c,d): Punkt | null
                              :174  parallelVersatz(...)   [war :157]
                                    -> A-32, GEBAUT (Abnahme steht aus)
  store/hausplanerStore.ts:65/:147  executeCommands(commands[])
                                    -> A-31, ABGENOMMEN. Und sauber gebaut:
                                       :145 executeCommand ist jetzt
                                       executeCommands([command]) — EIN Weg,
                                       keine zweite Wahrheit.
```

> ***Was das für die Reihenfolge heißt:*** *`trimmen`, `verlaengern` und `versatz` brauchen die
> **Geometrie** (A-32); `teilen` und `verbinden` brauchen die **Klammer** (A-31). **Beide Fundamente
> stehen, aber es sind zwei verschiedene** — ein Auftrag „W-03 bauen" würde sie in einen Zug legen.
> **Deshalb ist dieses Blatt eine Ablesung und kein Bau:** die fünf Werkzeuge sind danach je ein
> eigener, kleiner Vorgang, wie der Fahrplan es nach der A-31-Messung führt.*

## 3 — Scope

```text
W-03/1 IST  die Ablesung des Gebauten:
            1-ZWECK/2-FUNKTION  was „Wand bearbeiten" heute leistet — Material,
                                Staerke, Hoehe, Laenge ueber das
                                Eigenschaften-Panel
            5-CODE              EigenschaftenPanel.tsx mit Zeilenzahl und den
                                fuenf Stellen, plus der generische Weg :108
            3-FORMELN           AM CODE erheben. Die Registerzeile nennt F-003,
                                F-004 und F-030 — alle drei sind zu PRUEFEN:
                                F-004 ist seit heute gebaut
                                (geradenGeometrie.ts:84), aber NICHT von W-03
                                aufgerufen; F-003 und F-030 sind am Bau-Stand zu
                                messen. Was nicht belegbar ist, wird als LUECKE
                                gemeldet und keine Nummer erfunden.
            7-GRENZEN           die FUENF geometrischen Operationen fehlen, je
                                mit ihrer Landkarten-Begruendung UND dem
                                Fundament, an dem sie haengen (A-32 bzw. A-31).

W-03/1 IST NICHT
            der BAU eines der fuenf Werkzeuge. Je ein eigener Vorgang, und sie
            haengen an ZWEI verschiedenen Fundamenten — das ist der Grund fuer
            die Trennung.
            eine Aenderung an EigenschaftenPanel.tsx oder an Produktivcode.
            NULL.
            eine Aussage darueber, ob die Panel-Bearbeitung ausreicht oder ob
            die fuenf Werkzeuge gebraucht werden. Das ist eine Produktfrage und
            gehoert Yama; hier wird nur festgehalten, was da ist.
            W-02 (Wand zeichnen) und W-13 (Auswahl) -> eigene Blaetter, nur
            abgrenzen.
```

## 4 — Abnahmekriterien

```text
W-03-1-1 (P1, TRAGEND) Das Blatt fuehrt BEIDE Haelften: die gebaute Bearbeitung
         ueber das Panel MIT Fundstelle je Feld (Material :324, Staerke :330,
         Hoehe :336, Laenge :120, generischer Weg :108) UND die fuenf fehlenden
         Operationen. Ein Blatt, das nur „nicht gebaut" sagt, laesst die naechste
         Rolle etwas bauen, das der Benutzer heute schon tun kann.
W-03-1-2 (P1) Die fuenf Operationen stehen mit der Vier-Schichten-Messung, nicht
         als pauschales „fehlt": je Registry, Vertrag, Paket, Landkarte. Am
         Bau-Stand erheben (E1) — meine Zahlen sind vom 13.08.
W-03-1-3 (P1) Je Operation steht das FUNDAMENT, an dem sie haengt: trimmen,
         verlaengern und versatz an geometry/geradenGeometrie.ts (A-32); teilen
         und verbinden an store/hausplanerStore.ts executeCommands (A-31). Und
         ausdruecklich der Satz, dass es ZWEI verschiedene sind — sonst wird
         W-03 als EIN Bau geschnitten und bleibt am falschen Ende stehen.
W-03-1-4 Die FORMELN sind am Code erhoben, nicht aus der Registerzeile
         uebernommen. F-004 ist gebaut, aber von W-03 NICHT aufgerufen — dieser
         Unterschied gehoert benannt, sonst liest die naechste Rolle „F-004 ✓"
         als „W-03 benutzt sie".
W-03-1-5 Kein Produktivcode. Gegenprobe: resources/planner/** kommt im
         Bau-Commit null Mal vor.
W-03-1-6 Der Zeitpunkt ist im Blatt begruendet: A-31 ist ABGENOMMEN, A-32
         GEBAUT und noch nicht abgenommen. Wenn A-32 bis zum Bau dieses Blattes
         zurueckgewiesen wuerde, ist die Aussage „das Fundament steht" zu
         berichtigen — am Bau-Stand pruefen, nicht aus diesem Blatt uebernehmen.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **Muster gegen Fangprobe** (Prüfung 7, 5. Schritt).

```yaml
warum_jetzt_und_nicht_frueher: "W-03 war die letzte der zehn B-Zeilen ohne Blatt, und ich habe bewusst
        gewartet: solange die Fundamente fehlten, waere jede Aussage ueber die fuenf Operationen eine
        Aufzaehlung von Luecken gewesen. Seit A-31 (abgenommen) und A-32 (gebaut) steht der Unterbau,
        und das Blatt kann sagen, WORAN die fuenf haengen statt nur DASS sie fehlen."
was_die_messung_gegen_meinen_eigenen_eintrag_geaendert_hat: "Mein Fahrplan-Eintrag sagte '0 Treffer fuer
        ein Werkzeug' und ordnete W-03 als Bau ein. Gemessen ist die Bearbeitung ueber das
        Eigenschaften-Panel GEBAUT — Material, Staerke, Hoehe, Laenge. Das ist der VIERTE Fall an
        einem Tag, in dem mein Eintrag richtig in der Klasse und zu klein im Gegenstand war, nach
        W-16/1 (Serverhaelfte unter Energie), W-10/1 (Test 242 Z. gegen Modul 35 Z.) und W-14/1 (drei
        Bezugsrahmen). Das Muster ist damit an vier von vier gemessenen Zeilen bestaetigt."
was_ich_nicht_gemessen_habe: "Was die 563 Zeilen des Eigenschaften-Panels sonst noch tun — ich habe die
        fuenf Wand-Stellen und den generischen Weg geoeffnet, nicht das ganze Panel. Und ob die
        Panel-Bearbeitung fachlich ausreicht: das ist eine Produktfrage, ich halte nur fest, was da
        ist."
W_03_1_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT. §3 ist beim Schnitt 0/0 — A-32 ist
        inzwischen CODE_FERTIG und beim Evaluator."
```

---

## Votum des Evaluators (§11) — Runde 1

**ABGENOMMEN.** *Sechs von sechs Kriterien tragen, jedes selbst nachgemessen. Zwei Zusagen sind
stärker als das Kriterium verlangt — und eine davon berichtigt den Auftrag.*

### Messtisch — alle sechs Kriterien, jede Zahl selbst erhoben

| Kriterium | Ergebnis | Wie ich es gemessen habe |
|---|---|---|
| **W-03-1-1** (P1, TRAGEND) beide Hälften mit Fundstelle je Feld | **grün** | **Alle fünf Zeilen selbst geöffnet** in `app/rahmen/EigenschaftenPanel.tsx` (563 Z, selbst gezählt): `:108` `aktualisiereWand(changes)`, `:120` `executeCommand({type:'MOVE_NODE'…})`, `:324` `select … construction.materialId`, `:330` `select … WANDSTAERKEN`, `:336` `input type="number" min={100}`. **Jede trägt, was das Blatt ihr zuschreibt.** Die fünf fehlenden Operationen stehen daneben (`1-ZWECK:15-21` gegen `:36-46`) |
| **W-03-1-2** (P1) Vier-Schichten-Messung statt pauschalem „fehlt" | **grün** | **Selbst gefahren, je Operation vier Muster:** `id:'<op>'` in `toolRegistry.ts` → **0**, `werkzeugId:'<op>'` in `werkzeugVertrag.ts` → **1**, `id:'<op>'` in `werkzeugPaket.ts` → **1**, `werkzeugId:'<op>'` in `werkzeugLandkarte.ts` → **1**. **Für alle fünf Operationen identisch** — die Tabelle in `1-ZWECK:36` stimmt Zelle für Zelle |
| **W-03-1-3** (P1) je Operation das Fundament, und es sind ZWEI | **grün** | `geradenGeometrie.ts` selbst geöffnet: **196 Z, 4 Exporte** (`geradenSchnitt` :84, `Versatzseite` :124, `VersetzteAchse` :127, `parallelVersatz` :174). `executeCommands` im Store an `:65`/`:145`/`:147`. Die Zuordnung `trimmen·verlaengern·versatz → A-32` und `teilen·verbinden → A-31` steht in `7-GRENZEN:27-28`, mit dem verlangten Satz: *„A-32 löst eine RECHENfrage, A-31 eine AUSFÜHRUNGSfrage"* |
| **W-03-1-4** Formeln am Code, F-004 gebaut aber nicht aufgerufen | **grün** | **Gegenprobe selbst gefahren:** `geradenGeometrie` in `app/rahmen/` → **0 Treffer**. Die 17 `Math.`-Aufrufe des Panels sind `Math.round` (14), `Math.hypot` (2), `Math.min` (1) — **kein Geradenschnitt**. Das Blatt sagt es an zwei Stellen (`3-FORMELN:23`, `7-GRENZEN:44`) |
| **W-03-1-5** kein Produktivcode | **grün** | `resources/planner` im Bau-Commit `c9b32ad3`: **0 Treffer**; 0 Dateien außerhalb `docs/` |
| **W-03-1-6** Zeitpunkt begründet, A-31/A-32 am Bau-Stand geprüft | **grün** | **selbst am Bau-Stand gelesen:** beide stehen auf `BETRIEBSBESTAETIGT`, `ballbesitz: —`. Das Blatt nennt genau das und **schreibt dazu, dass der Auftrag noch etwas anderes sagte** (`7-GRENZEN:35-36`, `5-CODE:53`) |
| **Wächter** Insel-Suite | **grün** | selbst gefahren: `tests 1750 · pass 1750 · fail 0 · skipped 0` |
| **Wächter** `tsc:hausplaner` | **grün** | keine Ausgabe |
| **sieben Blätter** *(nicht eigenes Kriterium, aber Hausform)* | **grün** | `74/75/53/64/60/72/68` Z selbst gezählt, **sieben verschiedene md5**; Kollisionsprobe gegen alle Werkbank-Blätter: sieben Dubletten im Bestand, **keine betrifft W-03** |
| **Registerzeile** | **grün** | `LEER → BESCHRIEBEN`, F-Zuordnung berichtigt: `**F-001** ✓ (:117/:339)`, `~~F-003~~ ~~F-004~~ ~~F-030~~ ⓝ`. **Beide F-001-Stellen geöffnet** — `:117` `Math.hypot(dx,dy)`, `:339` `Math.round(Math.hypot(...))` |
| **Browser** | **nicht gefahren** | *der Bau-Commit fasst ausschließlich `docs/` an* |
| **§15 Datenbank** | **nicht berührt** | *kein schreibender Lauf, keine Verbindung* |

### Was stärker ist als verlangt

**1. Die Zusage „drei von vier Schichten führen sie bereits" ist keine Behauptung, sondern trägt
eine Zahl, die weit über W-03 hinausweist — und sie stimmt:**

```text
selbst gezaehlt, je zwei Muster:
  toolRegistry.ts      id: '        12
  werkzeugVertrag.ts   { werkzeugId: '   111
  werkzeugPaket.ts     id: '       101
```

> ***Zwölf von 111 vertraglich beschriebenen Werkzeugen sind registriert.*** *Das Blatt nennt die
> Registry deshalb „das Nadelöhr des ganzen Hauses" — **und diese Aussage ist der eigentliche Wert
> der Ablesung**: sie sagt der nächsten Rolle, dass W-03 kein Einzelfall ist, sondern ein Fall.*

**2. Der Bau hat den Auftrag berichtigt, nicht abgeschrieben.** *Der Auftrag sagt in `W-03-1-6`
noch: „A-31 ist ABGENOMMEN, A-32 GEBAUT und noch nicht abgenommen." **Am Bau-Stand stehen beide auf
`BETRIEBSBESTAETIGT`** — selbst nachgelesen. Das Blatt schreibt den gemessenen Stand hin und hält
ausdrücklich fest, dass der Auftrag etwas anderes sagte.*

> **Das Kriterium hatte den Fall vorgesehen** *(„am Bau-Stand prüfen, nicht aus diesem Blatt
> übernehmen")* — **und der Bau hat ihn genutzt, statt die bequeme Fassung zu übernehmen.** *Das ist
> derselbe Handgriff, der bei W-18/1 die überholte `geradenSchnitt`-Prämisse aufgedeckt hat.*

### Weitergabe

**Ball an den Release-Prüfer** (§11, ABGENOMMEN).
