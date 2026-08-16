# Drei Yama-Posten sind sachlich erledigt — schließen kann sie heute niemand

> **Release-Prüfer, 16.08. ~20:1x.** Frisch gemessen, nicht aus Notizen. Dieses Blatt existiert,
> weil die Ballrückgabe durch `docs/STATUS.md` läuft und die Datei seit 19:5x gesperrt ist.

## Die vier Posten bei Yama, Stand jetzt

```
P-07     Rueckweg zu           ERLEDIGT   Rueckstand 106/33/32 -> je 1
P-09     veralteter Zweig      ERLEDIGT   fehlende Verzeichnisse: 0 in allen fuenf Baeumen
REGISTER Bauvorrat 30 gegen 37 ERLEDIGT   Reifegrad gegen Blaetter: 0 Abweichungen bei 43 Zeilen
die_sicherung_steht_ab         HALB OFFEN unveraendert — die Push-Regel ist eine Vollmachtsfrage
```

## Die Belege, je Posten

### P-07 — „94 Commits erreichen den Planner nicht"

```
Rueckstand hinter rolle/plan-pruefer:
   bei der Meldung (18:49)   planner 106 · generator 33 · evaluator 32
   jetzt                     planner   1 · generator  1 · evaluator  1
```

Der verbleibende Commit ist jeweils sein **aktuellster**, aus derselben Minute — laufender Betrieb,
kein Rückstand. Der Rückweg ist offen und wird in jedem Takt nachgefahren.

### P-09 — „ein veralteter Zweig hat drei Fehlbefunde erzeugt"

```
fehlende Werkzeugverzeichnisse (W-25 · W-26 · W-28 · W-29 · W-30 · W-43):
   ticket 0 · planner 0 · plan-pruefer 0 · generator 0 · evaluator 0
```

Die Quelle ist versiegt. Kein Baum kann diese sechs mehr als „nicht vorhanden" messen.

### REGISTER — „der Bauvorrat ist 30 und nicht 37"

```
43 Registerzeilen, Reifegrad gegen tatsaechliche Blaetter geprueft:  0 Abweichungen
```

Die vier Zeilen, die ich um 19:2x als überholt gemeldet hatte (W-43, W-26, W-28, W-30), tragen
inzwischen den richtigen Reifegrad. Damit ist auch die Zahlendifferenz zwischen den drei Rollen
aufgelöst — sie kam aus verschiedenen Zweigständen, nicht aus verschiedenen Zählweisen.

## Warum sie trotzdem offen stehen

**Ein Posten wird geschlossen, indem der Ballbesitz im Datensatz zurückgegeben wird — und das ist
eine Änderung an `docs/STATUS.md`.** Seit die Sperre um 19:5x gezündet hat, darf das nur der
Integrator, und der ist nicht `SCHREIBEND`.

**Das ist der erste messbare Preis der Blockade.** Bisher konnte ich sagen, sie koste nichts, weil
kein `ABGENOMMEN` wartet. Das gilt weiter für Releases — aber **drei erledigte Posten stehen jetzt
sichtbar offen, obwohl sie es nicht sind.** Wer die Statuswahrheit heute liest, sieht bei Yama vier
Vorgänge, von denen drei nur noch aus einer Zeile bestehen, die niemand schreiben darf.

## Was das für die Reihenfolge heißt

Es ändert **nichts an meiner Empfehlung** und begründet keine Eile. Der Schaden ist Sichtbarkeit,
nicht Verlust: die Belege stehen hier, die Messungen sind wiederholbar, nichts geht verloren.

**Sobald Schritt J erteilt ist**, sind die drei Rückgaben ein Handgriff für den Integrator — mit
diesem Blatt als Beleg, damit er nicht neu messen muss. Bis dahin stehen sie offen, und das ist
korrekt so: **ein Posten, den niemand schließen darf, wird nicht heimlich als geschlossen
behandelt.**
