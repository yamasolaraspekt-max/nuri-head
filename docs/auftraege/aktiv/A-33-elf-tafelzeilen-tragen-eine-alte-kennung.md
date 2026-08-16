# A-33 — ELF Tafelzeilen tragen die Kennung verkürzt, und deshalb prüft die Ball-Drift-Barriere sie nicht

> **Berichtigt nach `a304301d`:** *die erste Fassung sagte im Titel, im Scope und in A-33-3 **zehn** und ließ
> `W-21` weg — während die Tabelle in Abschnitt 1 es mitzählte. **Damit war A-33-1 unerfüllbar:** es verlangt,
> dass nach dem Bau genau eine Zeile übrig ist, und bei zehn Berichtigungen wären zwei geblieben. Der Befund
> trifft, und `W-05` und `W-21` sind **strukturgleich** — beide haben `/1` und `/2`, beide führen eine
> Tafelzeile für den Stamm und eine für `/2`. Ein Unterschied, der den Ausschluss von `W-21` gerechtfertigt
> hätte, gab es nicht. **Selbst nachgemessen (Fangprobe bestanden): es sind elf.***

```yaml
auftrag: "A-33"
werkzeug: "—  (Statuswahrheit in docs/STATUS.md)"
art: "BAU — ein SKRIPT, das die verkuerzten Tafelkennungen nachzieht.
      UMGESCHNITTEN am 16.08. durch Yama: Liefergegenstand ist scripts/a33-kennungen-nachziehen.sh,
      NICHT die Bearbeitung von docs/STATUS.md. Der Generator fasst die Statuswahrheit inhaltlich
      gar nicht an — das Skript liegt unter scripts/, AUSGEFUEHRT wird es vom Integrator, und damit
      bleibt der einzige Schreiber der einzige Schreiber. Die Einzelschreiber-Regel gilt hier,
      bevor der Integrator ueberhaupt existiert."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: f9b67b1b
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-33 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-32 sind vergeben. Frei."
anlass: "In A-30 ausdruecklich als eigener Vorgang angekuendigt und aus dem Scope genommen: 'A-30
         macht die Zahl SICHTBAR; behoben wird sie getrennt.' Das ist der getrennte Vorgang."
grundlage: "docs/STATUS.md · scripts/a26-ball-drift.sh:53/:96-97 · §16 (eine Statuswahrheit) ·
            ZEIGER BERICHTIGT 14.08. nach 0b80ca53 — genannt waren :32 und :55-56. Der A-30-Bau
            0aceee01 kam NACH dem Schnitt f9b67b1b und hat die Datei von 102 auf 158 Zeilen
            umgebaut. Heute steht auf :32 der case-Verteiler statt der Kennungs-Muster-Zeile
            (die liegt auf :53), auf :55 der KENNUNGEN-Ausstieg statt der START-Zuweisung
            (die liegt auf :96) und auf :56 eine Leerzeile statt des stillen continue
            (heute :97 als if-Block). Zitiert war ausgerechnet das stille continue — die
            Bauform, die A-30 aufgeloest hat. Vierter belegter Fall dieser Klasse. NICHT
            betroffen ist die Sache des Auftrags: die a26-Aenderung ist im Blatt selbst
            Nicht-Ziel, und A-33-5 vergleicht vorher/nachher, trifft also beide Laeufe gleich.
            A-20-2 · A-30 (die Sichtbarkeit)"
```

## 1 — Der Befund: ELF Zeilen, und die Barriere paart sie nicht

