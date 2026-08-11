# Entscheidung — Doku-Stufen bekommen keine eigene Release-Station, sondern eine SAMMEL-Kontrolle

```yaml
art: "Planner-Entscheidung. Die Frage wurde mir zweimal ausdruecklich zugewiesen."
entschieden_am: "12.08."
basis_sha: 4f0d4584
zugewiesen_durch: "release-pruefer 01150cd1 ('weil sie dem Planner gehoert') und
                   plan-pruefer a1d29aed (Wuerdigung derselben Antwort)"
grundlage: "eine gemessene Zahl, keine Meinung — 6 von 11 §10-Punkten griffen"
umsetzung: "diese Entscheidung AENDERT §10 NICHT. Die Aufnahme in ARBEITSREGELN ist
            ein eigener Schritt und gehoert Yama oder einer Prozesspruefung nach §13."
```

## Die Frage

**Braucht eine Doku-Stufe (`W-xx/1 BESCHRIEBEN`) eine eigene Release-Prüfung nach §10 —
oder ist die Station dort leer?**

## Die Zahl, die der Release-Prüfer selbst gemessen hat

```text
von den ELF §10-Punkten griffen bei den drei Doku-Stufen (W-04, W-05, W-11):
  GEGRIFFEN  6   Votum-SHA · Release-Diff · Qualitaetstor · Rueckweg ·
                 Sicherheits-/Datengrenzen · offene P0/P1
  LEER       5   mangels Gegenstand nicht anwendbar
```

**Und sein eigener Einwand gegen die naheliegende Folgerung, wörtlich:**

> *„Sechs von elf ist wenig für eine eigene Station — **aber der einzige Fund der Runde liegt in den
> sechs, und keine andere Rolle hatte ihn**."*

## Der Fund, der die Entscheidung trägt

**Er hat gezählt, was vor ihm niemand gezählt hat:** der W-04-Messtisch des Evaluators trug
**sieben von zehn** Kriterienzeilen. `-2`, `-3` und `-4` fehlten — alle drei P1, und `-4` ist laut
Auftrag der Kern.

**Der Evaluator hat den Befund angenommen** (`fd076dc5`) und dabei den Satz geschrieben, der die
Lücke benennt:

> *„Mein Kopf sagte ‚alle zehn erfüllt' und mein eigener Messtisch trug das nicht. **Genau die
> Klasse, die ich anderen vorhalte** — hier hat sie mich getroffen, und gefunden hat es die Stufe
> nach mir."*

> **Das ist keine Nachlässigkeit einer Instanz, sondern eine strukturelle Lücke:** *ein Evaluator
> prüft **den Bau** gegen die Kriterien. **Ob sein eigener Bericht jede Kriterienzeile trägt, prüft
> in der ganzen Kette niemand — außer der Stufe nach ihm.*** Und die ist die Release-Prüfung.

## Der HEBEL war nicht die Station, sondern die SAMMELFORM

```text
Release-Pruefer 01150cd1, woertlich:
  "Die drei Auftraege unterscheiden sich an genau EINER Stelle, die nur im VERGLEICH
   auffaellt (W-11 und W-05 belegen -1 bis -10 einzeln, W-04 sieben von zehn) —
   einer Einzelpruefung haette der MASSSTAB gefehlt."
```

> **Das ist der eigentliche Befund, und er entscheidet die Frage.** *Der Fund kam nicht daraus, dass
> §10 auf W-04 angewandt wurde. Er kam daraus, dass **drei Berichte nebeneinander lagen**. Bei einer
> Einzelprüfung wäre „sieben Zeilen" eine Zahl ohne Vergleich gewesen; erst neben zwei Berichten mit
> zehn wurde sie ein Befund.*

**Und der Plan-Prüfer hat dieselbe Klasse an sich selbst gefunden** (`a1d29aed`): *„die einseitige
`must_preserve`-Messung war KEINE W-04-Besonderheit, sondern symmetrisch — ich hatte zugeschrieben
ohne Vergleichsmessung."* **Klasse: richtige Einzelmessung, zu weite Aussage.** *Zweimal dieselbe
Ursache an einem Tag: **eine Messung ohne Vergleich erzeugt eine Aussage, die weiter reicht als der
Beleg.***

## ENTSCHEIDUNG

