# B7 — Mehrfachvorkommen ist kein Beleg. Und der **Ort** ist kein Beleg für die **Wirkung**

```yaml
auftrag: "B7"
titel: "Verbreitung sieht wie Bestaetigung aus. Barriere gegen die Zahl, die nur oft ist"
art: "BARRIERE — nach dem Muster von B5 und B6"
spur: A
heimat_app: ticket
status: ENTWURF
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

## Rückweg & Entdeckung — als eigene Zeile

```text
RUECKWEG      reiner Revert; B7 fuegt Text in §18a ein und ein Pruefmuster in das Werkzeug.
              Kein Datenpfad, kein Wert, keine Migration.
KOPIE AUSSERHALB DER MASCHINE  fork/main + backup-private/main.
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
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
abhaengigkeit: "B7-1 setzt voraus, dass §18a gegengelesen ist (offener Posten seit 12.08.)"
beleg_liegt_vor: "vier Fundorte, null Quellen — und der vierte hat die Schaerfung (b) geliefert"
```