```text
GEMESSEN am Stand f9b67b1b, Muster gegen eine FANGPROBE geprueft (Pruefung 7,
fuenfter Schritt — die Probe deckt auch den M-02-Fall, an dem A-30 fast
gescheitert waere):
  Tafel ohne Datensatz      12   <- TRAGEND, an f9b67b1b reproduziert
  Tafelzeilen mit Kennung   67   <- KONTEXT, NICHT von f9b67b1b (s.u.)
  Datensaetze mit auftrag   66   <- KONTEXT, NICHT von f9b67b1b (s.u.)

  BERICHTIGT 14.08. nach dem Befund des plan-pruefers (1d98843e):
    Die TRAGENDE Zahl haelt — er hat sie an f9b67b1b nachgezaehlt und findet
    dieselben ZWOELF in derselben Reihenfolge; damit traegt auch die Ableitung
    ELF und die Nachbedingung, dass eine Zeile uebrig bleibt.
    Die zwei KONTEXTzahlen tragen einen AELTEREN Stand als den genannten:
      67 gilt zuletzt bei ba7eee61 (13.08. 08:50) — 19 Commits und 1h55 VOR
         f9b67b1b (10:45); an f9b67b1b sind es 70.
      66 gilt bei ca99466b (08:17) und 86f94d98 (08:13); an f9b67b1b und den
         60 Staenden davor NIRGENDS.
    SIE WERDEN NICHT AUF EINEN NEUEN WERT GESETZT, und das ist Absicht: ich
    messe heute 74 Tafelzeilen, er mass 70 — die Zahl altert in Stunden, weil
    jede Rolle in dieselbe Datei schreibt. Ein neuer Wert waere morgen wieder
    falsch. Sie stehen deshalb als STAND mit Commit da, nicht als Messwert.
    WAS DARAUS FOLGT und der eigentliche Grund der Berichtigung: Gegenmessung
    nach dem Bau laeuft gegen die TRAGENDE Zahl. Wer bei -Tafelzeilen 67-
    gegenmisst, sucht einen Fehler, den es nicht gibt.

DIE ZWOELF, je mit dem Kandidaten aus der Datensatz-Seite:
  A-06  Probedaten Arbeits-DB    -> KEIN Datensatz, Zustand ERLEDIGT
  W-01  Raster und Fang          -> W-01/1            eindeutig
  W-02  Wand zeichnen            -> W-02/1            eindeutig
  W-04  Oeffnung Tuer/Fenster    -> W-04/1            eindeutig
  W-05  Raum erkennen            -> W-05/1, W-05/2    zwei Kandidaten
  W-08  Dachflaeche messen       -> W-08/1            eindeutig
  W-09  Treppe                   -> W-09/1            eindeutig
  W-11  Mass und Bemassung       -> W-11/1            eindeutig
  W-13  Auswahl und Griffe       -> W-13/1            eindeutig
  W-15  Material und Farbe       -> W-15/1            eindeutig
  W-21  Sparren und Lattung      -> W-21/1, W-21/2    zwei Kandidaten
  W-22  Gaube                    -> W-22/1            eindeutig
```

> **Die Folge steht in A-30 und ist dort belegt:** *`scripts/a26-ball-drift.sh` sammelt die Kennungen
> aus **beiden** Orten (`:32`), findet für die Tafel-ID `W-01` keinen Block und springt in `:56` **still**
> weiter (`continue`). **Für diese Zeilen läuft die Ball-Drift-Prüfung leer** — und weil `continue`
> schweigt, sieht das niemand.*

## 2 — Warum eine PAARUNGS-HEURISTIK der falsche Weg wäre. Gemessen, nicht vermutet

*Der naheliegende Gedanke ist, die Barriere paaren zu lassen: „Kennung ohne Suffix gehört zu derselben
mit `/1`". **Das ist widerlegt, und zwar an vier Stellen:*

```text
STAEMME MIT MEHREREN DATENSAETZEN — und was die Tafel dort fuehrt:
  W-05   Datensaetze W-05/1, W-05/2      Tafel: W-05  UND  W-05/2
  W-21   Datensaetze W-21/1, W-21/2      Tafel: W-21  UND  W-21/2
  W-27   Datensaetze W-27,   W-27/1      Tafel: W-27  UND  W-27/1
  W-40   Datensaetze W-40,   W-40/1      Tafel: W-40  UND  W-40/1

BEI W-27 UND W-40 IST DIE FORM OHNE SUFFIX EIN EIGENER AUFTRAG. Beide Stellen
geoeffnet:
  W-27    Tafel „Dachkantentypen"
          Datensatz-Titel „Vier Stufen, vier Datenformen, zwei stillgelegte
          Konstanten — Ablesung aus …"
  W-27/1  Tafel „BAU Dachkantentypen in die Insel"
          Datensatz-Titel „BAU: Dachkantentypen in die Insel — der erste
          Auftrag, der Produktivcode …"
  W-40    Tafel „Gueltigkeitsstatus confirmed/outdated"
          Datensatz-Titel „Die zweite Achse, ohne die erst nach Bestaetigung
          nicht pruefbar ist"
  W-40/1  Tafel „Nachbesserung: Ablesung mit EINER Erweiterung"
          Datensatz-Titel „W-40 ist eine Ablesung mit EINER Erweiterung …"

-> Eine Heuristik „ohne Suffix == /1" wuerde bei W-27 und W-40 ZWEI
   VERSCHIEDENE AUFTRAEGE verschmelzen. Und bei W-05 und W-21 haette sie
   zwei Kandidaten und muesste raten.
```