```text
1  KEINE eigene Release-Station je Doku-Stufe.
   Fuenf von elf Punkten sind leer; eine Station, die zu knapp der Haelfte greift,
   wird zur Formsache und dann uebersprungen. Das ist an A-03 belegt (ein Riegel,
   der am falschen Gegenstand haengt, wird umgangen).

2  STATTDESSEN: eine SAMMEL-KONTROLLE, wenn MEHRERE Doku-Stufen abgenommen sind.
   Gegenstand ist NICHT der Bau — der ist geprueft. Gegenstand sind die BERICHTE
   im Vergleich. Auslesbar sind genau die sechs Punkte, die gegriffen haben.

3  DER AUSLOESER IST EINE ZAHL, KEIN GEFUEHL: ab DREI abgenommenen Doku-Stufen
   ohne dazwischenliegende Sammel-Kontrolle. Drei ist die Zahl, bei der ein
   Vergleich einen Massstab hat — bei zwei ist unklar, welcher der beiden abweicht.

4  DIE EINE PFLICHTFRAGE der Sammel-Kontrolle, und sie ist die, die den Fund brachte:
   "Traegt jeder Messtisch JEDE Kriterienzeile seines Auftrags — gezaehlt, nicht
   ueberflogen?"  Antwortform: Zahl je Bericht (7/10, 10/10, 10/10) plus die
   fehlenden Nummern. B5 gilt: die Zahl allein reicht nicht, die fehlenden
   Zeilen werden GENANNT.

5  WER: der Release-Pruefer, weil er die Stufe nach dem Evaluator ist und die
   Rollentrennung es verlangt — ein Evaluator kann seinen eigenen Messtisch nicht
   nachzaehlen, das ist dieselbe Betriebsblindheit, gegen die der ganze Zyklus gebaut ist.

6  NICHT GEAENDERT: §10 selbst. Diese Entscheidung ist eine Planner-Festlegung ueber
   die ANWENDUNG, keine Regelaenderung. Die Aufnahme in ARBEITSREGELN gehoert Yama
   oder einer Prozesspruefung nach §13 — ich lege sie vor, ich schreibe sie nicht ein.
```

## Was ich ausdrücklich NICHT entscheide

```text
- Ob die fuenf leeren §10-Punkte bei Doku-Stufen dauerhaft leer BLEIBEN. Sie waren
  bei DIESEN drei Auftraegen leer. Ein Doku-Auftrag, der ein Bundle oder eine
  Migration beruehrt, ist kein Doku-Auftrag mehr — dann gilt §10 voll.
- Die Zahl DREI als endgueltigen Ausloeser. Sie ist begruendet (ein Vergleich
  braucht einen Massstab), aber nicht gemessen. Wenn sich zwei als ausreichend
  zeigen, gehoert sie gesenkt.
- Ob die Sammel-Kontrolle eine eigene ZUSTANDSSTUFE braucht. Ich halte nein —
  sie ist eine Handlung, kein Zustand. Aber das beruehrt §3, und §3 gehoert
  nicht mir.
```

## Was diese Entscheidung dem Release-Prüfer schuldet

**Er hat die Frage nicht mit einer Meinung beantwortet, sondern mit `6 von 11` — und dabei gegen
sein eigenes Interesse argumentiert.** *Die bequeme Antwort für eine Station wäre „§10 greift, die
Station ist nötig".* **Er hat die Zahl genannt, die seine eigene Station in Frage stellt, und
daneben den Fund gestellt, der sie rechtfertigt.** *Das ist die Form, in der ein Vorschlag
belastbar ist: er trägt sein Gegenargument mit.*

```yaml
entschieden: "keine eigene Station · Sammel-Kontrolle ab drei · eine Pflichtfrage ·
              Ausfuehrender ist der Release-Pruefer"
offen_an_yama: "die Aufnahme in ARBEITSREGELN §10 — Regelaenderung, gehoert nicht dem Planner"
kern: "eine Messung ohne Vergleich erzeugt eine Aussage, die weiter reicht als der Beleg.
       Zweimal an einem Tag gefunden, von zwei verschiedenen Rollen, an sich selbst."
belegt_durch: "01150cd1 (6 von 11 + der Fund) · fd076dc5 (Annahme des Befunds) ·
               a1d29aed (dieselbe Klasse an sich selbst)"
```
