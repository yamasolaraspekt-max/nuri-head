# Warum wir oberflächlich arbeiten — und was daran mechanisch zu ändern ist

*Planner, 30.07.2026, 07:10 CEST. Auf Yamas Frage: „euch fehlt das Gefühl des sorgfältigen
Handelns sowie gewissenhaft lesen, bewerten und nachdenken — wie kann man die Schwäche von dir,
Generator und Evaluator beheben."*

> **Dieses Papier ist absichtlich kurz. Das ist der erste Teil der Antwort.**

---

## 1. Die Messung, die alles erklärt

```text
R10 bis R18 — neun Regeln, alle heute entstanden:
  Vorkommen im Regelwerk (docs/agents/00-REGELWERK.md):   0 · 0 · 0 · 0 · 0 · 0 · 0 · 0 · 0
  Vorkommen im Ledger (21 233 Zeilen):                    6 · 5 · 10 · 1 · 2 · 1 · 4 · 6 · 15
```

**Keine einzige der neun Regeln, die ich heute aufgestellt habe, steht im Regelwerk.** Sie stehen
in einem Ledger mit 21 233 Zeilen und in einer Tabelle.

**Niemand liest 21 233 Zeilen.** Wir schreiben Regeln an einen Ort, an dem sie niemand findet, und
schließen aus ihrer Wirkungslosigkeit auf mangelnde Sorgfalt.

## 2. Die zweite Messung: was ich heute Vormittag produziert habe

```text
1 139 Zeilen Auftragstext in vier Blättern — für drei baubare Aufträge.
Davon zurückgewiesen: zwei, beide vom Generator, beide zu Recht.
```

**Ich erzeuge Text schneller als Wissen.** Ein gut geschriebener Auftrag *fühlt sich an* wie
Sorgfalt und *liest sich* wie Sorgfalt. **Ein schön formulierter Auftrag auf einer vier Tage alten
Messung ist schlechter als ein grober auf einer frischen** — weil er zum Glauben einlädt.

*Das ist keine Faulheit. Es ist Flüssigkeit, und sie ist gefährlicher als Faulheit, weil sie
aussieht wie Fleiß.*

## 3. Der Beleg, dass Vorsätze nicht wirken und Barrieren wirken

Vierzehn Fehlerklassen, ein Tag. **Sortiert nach dem, was aus ihnen wurde:**

| Klasse | Antwort | Wiederholungen danach |
|---|---|---|
| F-05 Beifang im Index | **Barriere** — `git commit -- <pfade>` | **0** |
| F-01 Suche nach Muster | **Barriere** — die Liste der Testdateien, nicht das Muster | **0** — und sie hat heute den AUF-70-Konflikt gefunden |
| F-13 fehlender Vorher-Wert | **Barriere** — `vorher_wert_pflicht` in der Quittung | **0**, und sie hat sofort eine Sperre verkürzt |
| F-03 Messung älter als der Baum | *Regel* | **4×** |
| F-04 Zahl behauptet statt gemessen | *Regel* | **5×** |
| F-07 Bestand nachgebaut statt gemessen | *Regel* | **5×** |

**Das Muster ist eindeutig und braucht keine Interpretation: was mechanisch wurde, hörte auf.
Was Vorsatz blieb, wiederholte sich vier- bis fünfmal — an einem einzigen Tag.**

---

## 4. Was jede der drei Rollen wirklich falsch macht

### Planner — die schwächste der drei, und das bin ich

**Ich schreibe aus dem Entwurf statt aus dem Baum.** Alle drei Rückweisungen heute hatten dieselbe
Ursache: ich habe beschrieben, was gebaut werden *soll*, ohne zu messen, was schon *steht*.
Viermal war ein Kriterium längst erfüllt. Einmal hätte mein Kriterium vier abgenommene Zusagen
zurückgedreht.

> **Die mechanische Antwort ist nicht „sorgfältiger messen", sondern: der Auftrag darf keine Zahl
> mehr enthalten.**
> `population_at_writing` wird aus dem Schema **gestrichen**, nicht besser ausgefüllt. Ein Auftrag
> nennt den **Befehl**. Wer die Zahl braucht, fährt ihn.
> *Warum das trägt: der Bauende bezahlt eine falsche Zahl sofort, ich nie. Die Messung gehört zu
> dem, der die Rechnung bekommt.*

### Generator — heute der Stärkste, mit einer scharf umrissenen Schwäche

