# B5N — Die Barriere schlägt bei richtiger Arbeit an. Dreimal gemeldet, jetzt ein Auftrag statt einer vierten Meldung

```yaml
auftrag: "B5N"
art: "Nachbesserung nach §12.5 — B5 bleibt RELEASE_FREI, der Befund wirkt nicht rueckwirkend"
titel: "B5_BELEGZEILE erkennt nur datei.ext:zeile — die Schreibweise Z.217-268 faellt durch"
spur: A
heimat_app: ticket
status_steht_in: docs/STATUS.md
basis_sha: 8870387a
prioritaet: P2
anlass: "Plan-Pruefer 8870387a, woertlich: 'Inzwischen DREIMAL gemeldet … gehoert aber in einen
         Auftrag statt in eine vierte Meldung.' Vorher: Evaluator als p2 in der B5-Abnahme,
         Release-Pruefer im B6-Lauf."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "B5 (RELEASE_FREI) · scripts/commit-pruefen.sh:533-537 · A-03-Beleg zur Warnungsmuedigkeit"
```

## Der Befund — im Tor gelesen, nicht vermutet

```text
scripts/commit-pruefen.sh:534
  B5_BELEGZEILE='[A-Za-z0-9_./-]+\.[A-Za-z]{1,5}:[0-9]+|:[0-9]+:|Trefferzeile'

Erkannt wird:      dachGeometrie.ts:44   ·   :2076:   ·   das Wort "Trefferzeile"
NICHT erkannt:     Z.217-268   ·   Z.217   ·   "Zeile 171"
```

**Erhoben über die letzten 40 Commit-Botschaften — welche Schreibweisen real vorkommen:**

```text
datei.ext:zeile          40 Vorkommen   z.B. enginePanels.ts:241      ERKANNT
Trefferzeile             13 Vorkommen                                 ERKANNT
Z.NNN                    12 Vorkommen   z.B. Z.217                    fällt durch
Z.NNN-NNN                 4 Vorkommen   z.B. Z.217-268                fällt durch
"Zeile NNN"               4 Vorkommen   z.B. Zeile 171                fällt durch
:NNN:                     1 Vorkommen   z.B. :2076:                   ERKANNT
```

**PRÄZISIERT 12.08. — der Generator hat die richtigere Metrik geliefert (`53930b60`), selbst
nachgemessen und zeichengleich bestätigt:**

```text
Botschaften (nicht Vorkommen) mit Z.NNN in den letzten 40   ->  9
davon OHNE zusaetzlich eine ERKANNTE Form                   ->  7   <- zu Unrecht gewarnt
```

> **Meine Zahl „20 von 40 fallen durch" war die falsche Größe.** *Sie zählte **Vorkommen**, und eine
> Botschaft, die `Z.217` **und** `datei.ts:44` trägt, wird gar nicht gewarnt — die Barriere prüft pro
> Botschaft, nicht pro Fundstelle. **Richtig ist: sieben Botschaften mit gelesenen Trefferzeilen
> wären zu Unrecht gewarnt worden.** Die Zahl ist kleiner und der Befund dadurch belastbarer: er
> steht jetzt auf der Größe, die die Barriere tatsächlich misst.*

**Und es ist seine eigene Barriere** — sein Satz dazu: *„Das ist mein Muster und mein Fehler, und es
ist die gefährliche Richtung: eine Barriere, die falsch anschlägt, wird abgeschaltet — genau die
A-03-Lehre, die ich selbst in den Kommentar geschrieben habe."*

> **Sieben von neun Botschaften mit dieser Schreibweise fallen durch.** *Das ist keine Randform: `Z.` ist die
> gängige Schreibweise, wenn die Datei im Satz vorher genannt wurde — „`STATUS.md`, Z.217-268" ist
> **präziser** als eine Wiederholung des Dateinamens, und sie wird bestraft.*

## Warum das die Barriere entwertet, und zwar messbar

**Der Plan-Prüfer nennt den Mechanismus:** *„Eine Warnung, die bei RICHTIGER Arbeit anschlägt, wird
nach der dritten Wiederholung weggeklickt, und genau das macht sie wertlos."*

```text
Meldung 1   Evaluator, als p2 in der B5-Abnahme
Meldung 2   Release-Pruefer, im B6-Lauf  — er hat NACHGESEHEN statt weggeklickt (7a37fea8)
Meldung 3   Plan-Pruefer, aus der Wache (8870387a)
-> die vierte waere eine Meldung ueber eine Meldung. Deshalb dieser Auftrag.
```

**Und ich bin selbst der Beleg:** *ich habe `Z.NNN` heute **achtmal** in eigenen Botschaften benutzt —
jedes Mal mit gelesenen Trefferzeilen daneben. Die Barriere hätte mich achtmal gewarnt, und in keinem
Fall zu Recht.* **Das ist genau die A-03-Klasse:** eine Barriere, die immer anschlägt, wird
abgeschaltet — und dann fehlt sie dort, wo sie zählt.

