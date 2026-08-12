# A-18 — `wandaufbau` sagt seine Grenze selbst. Die Achse-2-Frage war falsch gestellt, und die Datenstruktur hat sie längst entschieden

```yaml
auftrag: "A-18"
titel: "Der U-Wert traegt seinen Vorbehalt als Pflichtfeld — keine Feuchteschutz-Aussage, weil sie mathematisch nicht durchfuehrbar ist"
art: "BAU — ein Feld, ein Satz. Nach dem Muster A-14/A-17."
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: ea9522bc
prioritaet: P1
anlass: "Yamas Auftrag 12.08. ('bitte das loesen, das ist dein Auftrag konzeptionell') auf die
         vorgelegte Analyse zur letzten offenen Achse-2-Zeile (wandaufbau)."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "A-15 Achse 2 (letzte offene Zeile) · A-14 als Praezedenzfall UND als Bauteil · A-17 als Muster"
```

## 1 · Die Analyse trägt — ich habe sie nachgemessen, nicht übernommen

**Alle fünf Kernaussagen halten. Jede einzeln geprüft:**

```text
(1) Die Schicht hat GENAU DREI Felder — wandaufbau.ts:9-15
      export interface Schicht { name?: string; dicke: number; lambda: number; }
    Fuer Glaser (DIN 4108-3) fehlen zwingend: mu bzw. sd je Schicht UND Klima innen/aussen.

(2) Zehn Feuchte-Begriffe in der Datei:  0 / 0 / 0 / 0 / 0 / 0 / 0 / 0 / 0 / 0
      taupunkt · tauwasser · kondensat · diffusion · sd-wert · glaser · 4108 · schimmel
      · feuchte · mu-wert

(3) Der Dateikopf nennt seine Norm selbst — wandaufbau.ts:4
      "Reine Bauphysik: Waermedurchgangskoeffizient U aus geschichtetem Aufbau (DIN EN ISO 6946).
       U = 1 / (Rsi + Sigma d/lambda + Rse)."
    DIN EN ISO 6946 ist U-Wert. DIN 4108-3 ist Feuchteschutz. Die Datei nennt die erste.

(4) berechneUWert hat GENAU EINEN Aufrufer ausserhalb der Tests — faehigkeiten.ts:81
      { id: 'engine-uwert', …, zustand: 'in_entwicklung', engineExport: 'berechneUWert' }
    Eine Faehigkeitsliste, KEIN Panel. Es gibt heute kein Gesamturteil zu streichen.

(5) Die PHP-Heizlast ruft die Engine NICHT auf
      grep -rl 'wandaufbau|berechneUWert' app/   ->  0 Dateien
```

> **Der entscheidende Satz der Analyse, und er stimmt:** *die Engine rechnet den Taupunkt nicht —
> **sie kann ihn nicht rechnen.*** *Das ist kein „noch nicht implementiert", sondern mathematisch
> nicht durchführbar: zwei der drei nötigen Größen existieren im Datentyp nicht. **Und das kann
> jeder nachzählen; dafür braucht niemand Bauphysik.***

## 2 · Eine Präzisierung — „null Treffer im ganzen Haus" ist zu stark

*Die Analyse sagt: „je null Treffer in der Datei **und im ganzen Haus**". In der Datei stimmt es
zehnmal. Im Haus nicht:*

```text
grep -rniE 'feuchte' resources/planner/  ->  ZWEI Fundstellen, beide gelesen (B5):
  app/dashboard/fachFlaechen.ts:149    { label: 'Feuchtelast', einheit: 'g/h' }
  geometry/dachformVorlagen.ts:105     holzfeuchteProzent: string;   (+ vier Werte, z.B. ':1525')
```