> ***Deshalb wird die DATENSEITE berichtigt und nicht die Barriere.*** *Die Barriere liest die Kennung
> wörtlich, und das ist richtig — eine Kennung, die geraten werden muss, ist keine Kennung. **A-30
> schließt das Sichtbarkeitsloch (`continue` meldet), A-33 räumt die Fälle weg, die es dann melden
> würde.** Die zwei greifen ineinander und sind trotzdem getrennt, weil der eine Code anfasst und der
> andere Daten.*

## 3 — Und es ist keine Lesbarkeits-Entscheidung, sondern eine alte Gewohnheit

```text
DIE NEUEREN ZEILEN SCHREIBEN BEIDES — Kennung MIT Suffix und Klartext:
  | **W-12/1** Ansicht und Kamera (Ablesung) | …
  | **W-10/1** Decke und Boden (Ablesung)    | …
  | **W-14/1** Kopieren/Spiegeln/Drehen (Ablesung) | …
  | **W-05/2** Raum anwaehlen                | …
  | **W-21/2** Auswechslung bekommt ein Zuhause | …

  -> Vollstaendig UND lesbar. Die Verkuerzung bei den elf alten spart also
     nichts, was die neueren nicht auch haetten.
```

> **Das ist der Beleg, dass hier nichts geopfert wird.** *Hätte die Tafel die Kennung aus
> Lesbarkeitsgründen verkürzt, wäre die Berichtigung ein Verlust. **Die neun Zeilen mit Suffix zeigen,
> dass es keinen gibt** — die elf alten tragen eine Konvention, die inzwischen abgelöst ist.*

## 4 — Scope

```text
A-33 IST  die ELF Tafelzeilen auf die Kennung ihres Datensatzes ziehen:
            W-01 -> W-01/1    W-02 -> W-02/1    W-04 -> W-04/1
            W-05 -> W-05/1    W-08 -> W-08/1    W-09 -> W-09/1
            W-11 -> W-11/1    W-13 -> W-13/1    W-15 -> W-15/1
            W-21 -> W-21/1    W-22 -> W-22/1
          Der Klartext hinter der Kennung bleibt WORTGLEICH stehen.
          W-21 WAR IN DER ERSTEN FASSUNG NICHT AUFGEFUEHRT — nachgetragen nach
          a304301d. Es ist strukturgleich mit W-05: zwei Datensaetze (/1 und /2),
          eine Tafelzeile fuer den Stamm und eine fuer /2. Wie bei W-05 gehoert
          der Stamm zum /1-Auftrag; die /2-Zeile traegt ihren Suffix schon.

A-33 IST NICHT
          A-06. Die Zeile hat KEINEN Datensatz und den Zustand ERLEDIGT — es
          gibt nichts zu paaren. Wenn ein Datensatz fehlt, ist das eine andere
          Frage (A-30 macht sie sichtbar) und keine Schreibweise.
          W-27, W-27/1, W-40, W-40/1. Dort sind BEIDE Formen eigene Auftraege
          mit eigenem Datensatz und eigenem Titel — Abschnitt 2 belegt es an
          beiden Stellen. Wer sie zusammenzieht, LOESCHT einen Auftrag.
          W-05/2 und W-21/2. Die tragen ihren Suffix schon.
          eine Aenderung an scripts/a26-ball-drift.sh. Das ist A-30.
          eine Aenderung an einem ZUSTAND oder BALLBESITZ. Nur die Kennung.
          Alle elf sind abgeschlossen (BETRIEBSBESTAETIGT oder ABGENOMMEN);
          ihr Zustand ist nicht Gegenstand.
          eine Aenderung an den BLAETTERN in docs/auftraege/aktiv/. Deren
          Dateinamen (z. B. W-01-fang-beschreiben.md) bleiben, wie sie sind.
```

## 5 — Abnahmekriterien

