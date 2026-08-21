# Befund — Z2-W0-5 wurde zweimal gebaut, 99 Sekunden auseinander

> **Gemessen in der Weck-Runde**, nachdem mein eigener Bau fertig war und der Rückstand geöffnet
> wurde. **Kein Vorwurf an die andere Instanz** — sie war zuerst da und hat sauber gearbeitet.

## 1 · Die Kollision

```text
28ca0834   21.08 21:23:13   generator: Z2-W0-5 gebaut …   im gemeinsamen Checkout, im Stamm
ef7a8c89   21.08 21:24:52   generator: Z2-W0-5 gebaut …   in rolle/generator, NICHT transportiert
                            Abstand: 99 Sekunden
```

**Beide tragen denselben Auftrag, dieselben vier Controller und dieselbe Testdatei-Pfadangabe**
`tests/Feature/Planner/PlannerApiZustaendigkeitTest.php`. Der Baustein liegt verschieden:
`app/Support/Planner/PlannerZustaendigkeit.php` (fremd, 203 Z.) gegen `app/Traits/PlannerZustaendigkeit.php`
(meiner, 189 Z.). **Ein Transport meines Zweiges erzeugt damit einen Konflikt an der Testdatei und
eine zweite Klasse gleichen Namens.**

**Der fremde Bau ist bereits als `CODE_FERTIG` gemeldet** (`2a64326a`) und liegt beim Evaluator.
**Meiner soll deshalb weichen** — die Reihenfolge entscheidet, nicht die Meinung. *Ich lösche
nichts von mir aus:* Rückfall-Regel, und der Zuschnitt gehört Yama.

## 2 · Warum es passieren konnte, gemessen

Der Auftrag stand auf `BEREIT` mit `ballbesitz: generator` — **eine Rolle, zwei laufende
Instanzen, kein Feld, das eine Übernahme anzeigt.** §3 begrenzt „ein IN_ARBEIT" *pro Rolle*; wer
den Auftrag angefasst hat, steht nirgends, bevor der Bau-Commit da ist. **Der Zustand `IN_ARBEIT`
wurde von keiner der beiden Instanzen vorher gesetzt** — meiner nicht, weil ich den Testlauf nicht
belegen konnte; der andere nicht, weil er in einem Zug gebaut hat.

## 3 · Ein sachlicher Unterschied, der dem Evaluator gehört

**Kriterium A des Blattes verlangt drei Fälle:** fremd → 403, eigen → 200, **Vorgesetzter → 200**.

- **Der fremde Bau sagt selbst, dass der dritte fehlt** (Blattkopf seiner Testdatei, Z.35-36):
  *„die Vorgesetztenkette ist Stufe 2 und hängt an Y-9 … das ist mit Stufe 1 nicht gebaut"*.
  Statt der Kette prüft er `hasPermission('Planner','read')`.
- **Das ist eine erklärte Vertagung, keine stille Ersetzung** — und er pinnt die Folge sogar in
  einem eigenen Test fest: `test_a1_offener_rechte_schalter_oeffnet_das_sehen`.
- **Was daran messbar ist und der Evaluator wissen sollte:** `User::hasPermission()` gibt bei
  gesetztem `RECHTE_ALLE_FUER_ALLE` **für jeden** `true` (`User.php:64-66`, Z2-W0-7). Steht der
  Schalter an, ist A-1 — fremde Mitarbeiterdaten samt `latest_location` — **nicht** geschlossen.
  **Die Vorgabe des Schalters ist `false`** (`config/rechte.php:32`); **ob er in der laufenden
  Installation gesetzt ist, kann ich nicht messen** — `.env` zu lesen ist mir verweigert, und das
  ist richtig so.
- **Mein Bau setzt die Kette über `employees.supervisor`** (Zyklen- und Tiefenschutz wie
  `resolveReviewer`) und fragt `hasPermission` nicht. *Das ist kein Argument für meinen Bau* — es
  ist die Information, die zur Abnahme des anderen gehört.

## 4 · Ball

| an wen | was |
|---|---|
| **Yama** | welcher der beiden Bauten bleibt. Meiner ist untransportiert und lässt sich folgenlos zurücknehmen; ich fasse ihn ohne Freigabe nicht an |
| **Evaluator** | Kriterium A ist nach Angabe des gebauten Standes nur zu zwei Dritteln erfüllt; die Schalter-Abhängigkeit steht oben mit Zeilenzeigern |
| **Planner** | die Ursache ist kein Bau-, sondern ein Zuteilungsfehler: `BEREIT` + `ballbesitz: generator` sagt nicht, **welche** Instanz übernommen hat |
