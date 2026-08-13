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
art: "BAU — ELF Tafelzeilen auf die Kennung ihres Datensatzes ziehen. Reine Datenberichtigung,
      kein Code."
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
grundlage: "docs/STATUS.md · scripts/a26-ball-drift.sh:32/:55-56 · §16 (eine Statuswahrheit) ·
            A-20-2 · A-30 (die Sichtbarkeit)"
```

## 1 — Der Befund: ELF Zeilen, und die Barriere paart sie nicht

```text
GEMESSEN am Stand f9b67b1b, Muster gegen eine FANGPROBE geprueft (Pruefung 7,
fuenfter Schritt — die Probe deckt auch den M-02-Fall, an dem A-30 fast
gescheitert waere):
  Tafelzeilen mit Kennung   67
  Datensaetze mit auftrag   66
  Tafel ohne Datensatz      12

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
A-33-1 (P1, TRAGEND) Nach dem Bau enthaelt die Menge „Tafelzeile ohne
       Datensatz" KEINE Verkuerzung mehr. Die Zielzahl wird MIT IHREM MUSTER
       genannt, weil sie sonst zwei Zahlen ist — dieselbe Klasse, die bei A-30
       schon einmal die DoR gekostet hat:
         unter dem Muster A-/W-        genau EINS   -> A-06
         unter allen Grossbuchstaben   genau ZWEI   -> A-06 und P-02
       A-06 hat KEINEN Datensatz und den Zustand ERLEDIGT; P-02 traegt den
       Zustand VORLAGE und braucht legitim keinen — beide sind keine
       Verkuerzungen und bleiben stehen.
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
A-33-7 Kein Code. Gegenprobe: der Bau-Commit fasst NUR docs/STATUS.md an —
       scripts/, resources/ und app/ kommen null Mal vor.
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