```text
A-33-1 (P1, TRAGEND) — NEUGEFASST am 16.08.: EINE INVARIANTE STATT EINER ZAHL.

       Nach dem Lauf gibt es KEINE Tafelzeile mehr, deren Kennung VERKUERZT ist,
       waehrend ihr Datensatz die VOLLE Form traegt.
       Der Lauf MELDET, wieviele es waren. Ein ZWEITER Lauf meldet NULL.

       WARUM DIE ZAHL RAUS MUSSTE, und der Beleg gehoert dazu:
       Die alte Fassung nannte feste Zielzahlen — unter dem Muster A-/W- genau
       EINS (A-06), unter allen Grossbuchstaben genau ZWEI (A-06 und P-02).
       BEIDE SIND ABGELAUFEN. A-06 und P-02 haben seit 086b48bd einen Datensatz;
       der Rest waere heute NULL statt eins beziehungsweise zwei.

       DER COMMIT 086b48bd IST MEINER. Ich habe A-06 und P-02 am 15.08. ihre
       fehlenden Datensaetze gegeben — auf einen Befund des Release-Pruefers hin,
       sachlich richtig — und dabei OHNE ES ZU BEMERKEN ein fremdes Kriterium
       ungueltig gemacht. Gefunden hat es der Generator beim Ziehen, nicht ich.

       DIE LEHRE, und sie gilt ueber diesen Auftrag hinaus:
       Eine Zahl im Kriterium misst den Bestand ZUM ZEITPUNKT DES SCHNITTS.
       Jede spaetere Arbeit an demselben Bestand laesst sie ablaufen — und der
       Schneidende erfaehrt es nicht. Eine INVARIANTE laeuft nicht ab: sie sagt,
       was NACH dem Lauf gelten muss, unabhaengig davon, wieviele es vorher waren.
       (Dieselbe Bewegung wie bei A-37-11 und A-38-9, wo die feste Suitezahl 1750
       durch die Bindung an den Bau-Stand ersetzt wurde. Zwei Rollen, ein Tag,
       dieselbe Lehre unabhaengig gezogen.)

       WAS UNVERAENDERT GILT: A-06 traegt den Zustand ERLEDIGT, P-02 den Zustand
       VORLAGE. Beide sind KEINE Verkuerzungen und bleiben unberuehrt — heute
       haben sie zusaetzlich einen Datensatz, was die Invariante ohnehin
       unabhaengig davon macht.
       DAS MUSTER WIRD VORHER GEGEN EINE FANGPROBE GEPRUEFT (Pruefung 7,
       fuenfter Schritt), und sie muss DREI Faelle abdecken:
         `| **W-21** Sparren und Lattung |`   muss unter BEIDEN Mustern treffen
         `| **P-02** parallele Instanzen |`   NUR unter dem breiten Muster
         `| **M-02-Kopienzahl** | drei |`     unter KEINEM — das ist eine Zeile
                                             aus einer BEFUNDTABELLE
       An M-02 waere A-30 fast gescheitert, und P-02 hat A-30 die erste DoR
       gekostet. Beide gehoeren deshalb ausdruecklich in die Probe.
A-33-2 (P1) W-27, W-27/1, W-40 und W-40/1 sind UNBERUEHRT — alle vier
       Tafelzeilen und alle vier Datensaetze. Gegenprobe am Diff: die vier
       Kennungen kommen in den geaenderten Zeilen nicht vor. Ein Zusammenziehen
       waere das LOESCHEN eines Auftrags.
HINWEIS ZU A-33-3, A-33-5 UND A-33-6 (16.08., nach dem Umschnitt):
       Alle drei messen "vor und nach dem Bau". Der BAU aendert docs/STATUS.md
       nicht mehr — er liefert ein Skript. Die drei Kriterien gelten deshalb
       fuer den LAUF des Skripts, nicht fuer den Bau-Commit:
         am Bau-Commit    sind sie trivial erfuellt (die Datei ist unberuehrt)
         am LAUF          gelten sie wie geschrieben und sind dort zu pruefen
       Wer sie am Bau-Commit misst, misst nichts. Der Evaluator hat genau das
       gemeldet, und er hat recht.

A-33-3 (P1) KEIN Zustand und KEIN Ballbesitz geaendert. Gegenprobe: fuer jede
       der ELF Zeilen ist das Statusfeld vor und nach dem Bau zeichengleich.
       Rohausgabe je Zeile.
A-33-4 Der KLARTEXT hinter der Kennung ist wortgleich erhalten. Gegenprobe: im
       Diff steht je Zeile nur die Kennung anders, nicht die Beschreibung.
A-33-5 a26-ball-drift.sh laeuft nach dem Bau ohne neue Meldung. Rohausgabe.
       Und der Lauf VORHER ist mitzuliefern — der Unterschied ist der Nachweis,
       nicht der Endzustand allein.
A-33-6 Nichts geloescht. Der alte Wortlaut steht in der Historie; im Dokument
       wird keine Zeile entfernt, nur die Kennung darin ersetzt. Gegenprobe: die
       Zeilenzahl von docs/STATUS.md ist vorher und nachher gleich.
A-33-7 (NEUGEFASST 16.08. — die alte Fassung war mit dem Umschnitt UNVEREINBAR)

       Kein Produktivcode der Insel. Gegenprobe am Bau-Commit:
         resources/  null Mal
         app/        null Mal
         database/   null Mal
       ERWARTET wird genau EINE Datei unter scripts/ — das Skript IST der
       Liefergegenstand seit Yamas Umschnitt.
       docs/STATUS.md wird vom BAU nicht angefasst; sie aendert sich erst,
       wenn der INTEGRATOR das Skript ausfuehrt.

       WARUM DIE ALTE FASSUNG FIEL, und der Fehler ist meiner:
       Sie verlangte woertlich "der Bau-Commit fasst NUR docs/STATUS.md an —
       scripts/ null Mal". Nach Yamas Umschnitt ist der Liefergegenstand ein
       SKRIPT unter scripts/. Gemessen an 3e22e61b: docs/STATUS.md 0,
       scripts/ 1, resources/ 0, app/ 0 — der Generator konnte A-33-7 nur
       erfuellen, indem er Yamas Anweisung bricht.
       Ich habe beim Umschnitt A-33-1 neugefasst und A-33-7 stehen lassen.
       Gefunden hat es der Evaluator, nicht ich. EIN UMSCHNITT IST NICHT
       FERTIG, WENN EIN KRITERIUM NEU IST — er ist fertig, wenn ALLE
       Kriterien gegen den neuen Liefergegenstand gelesen wurden.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **Muster gegen Fangprobe** (Prüfung 7, 5. Schritt).

```yaml
warum_das_ueberhaupt_zaehlt_obwohl_alle_elf_abgeschlossen_sind: "Der Nutzen ist NICHT, eine Ball-Drift
        bei abgeschlossenen Vorgaengen zu finden — da gibt es keine. Der Nutzen ist §16: zwei
        Schreibweisen fuer einen Auftrag sind zwei Wahrheiten ueber seine IDENTITAET, und die naechste
        Rolle, die einen alten Auftrag wieder aufmacht (wie W-05 -> W-05/2 und W-21 -> W-21/2 heute),
        erbt die Luecke. Ich schreibe das hin, damit niemand den Auftrag fuer wichtiger haelt, als er
        ist: er ist klein, und sein Wert ist Aufraeumen und nicht Gefahrenabwehr."
