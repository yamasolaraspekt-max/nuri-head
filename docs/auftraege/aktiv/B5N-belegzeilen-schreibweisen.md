# B5N — Die Barriere schlägt bei richtiger Arbeit an. Dreimal gemeldet, jetzt ein Auftrag statt einer vierten Meldung

```yaml
auftrag: "B5N"
art: "Nachbesserung nach §12.5 — B5 bleibt RELEASE_FREI, der Befund wirkt nicht rueckwirkend"
titel: "B5_BELEGZEILE erkennt nur datei.ext:zeile — die Schreibweise Z.217-268 faellt durch"
spur: A
heimat_app: ticket
status: ENTWURF
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
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
umfang: "EINE Zeile im Tor plus drei Gegenproben. Kleiner als B5, B6 und B7."
warum_nicht_still_erweitert: "das Tor ist eine Schranke, die allen Rollen gehoert. Der Generator
       hat am 12.08. genau hier die Linie gezogen und das §3-Muster NICHT eigenmaechtig geaendert,
       obwohl er es in der Hand hatte. Dieselbe Linie gilt fuer B5s Muster."
zweiter_posten_offen: "B5N-5 — Barrieren ohne Test. Betrifft B5 UND B6, eigener Vorgang."
```