## DECISION

```text
ERWEITERN     B5_BELEGZEILE um die drei gemessenen Schreibweisen. Kein neues Konzept, kein
              zweiter Ausdruck, keine zweite Warnung — EIN Muster, drei Alternativen mehr.
NICHT         Die Warnung wird NICHT zum Abbruch. B5 warnt, das bleibt (A-03-Beleg).
NICHT         B5_ZAEHLWORT bleibt unberuehrt. Der Fehler liegt in der Belegseite, nicht in
              der Erkennung des Zaehlworts — wer beides anfasst, kann nicht mehr sagen,
              welche Aenderung gewirkt hat.
NICHT         Keine Verschaerfung. Dieser Auftrag macht die Barriere leiser, nicht lauter;
              wer bei dieser Gelegenheit zusaetzliche Faelle einfangen will, schneidet dafuer
              einen eigenen Auftrag.
```

## Abnahmekriterien

```text
B5N-1  (P1) Die drei gemessenen Schreibweisen loesen die Warnung NICHT mehr aus:
         Z.217   ·   Z.217-268   ·   "Zeile 171"
       Gegenprobe je Form EINZELN gefahren, Ausgabe im Bericht. Drei Formen, drei Belege —
       eine Sammelaussage "erkennt jetzt alles" genuegt nicht.

B5N-2  (P1, DER TRAGENDE PUNKT) Die Warnung SCHWEIGT nicht ueberall: eine Botschaft mit
       Zaehlwort und OHNE jede Belegzeile loest sie weiterhin aus. Ohne diesen Gegenbeleg
       waere die Nachbesserung eine Abschaltung mit anderem Namen.
       Belegform: dieselbe Probe zweimal, einmal mit und einmal ohne Beleg.

B5N-3  (P2) Die ERKANNTEN Formen bleiben erkannt: datei.ext:zeile, :NNN: und das Wort
       Trefferzeile loesen weiterhin NICHT aus. Nachweis: git diff zeigt, dass die drei
       vorhandenen Alternativen zeichengleich stehen — nur ANGEHAENGT wurde.

B5N-4  (must_preserve) scripts/commit-pruefen.sh: die Barrieren B1 bis B4, B6 und die
       Torfunktionen (Rollenmarke, Pfadpruefung, Index-Angleichung) unveraendert. Nachweis:
       git diff nennt genau EINE geaenderte Zeile (534) und 0 geloeschte.
       resources/** und app/** byte-identisch.

B5N-5  (P2, der zweite Befund wird NICHT mitbehandelt) Der Release-Pruefer hat gemeldet:
       'B6 fuegt dem Tor Code hinzu und bringt keinen Test mit' — Suite am Elter 107/107,
       am Bau 107/107, diff auf scripts/__tests__ LEER. Das gilt fuer B5 GENAUSO und ist
       ein eigener Vorgang: eine Barriere ohne Test ist eine Behauptung ueber sich selbst.
       Hier nur BENANNT, damit er nicht als erledigt gilt.
       ABER: dieser Auftrag selbst darf die Luecke nicht vergroessern — wenn scripts/__tests__
       eine Stelle hat, an der B5s Muster geprueft wird, kommen die drei Formen dort hinzu.
       Ob es sie gibt, ist zu MESSEN und im Bericht zu sagen, nicht anzunehmen.

B5N-6  (P1, §3 wird BELEGT) Beide Orte nach der VERANKERTEN Methode (ARBEITSREGELN §3,
       Abschnitt 'Die Pruefmethode'): Tafelzeile mit ^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT und
       Zustandsfeld, beide Zahlen genannt, Messung unmittelbar vor der ersten Aenderung.
```

## Rückweg & Entdeckung — als eigene Zeile

```text
RUECKWEG      reiner Revert EINER Zeile im Tor. Kein Datenpfad, keine Migration, kein
              Produktivcode. Rueckwaerts-Patch via git apply --check -R Exit 0.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier NICHT behauptet.
ENTDECKUNG    zwei Signale, beide ohne Fachwissen ablesbar:
              (1) die Warnung erscheint bei einer Botschaft mit Z.NNN  -> B5N-1 gebrochen
              (2) die Warnung erscheint NICHT bei einer Botschaft ohne jeden Beleg
                  -> B5N-2 gebrochen, und das ist der gefaehrlichere Fall: dann schweigt
                  die Barriere und niemand merkt es, weil Schweigen wie Erfolg aussieht.
```

## Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Tafelzeile   ^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT   ->  0
Zustandsfeld ^zustand: *IN_ARBEIT                ->  0
             deckungsgleich, §3 ist FREI
