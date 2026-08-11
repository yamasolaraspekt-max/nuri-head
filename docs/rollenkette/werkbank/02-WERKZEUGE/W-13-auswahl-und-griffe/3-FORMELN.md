# W-13 · Auswahl und Griffe — FORMELN

## Keine F-Nummer trifft zu — und das Register sagt es bereits

Die Registerzeile führt **„keine"** und hat F-012 und F-003 gestrichen. **Diese Messung bestätigt
das und korrigiert nichts** — sie fügt hinzu, *was stattdessen* gerechnet wird.

**In allen vier Modulen wird nur eine einzige Sache gerechnet**, und die hat in der Sammlung keine
Nummer:

```text
trefferSuche.ts:72-74   toleranzInWelt(pixel, zoom) = zoom > 0 ? pixel / zoom : pixel
```

**Eine Einheitenumrechnung**, keine Geometrieformel. *Sie in die Sammlung aufzunehmen, wäre kein
Gewinn: eine Division ist keine Formel, die man nachschlägt.*

## Der Befund statt eines Eintrags

**Das ist eine Rechnung ohne Nummer** — nach Kriterium `W-13/1-3` als **Befund** zu melden und
**nicht** einzutragen. Hiermit gemeldet.

**Bemerkenswert an ihr ist nicht die Formel, sondern der Grenzfall:** *„ein Zoom von 0 oder kleiner
liefert die Toleranz unverändert, statt durch null zu teilen"* (Z.73-74). **Die Absage ist gebaut,
nicht vergessen.**

## Was hier ausdrücklich NICHT gerechnet wird

**Abstände.** `TrefferKandidat` bringt seine `distanz` **mit** — das Modul misst sie nicht, es
sortiert danach. *Wer eine Abstandsformel sucht, findet sie bei W-01 (F-001), nicht hier.*