Er hat zwei Aufträge zurückgewiesen, vier Kriterien als bereits erfüllt gemeldet statt sie
abzuhaken, den AUF-70-Konflikt gefunden und sein eigenes Bündel zurückgestellt, bevor es jemand
merkte. **Das ist keine schwache Arbeit.**

**Seine Schwäche hat einen Namen und drei Ausprägungen heute:** *die Datei war grün, der Inhalt
war falsch.* Ein unangeführtes Heredoc ließ die Shell fünf Namen verschlucken. Ein Bündel wurde
gebaut, während eine Sichtprobe beauftragt war.

> **Mechanisch: nach jedem erzeugenden Befehl wird das ERGEBNIS gelesen, nie der Exitcode.**
> `grep` auf das, was drinstehen soll — nicht der Rückgabewert.

### Evaluator — der schärfste Blick, das größte Risiko

Er hat gemessen, was ihm niemand aufgetragen hatte, und **seine eigene Gegenprobe widerlegt**:
*„Meine Mutation war unwirksam, nicht der Test blind."* Das ist die seltenste Eigenschaft von
allen.

**Zwei Schwächen, beide von ihm selbst benannt:** zwei Mutationen waren so grob, dass die Datei
nicht mehr lud — *ein Rot, das nichts beweist.* Und: **sein eiliger Auftrag liegt seit 35 Minuten
unbearbeitet**, während der Generator darauf wartet.

> **Mechanisch: eine Mutation gilt erst als Gegenprobe, wenn die Datei danach noch LÄDT.**
> Und: **ein eiliger Auftrag bekommt eine Empfangszeile, keine Stille.** *Wer nichts hört, kann
> nicht umplanen.*

---

## 5. Die eine gemeinsame Ursache

**Wir alle drei suchen, statt zu lesen.**

Eine Suche liefert in einer Sekunde, was Lesen in zehn Minuten liefert — und sie liefert **nur,
wonach man schon gefragt hat.** Jede der vier größten Fehlerklassen heute ist eine Variante davon:
ein Muster gesucht statt einer Menge · eine Zahl übernommen statt gemessen · einen Entwurf gelesen
statt des Codes · eine Zeichenkette geprüft statt einer Wirkung.

> **Im Zweifel lesen, nicht suchen.** Und wo das zu teuer ist: **die Menge aufzählen lassen, nicht
> das Muster raten.** Das ist R12, und sie ist die einzige Regel von heute, die zweimal etwas
> gefunden hat, das niemand gesucht hatte.

---

## 6. Die sechs Änderungen — und was ich dafür aufgebe

1. **Keine Zahl mehr im Auftrag.** `population_at_writing` fällt aus dem Schema. Nur Befehle.
2. **`scripts/auftrag-pruefen.sh` (AUF-87) wird der nächste gebaute Auftrag**, nicht der
   übernächste. Er macht aus „hast du gemessen?" einen Befehl, der fehlschlägt.
3. **Alle Regeln wandern ins Regelwerk** — R10 bis R18, heute noch. *Eine Regel, die nur im Ledger
   steht, existiert nicht.*
4. **Höchstens ein neues Auftragsblatt pro Stunde.** Heute waren es vier in drei Stunden, mit
   zwei Rückweisungen. **Weniger Blätter, gemessene Blätter.**
5. **Jedes Kriterium muss mit EINEM Befehl prüfbar sein.** Was das nicht ist, ist ein Wunsch und
   wird als Wunsch gekennzeichnet, nicht als Kriterium.
6. **Nach jedem erzeugenden Befehl das Ergebnis lesen, nie den Exitcode.** Gilt für alle drei.

---

## 7. Und eine unbequeme Bitte an Yama

**Der 3-Minuten-Takt arbeitet gegen die Sorgfalt.**

Er belohnt, alle drei Minuten *etwas* vorzuweisen — und genau dieser Druck erzeugt den Text aus
dem Gedächtnis statt der Messung aus dem Baum. **Vier der fünf F-04-Ausprägungen sind heute im
Takt entstanden.**

**Der Takt ist als Wache richtig und als Arbeitsrhythmus falsch.** Mein Vorschlag: er bleibt für
das **Lesen** (git log, Ledger, Meldungen aufnehmen) und er hört auf, ein Blatt zu verlangen.
**Wachen alle drei Minuten, schreiben höchstens einmal pro Stunde.**

*Ich sage das, obwohl der Takt von dir kommt — weil er sonst genau die Schwäche verstärkt, nach
der du gefragt hast.*
