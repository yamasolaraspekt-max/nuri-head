# W-18 · Topologie prüfen — ZWECK

> ***EINORDNUNG: W-18 war eine ABLESUNG und kein Bau*** — *und das ist gemessen, nicht angenommen.*
> **Nach Yamas Verfahren für Klasse B gilt zuerst die Messung, dann die Einordnung**; hier steht sie,
> damit die nächste Rolle sie nicht wiederholt.

```text
F-013 (Selbstschnitt)  GEBAUT   geometry/kontur.ts:109  schneidetSichSelbst
                                acht Exporte, eigener Test
                                UND eine Nutzermeldung   :63
F-004 (Geradenschnitt) GEBAUT   ABER ANDERSWO und als GEHRUNGSDETAIL:
                                wallGeometry.ts:62 (Bandkanten bis zum
                                Schnittpunkt) und :106 — nicht als
                                Topologie-Formel
ANSCHLUSS              STEHT    HausplanerApp.tsx:31 fuehrt vier Symbole ein,
                                :30 nennt den Grund; pruefeKontur laeuft
                                auf :831
```

> ***Beide Formeln sind gebaut und die Prüfung ist angeschlossen — deshalb war hier nichts zu bauen,
> sondern zu lesen.*** *Was blieb, waren die Grenzen (`7-GRENZEN`): der dritte Topologie-Fall
> „Treppe ohne Zielgeschoss" ist **nicht** gebaut, und W-18 hat **kein eigenes Werkzeug** in der
> Registry — `'kontur'` dort (`toolRegistry.ts:230`) ist das **Zeichnen**-Werkzeug, nicht die
> Prüfung.*
>
> **Die Einordnung entstand aus dieser Messung und nicht umgekehrt** — *vor dem Öffnen des Codes
> stand offen, ob F-004 fehlt.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er soll keine Fläche zeichnen können, die es geometrisch nicht gibt** — und wenn er es versucht,
soll er im selben Moment lesen, **was** nicht stimmt und **was er tun kann**.

Drei Fälle, und für jeden steht ein Satz bereit (`kontur.ts:61-64`, wörtlich):

```text
zu-wenig-punkte   'Eine Fläche braucht mindestens drei Punkte — setze noch einen.'
selbstschnitt     'Die Kontur überschneidet sich selbst — zieh den letzten Punkt so,
                   dass sich keine zwei Kanten kreuzen.'
keine-flaeche     'Alle Punkte liegen auf einer Linie — das umschließt keine Fläche.'
```

> ***Jeder Satz nennt den Grund UND den nächsten Handgriff.*** *„Ungültige Kontur" wäre kürzer und
> nutzlos: der Anwender wüsste, dass etwas falsch ist, aber nicht, welcher Punkt.*

## Der tragende Punkt: die Prüfung ist eine VORprüfung, kein eigener Arbeitsschritt

**Sie wirkt beim Zeichnen.** *`HausplanerApp.tsx:31` führt vier Symbole aus `geometry/kontur` ein,
und `:30` nennt den Grund im Klartext:*

> *„Z-05: die Konturprüfung ist reine Geometrie und wohnt dort, nicht hier."*

**Es gibt kein Werkzeug „Topologie prüfen", das man anklickt** — und das ist kein Mangel, sondern
die Bauform. *Wer eine ungültige Kontur nie schließen kann, braucht keinen nachträglichen Befund.*
Siehe `7-GRENZEN`.

## Die Namensfalle, die dieses Blatt zuerst auflöst

**Es gibt zweimal „kontur", und es sind zwei verschiedene Dinge:**

| | was es ist |
|---|---|
| **`id: 'kontur'`** (`app/tools/toolRegistry.ts:230`) | das **Werkzeug zum ZEICHNEN** einer Kontur |
| **`geometry/kontur.ts`** | das **Modul zum PRÜFEN** einer Kontur |

> ***Gemessen, und der Unterschied ist keine Feinheit:*** *ein `import` auf `geometry/kontur` liefert
> **eine** Testdatei, das **Wort** „kontur" liefert **zwölf** — elf davon treffen die Werkzeug-ID.*
> **Wer die Wächter über das Wort zählt, schreibt „zwölf" hin und meint eine.**

## Wann greift der Anwender danach?

**Bei jedem Punkt, den er setzt.** *Die Prüfung läuft mit, während die Kontur entsteht — nicht am
Ende.*

## Woran merkt er, dass es fehlt?

**Er schlösse eine Kontur, die sich selbst überschneidet**, und das Dach oder der Raum daraus wäre
unsinnig — *ohne dass beim Zeichnen etwas gesagt hätte.* **Der Fehler fiele erst weit später auf,
an einer Stelle, die nichts dafür kann.**

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

- **Kein Gesamtbefund über den Grundriss.** *Ob der Anwender zusätzlich eine Übersicht über alle
  Topologiefehler braucht, ist eine Produktfrage — hier nicht entschieden.*
- **Keine Reparatur.** *Die Prüfung sagt, was nicht geht; sie rückt keinen Punkt.*
- **Keine Geschossbeziehungen.** *Der offene Posten „Treppe ohne Zielgeschoss" gehört hierher und
  ist NICHT gebaut — siehe `7-GRENZEN`.*