**Warum das die Schlussfolgerung nicht umstößt, sondern schärft:** *beide sind **andere**
Feuchtegrößen — eine **Feuchtelast** in g/h (Raumluft, Lüftung) und die **Holzfeuchte** in Prozent
(Abbund, ≤ 20 %). **Keine davon ist eine Tauwasserrechnung am Bauteil.** Die Aussage „`wandaufbau`
macht keine Feuchteschutz-Aussage" hält vollständig; die Begründung muss aber „in dieser Engine"
sagen statt „im ganzen Haus". **Und der Nebenbefund ist interessant: Raumklima-Größen kommen im Haus
durchaus vor — nur nicht dort, wo Glaser sie bräuchte.***

## 3 · DECISION — der Vorbehalt kommt ins Ergebnis, nicht in die Klassifikationstabelle

**Die Achse-2-Frage lautete „Bauschaden oder Fehlauslegung?" — und sie war falsch gestellt.** *Die
Engine behauptet keine Feuchteschutz-Aussage. Sie behauptet einen U-Wert und rechnet ihn vollständig
richtig (`U = 1/(Rsi + Σd/λ + Rse)`, 10 Testzusagen).*

```text
ENTSCHIEDEN   UErgebnis bekommt ein PFLICHTFELD 'vorbehalt' — nicht optional, nicht Anzeige.
              Wortlaut aus dem Dateikopf abgeleitet, nicht erfunden.
NICHT         Kein Glaser-Rechenweg. Kein mu/sd im Datentyp. Keine Klimafelder.
              Das waere ein neues Fachmodul und braucht Yamas Operanden — hier NICHT gebaut.
NICHT         Keine Plakette streichen: es gibt keine. Anders als bei A-14 und A-17 hat diese
              Engine kein Panel und damit kein Gesamturteil (Messung 4).
FOLGE FUER    Die Achse-2-Zeile loest sich auf: was die Engine WIRKLICH behauptet, wirkt auf
A-15          die Heizlast -> FEHLAUSLEGUNG. Der Bauschaden-Weg ist ausgeschlossen, sobald
              sie ihre Grenze selbst sagt. A-15 ist IN_ARBEIT beim Generator — ich fasse es
              NICHT an, sondern benenne die Folge hier.
```

**Der Wortlaut, vorgeschlagen in der Analyse und von mir gegen den Code geprüft:**

```text
U-Wert nach DIN EN ISO 6946. Keine Feuchteschutz-Aussage — Tauwasser und Schimmelrisiko
nach DIN 4108-3 sind nicht geprueft; dafuer fehlen die Diffusionswiderstaende und das
Raumklima. Ein guter U-Wert bedeutet nicht, dass die Konstruktion feuchtesicher ist.
```

> **Warum dieser Wortlaut KEIN Fachurteil von Yama braucht** — und das ist der Unterschied zu
> N-003: *bei N-003 musste Yama den **Geltungsbereich** festlegen, weil die Frage „wofür darf man
> eine Vorbemessung benutzen" eine Fachentscheidung ist. Hier ist jeder Satzteil eine **Ablesung**:
> die Norm steht im Dateikopf, die fehlenden Größen stehen im Datentyp, und der Schlusssatz ist die
> logische Folge daraus. **Yama entscheidet nicht, was gilt — er bestätigt, dass es sichtbar wird.***

## 4 · Der zweite Befund — der Dateikopf verspricht etwas, das nicht existiert

```text
wandaufbau.ts:2   "Konfigurator „Wandaufbau" §11, autark; SPEIST HEIZLAST & DACH"
gemessen          Aufrufe aus app/ (PHP-Heizlast):     0 Dateien
                  Aufrufe aus einem Panel:              0
                  einziger Aufrufer:                    faehigkeiten.ts:81, zustand 'in_entwicklung'
```

> **„Speist Heizlast & Dach" ist eine Zusage im Dateikopf, die kein Aufruf einlöst.** *Dieselbe
> Klasse wie die fehlende Azimut-Brücke und wie W-15s „Vertrag ohne Implementierung": **ein Text
> beschreibt eine Verbindung, die es nicht gibt.** Das ist **nicht** Teil dieses Auftrags — er wäre
> sonst ein Brückenbau. **Als eigener Posten geführt**, damit er nicht als erledigt gilt.*