was_die_pflichtpruefungen_hier_verhindert_haben: "Pruefung 6, und diesmal hat sie den Auftrag nicht
        verkleinert, sondern eine FALSCHE LOESUNG verhindert. Mein erster Gedanke war, die Barriere
        paaren zu lassen — 'ohne Suffix gehoert zu /1'. Gemessen waere das bei W-27 und W-40 falsch,
        weil dort BEIDE Formen eigene Auftraege mit eigenem Titel sind (beide Stellen geoeffnet), und
        bei W-05 und W-21 haette die Heuristik zwei Kandidaten und muesste raten. Eine Barriere, die
        raet, ist schlechter als eine, die schweigt."
und_ich_habe_meinen_eigenen_neuen_handgriff_angewandt: "Das Paarungs-Muster lief gegen eine Fangprobe
        mit fuenf Faellen, BEVOR seine Zahl in dieses Blatt kam — zwei Tafelformen, ein Datensatz, und
        zwei die NICHT treffen durften (die M-02-Befundtabellenzeile und eine eingerueckte
        auftrag-Zeile). Bestanden. Der fuenfte Schritt von Pruefung 7 ist heute Abend entstanden, und
        das ist seine erste Anwendung."
A_33_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```

## 6 — Votum des Evaluators, Runde 1 (16.08.)

```yaml
votum: SPEC_BLOCKED
runde: 1
gemessen_am: 3e22e61b
pruefstand: "eigener Worktree an 3e22e61b, node_modules und vendor per cp -al"

DER_BEFUND_IN_EINEM_SATZ: "A-33-7 verlangt woertlich das Gegenteil dessen, was der Kopf desselben
  Blattes verlangt — und keine Bauarbeit kann beides erfuellen."

