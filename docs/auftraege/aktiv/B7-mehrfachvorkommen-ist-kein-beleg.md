# B7 — Mehrfachvorkommen ist kein Beleg. Und der **Ort** ist kein Beleg für die **Wirkung**

```yaml
auftrag: "B7"
titel: "Verbreitung sieht wie Bestaetigung aus. Barriere gegen die Zahl, die nur oft ist"
art: "BARRIERE — nach dem Muster von B5 und B6"
spur: A
heimat_app: ticket
dor_beleg: "3403c601 — plan-pruefer: 'B7 BEREIT (2. Runde), der Restpunkt ist behoben'. Zustand vom Planner NACHGEZOGEN, nicht entschieden — der Pruefer hat ihn belegt und seinen Block geschrieben, Tafelzeile und Blattkopf hingen nach."
status_steht_in: docs/STATUS.md
basis_sha: 5d88f198
prioritaet: P2
anlass: "Yamas Antwort 12.08. Punkt 2 ('Schneide sie als B7'), Wortlaut von ihm"
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "F-051 🔴 · docs/BERICHT-M02-AUSGEWERTET.md · A-16 (der vierte Fundort und seine Messung)"
```

## Die Regel — Yamas Wortlaut, unverändert

> **HAUSREGEL · Mehrfachvorkommen ist kein Beleg.**
> Dieselbe Zahl an vier Stellen ist **nicht** vier Belege — sie ist ein Beleg, dreimal
> kopiert, oder gar keiner, viermal kopiert. Die Frage ist nie *„wie oft kommt sie vor"*,
> sondern **„wie oft kommt sie UNABHÄNGIG vor"**.
> Wer Vorkommen zählt, misst Verbreitung. Wer Herkunft prüft, misst Wahrheit.

## Der Beleg liegt vor, bevor die Barriere gebaut wird

```text
TIME_VARS, elf Zeitwerte:
  1  docs/planner/pv-belegung-referenz/DachplanerProPage.tsx      Prototyp
  2  M-02 (Archivbestand)                                        Prototyp
  3  M-02-Kopie                                                  Prototyp, byte-identisch
  4  resources/views/admin/layouts/roof.blade.php:74             Produktivbaum, WIP-Checkpoint
  --------------------------------------------------------------------------------
  unabhaengige Herkunftsangaben: NULL. Der Kommentar der Quelle sagt es selbst:
  "time assumptions (minutes) – adjust to your company values"
```

*Vier Fundorte, null Quellen. Und die Reihe hat einen Kommentar, der zugibt, dass sie
Platzhalter ist — er wurde viermal mitkopiert und nie eingelöst.*

## Die Schärfung — sie kommt aus A-16 und ist heute gemessen worden

**Yamas Regel trifft die Frage „wie oft".** A-16 hat gezeigt, dass daneben eine zweite,
gleich aussehende Falle liegt — die Frage **„wo"**:

```text
Ich hatte den vierten Fundort als "laufender Produktivcode" gelesen, weil er in
resources/views/ liegt. Gemessen:
  statische View-Referenzen auf die Datei     0 / 0 / 0  (drei Schreibweisen)
  Route, die so heisst                        zeigt auf eine ANDERE Datei ohne TIME_VARS
  Historie der Datei                          EIN Commit, "Checkpoint: save WIP"
-> Der Ort sah wie Auslieferung aus. Er war keine.
```

> **B7 hat deshalb zwei Sätze, nicht einen:**
> **(a)** *Wie oft eine Zahl vorkommt, sagt nichts über ihre Herkunft.*
> **(b)** *Wo eine Datei liegt, sagt nichts über ihre Wirkung.*
>
> *Beides ist dieselbe Verwechslung: **ein Merkmal, das leicht zu zählen ist, wird für das
> Merkmal genommen, auf das es ankommt.** Vorkommen statt Herkunft, Ort statt Ausführung.*

## Abnahmekriterien