ACHTUNG fuer die Reihenfolge: scripts/commit-pruefen.sh liegt im Scope von B7 (BEREIT) und
             lag im Scope von B5 und B6. B5N und B7 duerfen NICHT gleichzeitig laufen —
             wer zuerst zieht, hat die Datei, der andere wartet. Das ist keine Reihenfolge-
             vorgabe, sondern die Sperre selbst; sie gilt ohne Zutun.
B5N wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
ballbesitz: "plan-pruefer (DoR)"
umfang: "EINE Zeile im Tor plus drei Gegenproben. Kleiner als B5, B6 und B7."
warum_nicht_still_erweitert: "das Tor ist eine Schranke, die allen Rollen gehoert. Der Generator
       hat am 12.08. genau hier die Linie gezogen und das §3-Muster NICHT eigenmaechtig geaendert,
       obwohl er es in der Hand hatte. Dieselbe Linie gilt fuer B5s Muster."
zweiter_posten_offen: "B5N-5 — Barrieren ohne Test. Betrifft B5 UND B6, eigener Vorgang."
```

## §11 — Votum B5N (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "B5N"
votum: ABGENOMMEN
fehlerklasse: KEINE
abnahme_commit: "c54c7129"
elter: "a1751fbe"
in_arbeit_commit: "2766e0ac"
pruefstand: "worktree --detach auf c54c7129 und a1751fbe, node_modules UND vendor an beiden"
befangenheit_offengelegt: "Dieser Auftrag entstand aus MEINEM Befund in der B5-Abnahme
     (b7ab49c5, P2 Musterluecke). Ich pruefe hier also die Behebung meines eigenen Einwands.
     Das ist keine Selbstabnahme — gebaut hat der Generator, und ich messe gegen die
     KRITERIEN des Blattes, nicht gegen meinen Einwand. Wo beide dasselbe sagen, sage ich es
     dazu."

messtisch_alle_sechs:
  B5N-1: GRUEN — drei Formen, drei EINZELNE Belege
    beleg: |
      "gezaehlt, Trefferzeilen in Z.217"               -> B5 0 Warnungen, exit 0
      "gemessen, die Stellen stehen in Z.217-268"      -> B5 0 Warnungen, exit 0
      "die Fundstelle steht in Zeile 171, gezaehlt"    -> B5 0 Warnungen, exit 0
    bemerkung: "Das Kriterium verlangt ausdruecklich je Form eine eigene Probe und verbietet
            die Sammelaussage 'erkennt jetzt alles'. Ich habe drei Laeufe gefahren."
  B5N-2: GRUEN — der tragende Punkt
    beleg: |
      "vier Treffer gezaehlt"                          -> B5 1 Warnung
      "vier Treffer gezaehlt, Trefferzeilen in Z.217"  -> B5 0 Warnungen
    bewertung: "Dieselbe Botschaft, einmal ohne und einmal mit Beleg. Die Barriere ist LEISER
            geworden, nicht stumm — genau der Unterschied, den das Kriterium fordert."
  B5N-3: GRUEN
    beleg: |
      "gezaehlt, Trefferzeilen in wandaufbau.ts:57"    -> B5 0   (datei.ext:zeile)
      "gezaehlt, siehe :891:"                          -> B5 0   (:NNN:)
      "gezaehlt, Trefferzeile unten"                   -> B5 0   (das Wort)
    diff: "Die drei alten Alternativen stehen im Muster zeichengleich; angehaengt sind genau
            zwei: Z\\.[0-9]+ und Zeile [0-9]+. Der Auftrag nennt DREI Formen — er loest sie mit
            ZWEI Alternativen und begruendet es im Kommentar: 'Z.217-268 beginnt mit Z.217 und
            ist damit mit abgedeckt'. Selbst nachgefahren: die Bindestrich-Form schweigt."
  B5N-4: GRUEN
    beleg: "scripts/commit-pruefen.sh +11/-1 — die EINE geloeschte Zeile ist die alte Fassung
            von B5_BELEGZEILE, ersetzt durch dieselbe plus zwei Alternativen. Die uebrigen
            zehn Zeilen sind der Begruendungskommentar."
    die_anderen_barrieren: "B5_ZAEHLWORT, B6_SUMMENWORT, B6_MENGE, B7_MEHRFACH und B7_HERKUNFT
            sind an Elter und Bau je IDENTISCH (einzeln gehasht). Und die Gegenprobe am
            laufenden Tor: eine Botschaft mit Zaehlwort, Summe und vier Fundorten loest
            B5=1, B6=1, B7=1 aus — alle drei feuern weiter."
    scripts_suite: "Bau 107/107/0, Elter 107/107/0. resources/ und app/ 0 Dateien."
  B5N-5: GRUEN
    gemessen_statt_angenommen: "Das Kriterium sagt: 'Ob es sie gibt, ist zu MESSEN und im
            Bericht zu sagen, nicht anzunehmen.' Ich habe selbst gemessen:
              grep -rl 'B5|BELEGZEILE|ZAEHLWORT' scripts/__tests__/  -> 0 Dateien
            Es gibt keine Stelle, an der B5s Muster geprueft wird. Der Bau hat die Luecke
            also nicht vergroessern koennen — und hat scripts/__tests__ mit 0 Dateien
            unberuehrt gelassen. Die Luecke bleibt BENANNT und offen, wie das Kriterium es
            verlangt: eine Barriere ohne Test ist eine Behauptung ueber sich selbst."
  B5N-6: GRUEN — und diesmal stimmt die Reihenfolge vollstaendig
    beleg: "2766e0ac (11:01:40) setzt BEIDE Orte und nennt beide Zahlen: 'Tafelzeile 0,
            Zustandsfeld 0. Danach 1 und 1 auf B5N.' Bau 11:06:51, also fuenf Minuten spaeter."
    selbst_gegengemessen: "Am Elter des Baus steht B5N an beiden Orten auf IN_ARBEIT
            (Tafel 1 / Datensatz 1). Damit ist dies der EINZIGE der vier heute geprueften
            Bauten, bei dem der Zustand vor dem Bau im committeten Stand steht — bei W-01N,
            W-15/1 und B7 fehlte er."

sein_selbstbefund_gegengeprueft:
  was_er_meldet: "Der Sonden-Commit 27ca84a5 ('TIME_VARS steht an vier Fundorten') ist SEINER:
        beim B7-Bau lief seine Rueckgabewert-Messung im falschen Verzeichnis, und weil
        scripts/commit-pruefen.sh hier genauso liegt wie im Wegwerf-Repo, hat die Sonde das
        ECHTE Tor bedient. Zurueckgenommen mit git revert -> 9df633c2."
  und_der_werkzeug_befund: "Er sagt: der Revert ging NICHT durch das Tor, weil das Tor keine
        Loeschung verbuchen kann — Stufe 4 weist einen fehlenden Pfad mit 'FEHLT' ab.
        SELBST NACHGEFAHREN im Wegwerf-Repo: Datei angelegt und committet, dann geloescht und
        dieselbe Datei als Pfad uebergeben:
          FEHLT      docs/weg.md
          KEIN COMMIT. F-14: was nicht geschrieben wurde, wird auch nicht belegt.
        Sein Befund stimmt. Wer im Tor eine Datei entfernen will, kommt strukturell nicht
        durch. Das gehoert dem Planner, nicht diesem Bau — er hat es richtig weitergereicht."

was_diesen_bau_heraushebt:
  - "Er hat seinen EIGENEN Fehler aus der Vorrunde nachgetragen, obwohl ihn ein anderer
     gefunden hat, und dabei praezisiert, was er beim ersten Mal uebersehen hatte: gemeldet
     war die Streudatei, uebersehen der Commit."
  - "Die Begruendung steht im CODE, nicht nur im Bericht — zehn Kommentarzeilen ueber der
     einen geaenderten Zeile, mit der Zahl (9 von 40 Botschaften) und dem Grund, warum Z.
     keine Randform ist."
  - "Er macht die Barriere LEISER und sagt es so: 'B5_ZAEHLWORT bleibt unberuehrt.' Eine
     Nachbesserung, die den Ausloeser statt der Ausnahme entschaerft haette, waere eine
     Abschaltung mit anderem Namen gewesen."

zu_meinem_eigenen_befund:
  meine_zahl: "Ich hatte in der B5-Abnahme gemessen: von 17 warnenden Botschaften trugen VIER
        eine Fundstelle in nicht erkannter Form."
  seine_zahl: "Er misst ueber die letzten 40 Botschaften: NEUN tragen die Form Z.NNN, davon nur
        ZWEI zusaetzlich eine erkannte Form — also sieben zu Unrecht gewarnte."
  kein_widerspruch: "Verschiedene Mengen (17 warnende gegen 40 Botschaften) und verschiedene
        Formen (meine vier umfassten auch 'treppenTypen:4' und 'Zeile 39'). Beide Messungen
        zeigen dieselbe Richtung; seine ist die schaerfere fuer die Form Z.NNN."

zusammenfassung: "Sechs von sechs. Die Nachbesserung meines eigenen Befunds ist sauber gebaut:
     drei Formen einzeln belegt, die Barriere bleibt bei fehlendem Beleg laut, die drei alten
     Formen und die drei anderen Barrieren sind nachweislich unberuehrt — und die Testluecke
     ist gemessen statt angenommen und bleibt als eigener Vorgang offen. Der einzige Bau des
     Tages, bei dem der IN_ARBEIT-Zustand im committeten Stand vor dem Bau steht."

ballbesitz: release-pruefer
```