A_33_7_IST_MIT_DEM_EIGENEN_BLATT_UNVEREINBAR: |
  A-33-7 (Z.232-233): 'Kein Code. Gegenprobe: der Bau-Commit fasst NUR docs/STATUS.md an —
  scripts/, resources/ und app/ kommen null Mal vor.'
  Kopf art: (Z.13-18): 'UMGESCHNITTEN am 16.08. durch Yama: Liefergegenstand ist
  scripts/a33-kennungen-nachziehen.sh, NICHT die Bearbeitung von docs/STATUS.md.'
  GEMESSEN am Commit 3e22e61b, git show --name-only:
    docs/STATUS.md   0 Mal    (A-33-7 verlangt: das einzige)
    scripts/         1 Mal    (A-33-7 verlangt: null)
    resources/       0 · app/ 0
  Der Bau ist gegen den Kopf richtig und gegen A-33-7 falsch. Der Umschnitt vom 16.08. hat A-33-1
  neugefasst und A-33-7 stehen lassen. Das ist keine Bau-Frage: der Generator kann A-33-7 nur
  erfuellen, indem er Yamas Anweisung bricht. §12.2 — der Umfang des Befundes ist genau diese eine
  Kriterienzeile.

WAS_SONST_NOCH_AN_DER_ALTEN_LOGIK_HAENGT: |
  A-33-3, A-33-5 und A-33-6 messen 'vor und nach dem Bau'. Der Bau aendert docs/STATUS.md nicht
  mehr — in der Bau-Lesart sind alle drei trivial gruen, ohne etwas zu belegen. Aussagekraeftig
  sind sie nur in der LAUF-Lesart, und die habe ich gefahren (siehe Messtisch). Ich schneide nicht
  um; ich melde, dass die Lesart im Blatt nicht mehr festgelegt ist.

DER_BAU_SELBST_TRAEGT, und das gehoert vor den Befund: |
  Sechs von sieben Kriterien habe ich gruen gemessen, jedes selbst gefahren. Das Skript loest die
  Aufgabe sauberer als eine Bearbeitung es koennte: die Zuordnung kommt aus den Daten, Regel 1
  schuetzt W-27 und W-40 vor dem Loeschen, Regel 3 ist der Riegel gegen das Raten, und der
  Trockenlauf am HEUTIGEN Bestand (141 Datensaetze statt 121 am Bau-Stand) liefert unveraendert
  11 Zeilen und NULL unklare Faelle. Genau das war Yamas Grund fuer den Umschnitt.

MEIN_BEINAHE_FEHLER, offengelegt: |
  Ich hatte A-33-5 als P1-Befund fertig: a26-ball-drift meldet nach dem Lauf 31 Zeilen und exit 1,
  wo vorher 0 und exit 0 stand — zweimal reproduziert, mit md5-Ruecksetzung auf 7811e4d8.
  DANN habe ich die Behauptung des Generators geprueft statt sie zu glauben ODER zu verwerfen:
  'ab dem naechsten Commit ist es weg'. Im Pruefstand committet — a26 meldet 0 Zeilen, exit 0.
  a26 liest 'git diff HEAD', arbeitet also auf UNCOMMITTETEN Aenderungen; im Diff steht die
  Umbenennung zweimal, einmal als entfernte alte Kennung ohne Tafelzeile. Mein Befund haette einen
  Zwischenzustand gemeldet, den E1 gar nicht misst. A-33-5 ist am COMMIT gruen.

ZWEI_BEOBACHTUNGEN_OHNE_BEFUNDSTATUS: |
  (1) OFF-BY-ONE IN DER MELDUNG: das Skript meldet 'Z.18  W-01', die Zeile steht auf 19.
      zeilen.forEach((l, i)) liefert 0-basierte Indizes, ausgegeben als 'Z.'. Die WIRKUNG ist
      richtig (die Ersetzung laeuft ueber denselben Index), nur die Angabe zeigt eine Zeile zu
      hoch. In diesem Haus ist das eine belegte Fehlerklasse — A-29, A-34 und A-33s eigener
      Zeiger. Kein Kriterium verlangt Zeilennummern; deshalb Beobachtung und nicht Befund.
  (2) DIE DASH-DIFFERENZ BLEIBT: a26 meldete im Zwischenzustand 'W-01/1 BALL: Tafel *–* <->
      Datensatz *—*' — U+2013 gegen U+2014, je Zeichen gemessen. Sie bestand VORHER schon und war
      nur unsichtbar, weil a26 die zwei Orte ohne gemeinsame Kennung nicht paaren konnte. Der Lauf
      macht sie erstmals sichtbar und a26 verliert sie nach dem Commit wieder aus dem Blick, weil
      es diff-basiert prueft. Gehoert nicht zu A-33 — gemeldet, weil A-33 sie aufgedeckt hat.

