# W-13 · Auswahl und Griffe — PRÜFUNG

## Die Absicherung ist DÜNN — mit Messweise, damit die Zahl nachrechenbar ist

**Messweise:** *dediziert* = eine Testdatei in `resources/planner/hausplaner/__tests__/`, deren
**Dateiname mit dem Modulnamen beginnt**. *Erwähnend* = eine Testdatei, die das Modul **importiert**.

```text
DEDIZIERT   auswahlModus 0 · auswahlDarstellung 0 · auswahlUebersicht 0 · trefferSuche 0
ERWAEHNEND  zwei Dateien:  __tests__/markieren.test.ts
                           __tests__/teilKennung.test.ts   (36 Zusagen zusammen)
VERGLEICH   W-01 fangKern 2 dedizierte · W-02 wallGeometry 1 dedizierte
```

**Null dedizierte Zusagen bei 321 Zeilen und 18 Ausfuhren.** *Eine Beschreibung, die eine dünne
Grundlage verschweigt, lässt Stufe 2 in eine Falle laufen.*

## Was eine Prüfung hier belegen muss

1. **Den Modifikator-Vorrang** — `Alt`+`Shift` ergibt `remove`, nicht `add`. Eine Kette von vier
   `if` ist genau die Stelle, an der eine Umsortierung unbemerkt bleibt.
2. **Dass „oben" vor „nah" gewinnt.** Ein Test mit **gleicher** Distanz und verschiedener
   Zeichenreihenfolge — sonst prüft man die Distanz und hält es für die Sortierung.
3. **Dass der Primärstand beim Entfernen nachrückt** — und nur dann.
4. **Dass ein Klick ins Leere mit Modifikator NICHTS tut.**
5. **Dass `waehlbar === undefined` als wählbar gilt** — der Code prüft `!== false`, nicht `=== true`.
   *Der Unterschied entscheidet über jedes Objekt, das das Feld nicht setzt.*

## Was ich NICHT geprüft habe

**Was die 36 Zusagen der zwei erwähnenden Dateien tatsächlich abdecken.** Ich habe gezählt, welche
Dateien importieren — **nicht**, welche der fünf Punkte oben darin schon stehen. *Als Frage notiert.*