**Und es senkt die Dringlichkeit, ohne die Systematik zu ändern:** *heute erreicht der U-Wert
niemanden — kein Panel, keine Heizlast, keinen Bildschirm. **Der Vorbehalt wird gebaut, bevor die
Brücke steht, nicht danach.** Genau das ist der Unterschied zu A-16, wo ich die Reihenfolge
umgekehrt vorgefunden habe: dort liegt eine fertige Oberfläche und wartet auf drei Zeilen Route.*

## 5 · Abnahmekriterien

```text
A-18-1  UErgebnis (wandaufbau.ts:30-37) traegt ein Feld 'vorbehalt: string' — PFLICHT, nicht
        optional. Gegenprobe: tsc bricht ab, wenn eine Rueckgabe es weglaesst; das ist der
        Beweis, dass es nicht weglassbar ist. Ein optionales Feld waere die stille Ersetzung.

A-18-2  Der Wortlaut steht als benannte Konstante (Muster: N003_VORBEHALT in
        sparrenBerechnung.ts:100), nicht als Zeichenkette im Rueckgabeblock — damit er EINMAL
        im Haus steht und zitierbar ist. Gegenprobe: die Konstante kommt genau einmal vor.

A-18-3  DER WORTLAUT IST ZEICHENGENAU der aus Abschnitt 3. Keine Umformulierung, keine
        Kuerzung, kein "ggf." — Gegenprobe des Evaluators: Zeichenvergleich gegen dieses Blatt.

A-18-4  KEINE RECHENAENDERUNG. uWert, rGesamt, rBauteil, schichtR, gesamtdicke und die
        pruefungen bleiben zeichengleich. Nachweis: git diff zeigt 0 geaenderte Rechenzeilen,
        0 geaenderte Vergleichsoperatoren, 0 geaenderte Konstanten in UEBERGANG und ZIEL_U.

A-18-5  KEIN GLASER, KEIN mu, KEIN sd, KEIN KLIMA. Gegenprobe: die zehn Feuchte-Begriffe aus
        Abschnitt 1 bleiben in der Datei bei 0 Treffern — mit der EINEN Ausnahme des
        Vorbehaltstextes selbst, der 'Tauwasser', 'Schimmelrisiko', '4108' und
        'Diffusionswiderstaende' NENNT. Der Unterschied ist der Punkt: die Engine SPRICHT
        ueber die Groessen, sie RECHNET sie nicht. Der Evaluator prueft, dass kein
        Rechenausdruck hinzugekommen ist.

A-18-6  DIE ZEHN VORHANDENEN TESTZUSAGEN bleiben gruen, und EINE kommt hinzu: dass der
        Vorbehalt im Ergebnis steht und zeichengenau stimmt. Belegform: Befehl + Ausgabe.

A-18-7  DIE FAEHIGKEITSLISTE wird nachgezogen: faehigkeiten.ts:81 fuehrt ausgang 'UErgebnis'.
        Wenn UErgebnis ein Pflichtfeld gewinnt, ist der Eintrag ohne Aenderung weiterhin
        richtig — das ist zu PRUEFEN und im Bericht zu sagen, nicht anzunehmen.

A-18-8  DER ZWEITE BEFUND wird NICHT mitbehandelt, sondern benannt: "speist Heizlast & Dach"
        (Dateikopf:2) gegen 0 Aufrufe aus app/. Kein Brueckenbau in diesem Auftrag.
```

## 6 · Auswirkungen (§5) und `must_preserve`

```text
BETROFFEN     resources/planner/hausplaner/geometry/wandaufbau.ts   (Feld + Konstante)
              resources/planner/hausplaner/__tests__/wandaufbau.test.ts  (eine Zusage mehr)
NICHT         app/**  (0 Aufrufe — die PHP-Seite ist unberuehrt)
BETROFFEN     kein Panel, kein Renderer, keine Migration, kein Datenpfad, kein Geldwert

must_preserve
  (1) resources/** ausser den zwei genannten Dateien byte-identisch
  (2) app/** byte-identisch — Nachweis: git diff --stat nennt app/ nicht
  (3) die zehn vorhandenen Zusagen in wandaufbau.test.ts unveraendert; NUR Einfuegung
  (4) UEBERGANG (Rsi/Rse je Bauteil) und ZIEL_U zeichengleich — das sind Normwerte,
      und sie sind NICHT Gegenstand dieses Auftrags
```

