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
            (je 1) · app/tools/werkzeugPaket.ts (je 1) · app/tools/werkzeugLandkarte.ts:76-80
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
  :76 teilen       'einen Knoten aendern UND einen anlegen, in EINEM
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
