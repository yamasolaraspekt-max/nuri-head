# SKILL · Eine Machbarkeitsfrage beantworten

**Wann:** Im Raum steht ein Satz wie „das Dach soll auch L-förmig gehen",
„die Wand muss sich krümmen lassen", „das müsste die Domäne können".

> **Dieser Skill existiert wegen eines konkreten Verlusts.** Ein Auftrag verlangte
> ein L-förmiges Dach mit 68 m². Die Behauptung, das sei machbar, war nie gemessen.
> Die Domäne hatte es nie gekonnt. Zwei Runden verloren.

---

## Die Regel

**Niemals schätzen. Immer messen.** Eine Machbarkeitsaussage ohne Messung ist
eine Vermutung, und Vermutungen werden in Aufträgen zu Fehlern.

---

## Der Ablauf

### 1 · Die Frage in eine Messung übersetzen

| Vage Frage | Messbare Frage |
|---|---|
| „geht ein L-Dach?" | „Wirft `dachGeometrie` bei einer L-Kontur einen Fehler? Ja/nein, Zeilennummer" |
| „ist das schnell genug?" | „Wie viele Bilder/s bei 40 Wänden + Dach? Zahl" |
| „gibt es das schon?" | „Wie viele Treffer für `<Begriff>` im Repo? Zahl + Fundstellen" |

### 2 · Am echten Gegenstand messen, nicht am Gedächtnis

- **Code:** die Stelle lesen und die Zeilennummer notieren
- **Verhalten:** einen kleinen Lauf machen und die Ausgabe notieren
- **Menge:** zählen — nicht „mehrere", nicht „einige"

### 3 · Die Zahl mit Herkunft aufschreiben

```
Behauptung:  „die Domäne kann L-förmige Konturen"
Gemessen:    geometry/dachGeometrie.ts:87 wirft DachGeometrieUngueltig
             für JEDE nicht-rechteckige Kontur
Ergebnis:    Behauptung FALSCH
Folge:       Auftrag nicht schneidbar, bis Spalt-Ereignisse gebaut sind
```

### 4 · Wenn die Messung die Behauptung widerlegt

**Den Auftrag nicht schneiden.** Stattdessen:

- den Befund in `7-GRENZEN.md` des Werkzeugs eintragen
- den fehlenden Baustein als eigenen Auftrag benennen
- Yama den Unterschied zeigen: „gewünscht war X, möglich ist Y, dazwischen liegt Z"

---

## Was als Messung gilt — und was nicht

| Gilt | Gilt nicht |
|---|---|
| Zeilennummer im Code + zitierte Bedingung | „ich glaube, das steht in …" |
| Ausgabe eines tatsächlich gelaufenen Befehls | „normalerweise liefert das …" |
| gezählte Treffer mit Suchmuster | „ungefähr zwanzig" |
| Ergebnis eines Tests, der rot wurde | „der Test würde fehlschlagen" |

---

## Zwei eigene Fehlmessungen als Warnung

**Stichprobe ≠ Quote.** Eine Messung an 25 von 1739 Dateien ist eine Stichprobe.
Sie darf **nicht** hochgerechnet werden. Der Satz „24 von 25 waren harmlos" sagt
nichts darüber, wie viele der übrigen 1714 es sind.

**Zu kurz gesucht ist falsch gemessen.** Eine Suche über 40 Commits meldete eine
Datei als „nicht in der Historie". Die Datei hatte 567 Commits. Ohne den zweiten,
vollständigen Durchgang wäre ein Phantom-Fund gemeldet worden.

> Beide Fehler wurden von den Messenden **selbst offengelegt**. Das ist der
> Maßstab: Eine Messung, deren Grenzen man nicht mitnennt, ist keine Messung.

---

## Fertig-Probe

- [ ] Die Frage ist als Ja/Nein oder als Zahl formuliert
- [ ] Es gibt eine Fundstelle (Datei:Zeile) oder eine Befehlsausgabe
- [ ] Bei Stichproben steht dabei, dass es eine ist — und wie groß
- [ ] Das Ergebnis steht im Werkzeugordner, nicht nur in einer Nachricht