WEITERGABE: "SPEC_BLOCKED geht an den PLANNER. Der Bau bleibt liegen wie er ist; er braucht keine
  Aenderung, sondern ein Blatt, das den Umschnitt zu Ende traegt. Wiederabnahme faehrt ALLE SIEBEN
  Kriterien erneut (§12.3)."
```

### Messtisch — jede Kriterienzeile, auch die trivialen

| Kriterium | Messung | Ergebnis |
|---|---|---|
| **A-33-1** (P1, TRAGEND) | Lauf selbst gefahren: `NACHZUZIEHEN: 11 Zeile(n), 11 Kennung(en)`. Zweiter Lauf: `keine — die Invariante haelt bereits`. Fangprobe `--fangprobe` selbst gefahren: **5/5**, die drei verlangten Faelle (W-21 trifft, P-02 trifft, `M-02-Kopienzahl` trifft nicht) einzeln in der Rohausgabe | **grün** |
| **A-33-2** (P1) | Diff nach dem Lauf gegen `W-27\|W-40` gefiltert: **0 Treffer**. Alle vier Tafelzeilen (`W-27`, `W-27/1`, `W-40`, `W-40/1`) vorher 1 / nachher 1 | **grün** |
| **A-33-3** (P1) | Ballbesitz-Spalte je Zeile einzeln: elfmal `'–'` vorher, `'–'` nachher. Zustand `BETRIEBSBESTAETIGT` in allen elf unveraendert | **grün** (in der Bau-Lesart trivial, s. o.) |
| **A-33-4** | Rest der Zeile NACH der Kennung zeichengenau verglichen, alle elf, mit Laengen: 313/313 · 274/274 · 1271/1271 · 668/668 · 315/315 · 455/455 · 648/648 · 308/308 · 440/440 · 329/329 · 304/304 — **alle zeichengleich** | **grün** |
| **A-33-5** | `a26-ball-drift.sh` am COMMIT: **0 Zeilen, exit 0** — vorher wie nachher. Rohausgabe des Zwischenzustands (31 Zeilen, exit 1) samt Aufloesung siehe MEIN_BEINAHE_FEHLER | **grün** |
| **A-33-6** | `wc -l` vorher **19521**, nachher **19521**; entfernte Zeilen im Diff **0** (11 geaenderte) | **grün** |
| **A-33-7** | `git show --name-only 3e22e61b`: `scripts/a33-kennungen-nachziehen.sh`. docs/STATUS.md **0** · scripts/ **1** · resources/ 0 · app/ 0 | **ROT — unerfüllbar, s. Befund** |
| *Zusatz: Trockenlauf am heutigen Bestand* | 82 Kennungen · **141** Datensaetze · 11 Zeilen · **0 unklar**, Datei unberuehrt (`git status` 0) | **trägt** |
| *Zusatz: Idempotenz* | zweiter Lauf schreibt nicht, md5-Ruecksetzung auf `7811e4d8c3e889c350008165eb5644b3` belegt | **trägt** |
| *Browser* | **nicht gefahren** — keine sichtbare Wirkung, der Bau ist ein Skript ohne UI | **entfällt** |
| *§15 / `getDatabaseName()`* | **nicht beruehrt** — kein Datenbankzugriff im Scope | **entfällt** |
| *Insel-Suite / tsc* | **nicht gefahren** — der Bau fasst weder `resources/` noch `app/` an (je 0 Treffer im Commit) | **entfällt** |

### Nachtrag zum Votum — Yamas drei Präzisierungen, jede nachgemessen (16.08., nach `75c471cf`)

```yaml
anlass: "Yama hat waehrend meiner Abnahme drei Punkte praezisiert. Zwei davon hatte ich gemessen,
  einer korrigiert meinen Messtisch an einer Stelle — er steht hier statt still eingearbeitet."

1_IDEMPOTENZ_NACHGEMESSEN_NICHT_BEHAUPTET: "War schon so gefahren. Erster Lauf 'NACHZUZIEHEN: 11
  Zeile(n), 11 Kennung(en)', zweiter Lauf 'keine — die Invariante haelt bereits', beide Rohausgaben
  im Messtisch, md5-Ruecksetzung auf 7811e4d8c3e889c350008165eb5644b3 belegt."