```text
B7-1  Die Regel steht in docs/ARBEITSREGELN.md §18a als H-8, im Wortlaut Yamas, mit beiden
      Teilen (a) und (b). Nicht als neue Sammlung, nicht als zweite Datei — §18a ist die
      Heimat der Hausregeln, das ist am 12.08. entschieden worden.

B7-2  WARNUNG, NICHT ABBRUCH — die Lehre aus B5/B6 und dem A-03-Beleg. Wenn ein Bericht eine
      Zahl mit mehr als einem Fundort nennt und keine Herkunftsangabe fuehrt, wird gewarnt.
      Ein Abbruch waere hier falsch, weil Mehrfachvorkommen auch voellig harmlos ist
      (jede Konstante kommt mehrfach vor).

B7-3  GEGENBELEG, dass die Warnung SCHWEIGT, wo sie schweigen muss: eine Zahl mit genau EINEM
      Fundort und eine Zahl MIT Herkunftsangabe loesen sie nicht aus. Ohne diesen Beleg ist
      eine Warnung, die immer feuert, dasselbe wie keine — die Lehre aus B6-2.

B7-4  DER FUNDORT-TEIL (b) ist gesondert pruefbar: eine Aussage der Form "steht im
      Produktivcode" gilt erst als belegt, wenn ein AUFRUFER genannt ist — Route, @include,
      @extends oder ein aufgeloester dynamischer View-Name. Ordnerlage genuegt nicht.
      Belegform: Befehl + Trefferzeile (B5-Standard).

B7-5  DIE REICHWEITE der eigenen Messung wird mitgenannt, wenn sie begrenzt ist: "kein
      statischer Aufrufer" ist eine andere Aussage als "unerreichbar". A-16 fuehrt es vor —
      dort ist die dynamische Luecke ausdruecklich als offen benannt, statt weggelassen.

B7-6  KEINE DRITTE STELLE. Beim Eintrag in §18a wird geprueft, dass H-8 nicht zusaetzlich in
      docs/HAUSREGELN.md landet — die Datei ist am 12.08. zum Wegweiser aufgeloest worden und
      traegt keinen Regelinhalt mehr. Ihre Wegweiser-Tabelle wird um H-8 ergaenzt, ohne den
      Regeltext zu wiederholen.
```

## §5 · AUSWIRKUNGEN und `must_preserve` — Nachtrag nach dem DoR-Restpunkt (`8b1b9d05`)

**Der Plan-Prüfer hat recht, und die Begründung ist stärker als der Formfehler:** *B7 ist die **dritte**
Barriere in derselben Datei. B5 und B6 stehen auf `BEREIT` und sind **nicht gebaut**. Eine Reihenfolge
ist keine Zusage — wer zuerst baut, könnte den Platz der anderen besetzen, und keines der drei
Blätter sagt heute, dass das nicht passieren darf.*

```text
B7-7 (must_preserve) — vier Zusagen, jede einzeln nachweisbar:

  (1) §18a BESTAND    H-1 bis H-7 bleiben zeichengleich. H-8 wird ANGEHAENGT, keine
                      bestehende Regel umformuliert, umnummeriert oder verschoben.
                      Nachweis: git diff docs/ARBEITSREGELN.md zeigt 0 geloeschte Zeilen
                      im §18a-Block, ausschliesslich Einfuegungen.

  (2) DIE ANDEREN ZWEI BARRIEREN   B5 und B6 sind unbebaut. B7 belegt ihre Stellen im Tor
                      NICHT und formuliert seine Pruefung so, dass sie neben ihnen stehen
                      kann. Nachweis vor dem Bau: die Einfuegestelle von B7 wird benannt
                      und gegen die in B5/B6 vorgesehenen Stellen gehalten — beruehren sie
                      sich, geht der Auftrag zurueck an den Planner statt sich zu einigen.

  (3) DAS TOR SELBST  Rollenmarke, Pfadpruefung, Index-Angleichung und die Barrieren B1-B4
                      unveraendert. Nachweis: git diff scripts/commit-pruefen.sh zeigt nur
                      Einfuegungen, 0 geloeschte Zeilen.

  (4) PRODUKTIVCODE   resources/** und app/** byte-identisch. B7 ist eine Prozessbarriere
                      und faehrt an keiner Fachdatei vorbei. Nachweis: git diff --stat
                      nennt weder resources/ noch app/.
```

> **Warum (2) die eigentliche Zusage ist:** *die drei Barrieren sind nacheinander geschnitten und
> werden womöglich in umgekehrter Reihenfolge gebaut. Ohne diese Zeile ist die Frage „wessen Änderung
> gewinnt" erst dann sichtbar, wenn sie schon verloren ist.*

## Rückweg & Entdeckung — als eigene Zeile

```text
RUECKWEG      reiner Revert; B7 fuegt Text in §18a ein und ein Pruefmuster in das Werkzeug.
              Kein Datenpfad, kein Wert, keine Migration.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier NICHT behauptet.
              BERICHTIGUNG meiner ersten Fassung: dort stand "vorhanden: fork/main +
              backup-private/main". Selbst nachgemessen, nachdem der Release-Pruefer den
              abgelehnten Push gemeldet hatte:
                7d6c39cf (der Commit, der dieses Blatt traegt)
                  auf fork/main             NEIN
                  auf backup-private/main   NEIN
                  auf origin/main           NEIN
              Ich hatte eine Kopie zugesagt, die es fuer diesen Stand nicht gab — B5 in
              eigener Sache, ein Wort statt einer Stelle. Der Generator baut erst, wenn der
              Rueckfallpunkt AM BAUTAG gemessen und im Bericht mit Befehl belegt ist.
ENTDECKUNG    das Signal ist die Warnung selbst: feuert sie bei einer einfach vorkommenden
              Zahl mit Quellenangabe, ist B7-3 gebrochen und das Werkzeug macht Laerm statt
              Arbeit. Messbar an den beiden Gegenproben aus B7-3.
```

## Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Index LEER · §3 = 1 (W-09 Treppe, Generator) · Scope von B7 = docs/ARBEITSREGELN.md,
docs/HAUSREGELN.md (Wegweiserzeile), Pruefwerkzeug. Keine Ueberschneidung mit W-09.
B7 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
ACHTUNG fuer den Generator: docs/ARBEITSREGELN.md ist die Prozessquelle und wird von mehreren
Rollen gelesen. §18a ist zuletzt vom Planner geschrieben, das Gegenlesen durch den
plan-pruefer steht noch offen — B7-1 haengt daran und darf es nicht ueberholen.
```

```yaml
ballbesitz: "plan-pruefer (DoR)"
abhaengigkeit: "B7-1 setzt voraus, dass §18a gegengelesen ist (offener Posten seit 12.08.)"
beleg_liegt_vor: "vier Fundorte, null Quellen — und der vierte hat die Schaerfung (b) geliefert"
```

## §11 — Votum B7 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "B7"
votum: ABGENOMMEN
fehlerklasse: KEINE   # ein P2, blockiert nicht
abnahme_commit: "b1554b01"
elter: "9d09b02d"
pruefstand: "worktree --detach auf b1554b01 und 9d09b02d, node_modules UND vendor an beiden"
pruefform: "Dritte Barriere in derselben Datei — also dieselbe Form wie B5 und B6 (ausloesen,
     nicht lesen) UND die Frage, die mit jeder weiteren Barriere schwerer wiegt: verdecken sich
     inzwischen DREI Waechter?"

messtisch_alle_sieben:
  B7-1: GRUEN
    beleg: "grep -c 'H-8' docs/ARBEITSREGELN.md — Elter 0, Bau 1. H-8 steht in §18a als
            '### H-8 · Mehrfachvorkommen ist kein Beleg — und der Ort ist kein Beleg fuer die
            Wirkung' (Z.812), ANGEHAENGT hinter H-7 und vor §18b."
    seine_acht_zeilennummern_selbst_nachgeschlagen: |
      752 H-1 · 762 H-2 · 767 H-3 · 776 H-4 · 786 H-5 · 795 H-6 · 803 H-7 · 812 H-8
      Alle acht geoeffnet, alle acht tragen genau die genannte Ueberschrift.
    beide_teile: "(a) 'Dieselbe Zahl an vier Stellen ist nicht vier Belege … Wer Vorkommen
            zaehlt, misst Verbreitung. Wer Herkunft prueft, misst Wahrheit.'
            (b) 'Wo eine Datei liegt, sagt nichts ueber ihre Wirkung … gilt erst als belegt,
            wenn ein Aufrufer genannt ist … Ordnerlage genuegt nicht.' Beide im Wortlaut."
  B7-2: GRUEN
    ausgeloest: |
      $ TICKET_ROLLE=evaluator bash scripts/commit-pruefen.sh \
          "TIME_VARS steht an vier Fundorten im Produktivcode" docs/a.md
      B7-WARNUNG  Mehrere Fundorte genannt, aber keine Herkunft.
                  Wie oft etwas vorkommt, sagt nichts darueber, WOHER es kommt: dieselbe
                  Zahl an vier Stellen ist ein Beleg dreimal kopiert — oder keiner.
                  Und 'steht im Produktivcode' gilt erst mit genanntem AUFRUFER;
                  Ordnerlage ist kein Beleg fuer Wirkung.
                  Warnung, kein Abbruch — der Commit laeuft weiter.
      exit=0
  B7-3: GRUEN
    drei_gegenproben: |
      "die Konstante steht an genau einem Fundort: wandaufbau.ts:57"      -> B7 0, exit 0
      "vier Fundorte, alle aus derselben Quelle kopiert — Herkunft: …"    -> B7 0, exit 0
      "Suite 1698/1698 gruen"                                             -> B7 0, exit 0
    bewertung: "Ein Fundort schweigt, mehrere MIT Herkunft schweigen, eine reine Zahl schweigt.
            Genau die drei Faelle, die das Kriterium nennt."
  B7-4: GRUEN
    beleg: "H-8(b) formuliert die Pruefbarkeit woertlich: der Aufrufer muss genannt sein —
            'Route, @include, @extends oder ein aufgeloester dynamischer View-Name' — und
            'Ordnerlage genuegt nicht'. Damit ist Teil (b) gesondert pruefbar, wie verlangt."
  B7-5: GRUEN
    beleg: "ARBEITSREGELN.md:834 traegt die Reichweiten-Zeile: 'kein statischer Aufrufer ist
            eine andere Aussage als unerreichbar. Wer die dynamische Luecke nicht ausschliessen
            kann, benennt sie — statt sie wegzulassen.' Selbst aufgeschlagen."
  B7-6: GRUEN — und das ist die feinste der sieben Zusagen
    beleg: "docs/HAUSREGELN.md: +1 Zeile, +0 geloescht. Die eine Zeile ist ein
            WEGWEISER-Tabelleneintrag in derselben Form wie H-1 bis H-7:
              | **H-8** | Mehrfachvorkommen ist kein Beleg — und der Ort ist kein Beleg
                          fuer die Wirkung | dito |
            Kein Regeltext, keine zweite Fassung. Genau eine Stelle traegt die Regel."
  B7-7: GRUEN — alle vier Zusagen einzeln
    zusage_1_bestand: "H-1 bis H-7: ich habe jede Regel an Elter und Bau gehasht — alle sieben
            IDENTISCH. ARBEITSREGELN.md +30/-0, also keine umformuliert oder verschoben."
    zusage_2_die_anderen_barrieren: "Der Kern, und ich habe ihn in zwei Richtungen gemessen:
            (i) Die Bloecke B5_ZAEHLWORT und B6_SUMMENWORT sind an Elter und Bau BYTEGLEICH
                (md5 ee42893e bzw. db6b9d24, je beidseitig).
            (ii) Mutation mit dem richtigen Anker: B7_MEHRFACH-Block entfernt, dieselbe
                 Botschaft — B7 0, B6 1, B5 1. Die drei Waechter feuern unabhaengig."
    zusage_3_das_tor: "scripts/commit-pruefen.sh +26/-0. Rollenmarke, Pfadpruefung und
            Index-Angleichung unberuehrt; die scripts-Suite laeuft an Bau UND Elter 107/107/0."
    zusage_4_produktivcode: "resources/ und app/: 0 Dateien im Bau-Diff."

p2_der_zustand_fehlt:
  klasse: BEWEIS
  was: "B7 stand am Elter des Baus an beiden Orten auf BEREIT — der Auftrag wurde nie auf
        IN_ARBEIT gesetzt, und die Bau-Botschaft traegt keinen §3-Beleg."
  warum_kein_rot: "B7 hat als einziger der drei B-Auftraege KEIN §3-Kriterium (B7-1 bis -7,
        keines davon). Es ist also kein verletztes Abnahmekriterium, sondern die Regel selbst.
        Und die Schutzfrage habe ich gemessen: zum Bauzeitpunkt war die Tafel LEER und das
        Zustandsfeld LEER — kein Auftrag lief, also war keine fremde Scope-Sperre zu verletzen."
  derselbe_massstab: "Ich habe heute dreimal ein Votum nur ins Blatt geschrieben, weil
        docs/STATUS.md fremd belegt war, und es jedes Mal offengelegt. Dieselbe Lage, dieselbe
        Bewertung — mit dem Unterschied, dass er es hier nicht benannt hat. Das ist der Rest:
        nicht das Unterlassen, sondern das Schweigen darueber."

was_diesen_bau_heraushebt:
  - "Er meldet einen eigenen Messfehler, den niemand gefunden haette: seine Rueckgabewert-Messung
     lief im falschen Verzeichnis, dreimal exit 127. Und er nennt den Grund, warum das gefaehrlich
     ist — '127 ist der Rueckgabewert, der wie ein Messergebnis aussieht und keines ist'. Er haette
     beinahe 'der Rueckgabewert hat sich geaendert' bedeutet."
  - "Die dabei entstandene Streudatei a.txt hat er BEISEITEGELEGT, nicht geloescht, mit Verweis auf
     die Dauerregel. Ich habe im Wurzelverzeichnis nachgesehen: sie ist weder dort noch in
     git status."
  - "H-8 ist angehaengt statt eingeschoben — die Nummerierung von H-1 bis H-7 bleibt, und damit
     bleiben alle Verweise darauf gueltig."

zusammenfassung: "Sieben von sieben. Die dritte Barriere in derselben Datei steht neben den beiden
     anderen, ohne sie zu beruehren — und das ist nicht behauptet, sondern zweifach gemessen:
     bytegleiche Bloecke und eine Mutation, nach der B5 und B6 weiterfeuern. Die Regel steht an
     GENAU EINER Stelle, der Wegweiser traegt nur den Verweis. Ein P2: der Zustand wurde nie auf
     IN_ARBEIT gesetzt — ohne Schaden, weil zum Bauzeitpunkt nachweislich kein Auftrag lief."

ballbesitz: release-pruefer
```