## 7 · Rückweg & Entdeckung — als eigene Zeile

```text
RUECKWEG      reiner Revert. Ein Feld und eine Konstante; kein Datenpfad, keine Migration,
              kein Schema, kein Geldwert. Rueckwaerts-Patch via git apply --check -R Exit 0,
              OHNE den Arbeitsbaum anzufassen.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier NICHT behauptet — die Lehre
              aus meiner falschen Zusage in A-16/B7. Der lokale Zweig liegt heute 15 Commits
              hinter fork/auto/hausplaner-integration (gemessen 12.08.); der Generator belegt
              den Rueckfallpunkt am Bautag mit Befehl.
ENTDECKUNG    das Signal ist der Vorbehalt selbst: fehlt er in EINER Rueckgabe, bricht tsc —
              deshalb ist A-18-1 als PFLICHTfeld formuliert und nicht als optionales.
              Zweites Signal: weicht der Wortlaut ab, faellt der Zeichenvergleich aus A-18-3.
```

## 8 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Index (gestaged)   LEER
Arbeitsbaum        docs/BERICHT-A-15-fachaussage-oder-hinweis.md (M), docs/STATUS.md (M),
                   docs/BERICHT-A-15-klassifikation.md (D, halber git mv)  -> alle FREMD
§3-Stand           1 IN_ARBEIT: A-15 (Generator)
SCOPE-UEBERSCHNEIDUNG  A-15 fasst NUR docs/ an (der Generator belegt "resources/ und app/
                   weiterhin 0 geaendert" in a2385d35 und 82d7c31e). A-18 fasst zwei Dateien
                   unter resources/planner/ an. KEINE Ueberschneidung.
                   ABER: A-18 ist die FOLGE aus A-15s letzter offener Zeile. Es darf erst
                   BAUEN, wenn A-15 abgeschlossen ist — sonst baut es auf einem Vorschlag.
A-18 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
ballbesitz: "plan-pruefer (DoR)"
abhaengigkeit: "A-15 muss die Klassifikation abschliessen. Die wandaufbau-Zeile war die LETZTE
                offene — mit A-18 loest sie sich auf, ohne dass Yama ein Fachurteil faellen muss."
was_yama_NICHT_entscheiden_muss: "ob die Engine den Taupunkt abdeckt. Sie kann ihn nicht rechnen,
                das steht im Datentyp und nicht in einer Meinung."
zweiter_posten: "'speist Heizlast & Dach' (Dateikopf:2) gegen 0 Aufrufe aus app/ — eigener
                Vorgang, hier ausdruecklich NICHT erledigt."
praezisierung: "'feuchte' hat im Haus ZWEI Fundstellen (Feuchtelast g/h, holzfeuchteProzent),
                beide andere Groessen. Die Aussage gilt fuer DIESE Engine, nicht fuer das Haus."
```

## §11 — Votum A-18 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "A-18"
votum: ABGENOMMEN
fehlerklasse: KEINE
abnahme_commit: "492a6a71"
elter: "b7ab49c5"
pruefstand: "worktree --detach auf 492a6a71 und b7ab49c5, node_modules UND vendor per cp -al
     — nach dem vendor-Fehler bei B5 diesmal beides vor der ersten Messung."
reihenfolge: "Die KERNBEHAUPTUNG des Auftrags habe ich am Datentyp selbst geprueft, bevor ich
     irgendetwas anderes gelesen habe: Schicht traegt name?, dicke, lambda — drei Felder, kein
     mu, kein s_d, kein Klima. Die Praemisse haelt. Den Bericht habe ich zuletzt gelesen."

messtisch_alle_acht:
  A-18-1: GRUEN — mit der vom Kriterium verlangten Gegenprobe
    beleg: "wandaufbau.ts:49 'vorbehalt: string;' — kein Fragezeichen, also Pflicht."
    gegenprobe_gefahren: "Mutation: 'vorbehalt: UWERT_VORBEHALT,' aus dem Rueckgabeblock
            entfernt (Anker genau 1x). tsc bricht ab:
              wandaufbau.ts(87,3): error TS2741: Property 'vorbehalt' is missing in type ...
            Zurueckgestellt, md5 identisch. Das ist der Beweis, den das Kriterium verlangt —
            ein optionales Feld haette diesen Fehler NICHT erzeugt."
  A-18-2: GRUEN — und seine Frage entscheide ich, gemessen statt geurteilt
    beleg: "export const UWERT_VORBEHALT genau 1x deklariert (wandaufbau.ts:57), 1x verwendet (:94)."
    seine_vorlage: "Er meldet, dass der WORTLAUT zweimal vorkommt — Konstante und ausgeschrieben
            in der Zusage — und legt mir die Entscheidung vor, statt die passende Stelle
            auszuwaehlen. Das ist die richtige Form."
    meine_entscheidung: "DIE ZWEITE STELLE BLEIBT. Begruendung nicht aus dem Bauchgefuehl,
            sondern gemessen: ich habe den Wortlaut in der KONSTANTE still umformuliert
            ('bedeutet' -> 'heisst', Anker genau 1x) und die Zusagen gefahren:
              ✖ A-18: jede Rueckgabe traegt den Vorbehalt, zeichengenau
              pass 6 / fail 1
            Zurueckgestellt, md5 identisch. Der ausgeschriebene Vergleich IST der Waechter
            gegen die stille Umformulierung, die A-18-3 verbietet. Ein Test, der nur
            r.vorbehalt === UWERT_VORBEHALT prueft, waere tautologisch und haette geschwiegen."
    zur_zahl_des_kriteriums: "'Die Konstante kommt genau einmal vor' ist erfuellt — die
            Konstante. Der WORTLAUT kommt zweimal vor, und die zweite Stelle ist ein Test,
            keine zweite Wahrheit im Produktivcode. Kein Befund."
  A-18-3: GRUEN
    beleg: "Maschineller Zeichenvergleich Blatt (Zeilen 91-93) gegen die Konstante im Commit:
            beide 258 Zeichen, IDENTISCH: True. Ich habe den Vergleich selbst gefahren, nicht
            seinen uebernommen — und beim ersten Versuch den falschen Codeblock des Blattes
            erwischt (den ENTSCHIEDEN/NICHT-Block); das war mein Raster, nicht sein Bau."
  A-18-4: GRUEN
    beleg: "git diff --numstat wandaufbau.ts: 24 Einfuegungen, 0 LOESCHUNGEN. Es gibt keine
            geaenderte Rechenzeile, weil es keine geaenderte Zeile gibt. Alle neuen
            Nicht-Kommentarzeilen sind: das Feld, die Konstante, die drei Wortlautzeilen und
            die Zuweisung im Rueckgabeblock — kein Ausdruck, kein Operator, keine Konstante
            in UEBERGANG oder ZIEL_U."
  A-18-5: GRUEN
    beleg: "Zehn Feuchte-Begriffe, Elter gegen Bau: alle waren 0. Im Bau tragen Tauwasser,
            Glaser, Schimmel, 4108, Diffusion, s_d und Feuchte Treffer — und ich habe die
            TREFFERZEILEN GELESEN, nicht gezaehlt: alle liegen in :41-42 (Kommentar) und
            :58-60 (der Vorbehaltstext). Kein einziger im Rechenweg.
            mu, Kondensat, Dampfbremse bleiben bei 0."
    der_unterschied_haelt: "Die Engine SPRICHT ueber die Groessen, sie RECHNET sie nicht.
            Genau die Grenze, die -5 zieht."
  A-18-6: GRUEN
    was_ich_zuerst_falsch_mass: "Ich zaehlte test()-Bloecke: Elter 6, Bau 7 — und haette
            'die zehn des Auftrags stimmen nicht' gemeldet. Beide Zaehlweisen selbst gefahren:
              grep -cE '^test\\('   -> Elter 6,  Bau 7
              grep -cE 'assert\\.'  -> Elter 10, Bau 13
            Die ZEHN sind die Assertions. Die Zahl des Auftrags stimmt, meine Ebene war falsch."
    beleg: "Keine Zusage entfernt — die einzige geloeschte Zeile ist die import-Zeile, ersetzt
            durch dieselbe plus UWERT_VORBEHALT. Ein Block kommt hinzu, mit drei Assertions.
            Insel-Suite: 1694/1694/0, vorher 1693 — genau ein Block mehr."
  A-18-7: GRUEN
    beleg: "faehigkeiten.ts:81 selbst geoeffnet: id 'engine-uwert', eingang 'Schicht[]',
            ausgang 'UErgebnis', zustand 'in_entwicklung'. Der Eintrag nennt den TYP, nicht
            seine Felder — ein neues Pflichtfeld macht ihn nicht falsch. Die Datei ist im Bau
            0-mal angefasst, und das ist richtig so."
  A-18-8: GRUEN
    beleg: "Dateikopf:2 sagt 'speist Heizlast & Dach'. Aufrufe aus app/: ich zaehlte zuerst
            EINE Datei und war im Begriff, den Auftrag zu widerlegen. Die Trefferzeile gelesen:
            faehigkeiten.ts:81 — dort steht 'berechneUWert' als ZEICHENKETTE im
            Faehigkeits-Eintrag, nicht als Aufruf. Echte Aufrufe: 0. Die Behauptung haelt,
            der Befund ist benannt und ausdruecklich nicht behandelt."

must_preserve_gemessen:
  suite: "1694/1694/0 (Elter 1693)"
  tsc: "tsc -p tsconfig.hausplaner.json --noEmit -> clean"
  loeschungen_im_produktivcode: "0"

was_diesen_bau_heraushebt:
  - "Er legt eine ENTSCHEIDUNG vor, statt sie selbst zu treffen: der Wortlaut kommt zweimal vor,
     er nennt beide Stellen, begruendet die zweite und sagt ausdruecklich 'das ist seine
     Entscheidung, nicht meine'. Genau so soll ein Bauender mit einer Kriteriumsgrenze umgehen."
  - "Er loest die 6-gegen-10 selbst auf, statt sie zu verschweigen oder den Auftrag zu
     widerlegen — und nennt sie 'B6 in freier Wildbahn: eine Summe, deren Menge nie benannt
     wurde'. Dieselbe Messung habe ich unabhaengig gemacht und komme auf dieselben Zahlen."
  - "Der Rueckweg steht als eigene Zeile, am BAUTAG gemessen — der Punkt, der bei A-14, A-15
     und W-09 dreimal in Folge gefehlt hat."

meine_eigenen_zwei_fallen_in_dieser_abnahme:
  - "test()-Bloecke statt Assertions gezaehlt (6 statt 10) — beinahe ein Fehlbefund gegen den
     Auftrag. Aufgeloest durch beide Zaehlweisen, nicht durch eine."
  - "'berechneUWert in app/: 1 Datei' — beinahe 'der Auftrag ist widerlegt'. Die Trefferzeile
     gelesen: eine Zeichenkette in der Faehigkeitsliste. Das ist B5, angewandt auf mich,
     zum zweiten Mal in zwei Abnahmen."

zusammenfassung: "Acht von acht. Der Kern A-18-1 ist nicht behauptet, sondern bewiesen: ohne das
     Feld bricht tsc mit TS2741 ab. Der Wortlaut ist zeichengenau (258 = 258, maschinell
     verglichen). Die Rechnung ist unberuehrt, weil es 0 Loeschungen gibt. Und die vorgelegte
     Frage habe ich entschieden, indem ich sie gemessen habe: der ausgeschriebene Vergleich
     faengt eine stille Umformulierung — er bleibt."

ballbesitz: release-pruefer
```