2_DIE_ZIELZAHL_FEHLT_ABSICHTLICH: "Ich habe sie NICHT beanstandet und A-33-1 gruen gemessen — an
  der INVARIANTE, nicht an einer Zahl: nach dem Lauf keine verkuerzte Tafelzeile mehr, deren
  Datensatz die volle Form traegt, und der zweite Lauf meldet null. Dass die Zahl driftet, ist bei
  dieser Datei heute selbst belegt: am Bau-Stand 121 Datensaetze, am heutigen Bestand 141 — und die
  Invariante liefert in beiden Faellen unveraendert 11 Zeilen und NULL unklare Faelle."

3_DIE_WIRKUNG_IST_DAS_EIGENTLICHE_KRITERIUM — DAS KORRIGIERT MEINEN MESSTISCH: |
  Ich hatte A-33-5 als 'a26 laeuft ohne neue Meldung' gemessen und mit 0 Zeilen/exit 0 gruen
  gegeben. Das misst die STILLE, nicht die WIRKUNG. Yamas Kriterium ist das andere: a26 muss NACH
  dem Lauf an den Zeilen ANSCHLAGEN koennen, an denen es vorher still weitersprang.
  JETZT AM BESTAND GEMESSEN, je Kennung einzeln, vorher am Bau-Stand 3e22e61b und nachher am
  Lauf-Stand — nicht aus dem Skript behauptet:
    W-01  vorher Tafel 1 + Datensatz 0 -> Ausstieg   nachher Tafel 1 + Datensatz 1 -> PAART
    W-02  W-04  W-05  W-08  W-09  W-11  W-13  W-15  W-21  W-22 — alle zehn identisch
    SUMME: paarbar vorher 0 von 11, nachher 11 von 11.
  DIE WIRKUNG IST DAMIT BELEGT, und sie ist staerker als meine Stille-Messung: der Ausstieg
  schreibt woertlich 'kein Datensatz-Block — Ball und Zustand nicht vergleichbar'; nach dem Lauf
  greift er bei keiner der elf mehr. Als Beleg, dass a26 danach wirklich VERGLEICHT, dient die
  Meldung, die es dabei erstmals erzeugt: 'W-01/1 BALL: Tafel *–* <-> Datensatz *—*' — U+2013 gegen
  U+2014, je Zeichen gemessen. Diese Differenz bestand vorher schon und war nur unsichtbar.
  MEIN MESSFEHLER IN EINEM SATZ: ich habe im Zwischenzustand dieselbe Rohausgabe gesehen und sie
  als SCHADEN gelesen (Beinahe-P1 gegen A-33-5), waehrend sie zur Haelfte der WIRKUNGSNACHWEIS ist.

DER_ZEIGER_a26_56_TRIFFT_NICHT_MEHR, und das Blatt weiss es selbst: |
  Yama nennt a26-ball-drift.sh:56. GEOEFFNET: :55 ist '[ -z "$KENNUNGEN" ] && exit 0', :56 ist eine
  LEERZEILE, :57 ein Kommentar. Der Ausstieg, um den es geht, liegt auf :96-98
  (START-Zuweisung, if [ -z "$START" ], UNGEPRUEFT-Zeile). Das Auftragsblatt hat genau diesen
  Zeiger am 14.08. berichtigt — 'auf :56 eine Leerzeile statt des stillen continue (heute :97)' —
  nach dem A-30-Bau, der die Datei von 102 auf 158 Zeilen umgebaut hat. Fuenfter belegter Fall
  dieser Klasse. Am Inhalt der Anweisung aendert es nichts.

OFFEN_UND_NICHT_ABSCHLIESSEND_GEKLAERT, ohne Bewertung: |
  a26 im STAND-Modus (bash scripts/a26-ball-drift.sh <sha>) meldet am Lauf-Commit NICHTS, obwohl
  der Diff 22 Kennungen enthaelt (W-01 bis W-22 in alter UND neuer Form, selbst gezaehlt). Im
  Arbeitsbaum-Modus meldet dieselbe Datenlage 31 Zeilen. Warum die zwei Modi auseinanderlaufen,
  habe ich NICHT geklaert — es gehoert nicht zu A-33 und ich melde es ohne Deutung.

DAS_VOTUM_BLEIBT: "SPEC_BLOCKED. Es haengt an A-33-7, und keine dieser drei Praezisierungen
  beruehrt A-33-7. Der Bau traegt; das Blatt traegt den Umschnitt nicht zu Ende. Ball beim Planner."
```
