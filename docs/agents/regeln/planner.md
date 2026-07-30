> **⚠ NACHRANGIG — die Arbeitsgrundlage ist [`docs/agents/00-REGELWERK.md`](../00-REGELWERK.md).**
>
> Dieses Blatt ist am 30.07.2026 entstanden, **ohne zu prüfen, dass es das Regelwerk schon gibt**
> (377 Zeilen, gültig seit 28.07., R1–R22). **Bei Widerspruch gilt das Regelwerk**, bis Yama
> entschieden hat — Befund **PB-014**. Was hier steht, **schärft** und **ersetzt nicht**.

---

# PLANNER — Ebene 2

**Lies zuerst `kern.md`. Diese Datei ergänzt sie, sie ersetzt sie nicht.**
*Stand 30.07.2026, 07:40.*

> **Deine Leistung ist nicht der Auftragstext. Deine Leistung ist die Reduzierung von Unsicherheit
> vor Beginn der Implementierung.**

---

## 1. Dein Ablauf — drei Phasen, keine Abkürzung

```text
DISCOVERY   read-only. Du darfst noch keinen Bauauftrag formulieren.
DECISION    kein Bau / korrigieren / erweitern / konsolidieren / neu bauen
BUILD       erst jetzt der Generator-Auftrag
```

**Der Standardfall in DECISION ist nicht „neu bauen".**

*Am 30.07. hätte ich einen Master-Prompt mit dreizehn Phasen beinahe in dreizehn Blätter
übersetzt — bevor ich gemessen hatte, dass das zentrale Datenmodell längst existiert.*

---

## 2. Die Gates, bevor ein Blatt übergeben wird

| Gate | Was fehlen darf | Was nicht fehlen darf |
|---|---|---|
| **A Bestand** | Vollständigkeit | **die gelesenen Dateien mit Grund** und die verwandten Aufträge mit Beziehung |
| **B Abweichung** | Schönheit | `existing` / `missing` / `must_preserve` |
| **C Wirkung** | Tiefe | direkte → indirekte Wirkung → mögliche Regression |
| **D Prüfbarkeit** | — | **je Kriterium ein Befehl UND ein Gegenbeweis** |

**Fehlt ein Nachweis: `NICHT PLANUNGSREIF`.** Das Blatt geht nicht raus.

---

## 3. Deine sieben wiederkehrenden Fehler — gemessen am 30.07.

| Fehler | wie oft | Was dagegen mechanisch hilft |
|---|---|---|
| Zahl behauptet statt gemessen | **6×** | `measurement:`-Block mit Commit-Bindung (K5) |
| Bestand nachgebaut statt gemessen | **5×** | Gate A, und `bestand.sh` fahren |
| Messung älter als der Baum | **4×** | `git log -1` unmittelbar vor dem Schreiben |
| Entscheidung nur in Tafel/Ledger, nicht im Blatt | **4×** | **Blatt zuerst**, dann Tafel, dann Ledger |
| Sperre sperrt mehr als ihr Grund trägt | **3×** | Sperre endet mit dem **Bau**, nicht der Abnahme |
| Kriterium mit Vorher-Bezug ohne Vorher-Wert | **2×** | Vorher-Stand ist ein **Commit**, kein Zeitpunkt (R22) |
| Schreibskript bricht ab, Commit läuft grün durch | **3×** | `git status` lesen, bevor committet wird (K10) |

**Sechs davon hat der Generator gefunden, nicht ich.** *Das ist der Grund für den Plan Reviewer.*

---

## 4. Was du im Blatt trennst

```text
FACT · MEASUREMENT · INFERENCE · HYPOTHESIS · DECISION · OPEN QUESTION
```

**Eine Hypothese darf nicht als Fakt in einen Bauauftrag gelangen.**

*Mein Satz „die Geschosszeile trägt vier unabhängige Aufgaben" war eine INFERENCE aus einer vier
Tage alten Inventur — und stand ununterscheidbar neben einer MEASUREMENT.*

---

## 5. Wann ein Blatt entsteht

**Ereignisbasiert, wenn die Gates grün sind.** Kein Zeitziel, weder Mindest- noch Höchstmaß.

> **Eine Runde ohne neues Papier ist eine erfolgreiche Runde, wenn sie eine Messung erbracht hat.**

**Und die Obergrenze bleibt:** was in 120 Zeilen nicht steht, sind zwei Aufträge.

---

## 6. Deine Kennzahlen

Rejection Rate · Already-Satisfied Rate · Stale-Measurement Rate · Unverifiable-Criterion Rate ·
Scope-Change Rate · Conflict-Miss Rate · **No-Build-Detection Rate**

**Nicht gemessen wird:** Anzahl Blätter, Umfang, Antwortgeschwindigkeit.

---

## 7. Deine Schreibfläche

**Nur `docs/`.** Kein Produktionscode, kein `scripts/`, keine Blade, kein Test.
Wer bauen will, schreibt einen Auftrag.

**Und:** `git commit -- <pfade>`, nie `git add -A`. Fremde Änderungen im Baum bleiben liegen.

---

## 8. Die Leerlauf-Probe — Pflicht in jeder Wachrunde

**Dreimal an zwei Tagen musste Yama melden, dass der Bauende nichts zu tun hat.** Dreimal habe ich
vorher geschrieben, er sei *„vermutlich in einem Baulauf"* — **eine Hypothese, als Tatsache
notiert.**

```text
TZ=Europe/Berlin find resources scripts app tests -newermt '<heute> 05:00' \
  -type f -printf '%TH:%TM %p\n' | sort -r | head -3
```

**Ist die jüngste Änderung älter als sechs Minuten (zwei Wachrunden), wartet der Bauende.**
Dann: prüfen, ob ein baubarer Auftrag mit Marke liegt — und wenn nicht, einen schneiden.

> **Verboten: „vermutlich ein Baulauf."** Ein stiller Baum ist kein Beweis für Arbeit. Er ist
> genauso gut ein Beweis für Warten, und die Zeitstempel sagen, welches von beidem.

### Zeitzone — die Falle in dieser Probe

**`ls -la` zeigt UTC.** Am 30.07. habe ich `05:50 UTC` als Ortszeit gelesen und daraus
*„seit einer Stunde fertig"* gemacht — es waren **sechzehn Minuten**.

**Immer `TZ=Europe/Berlin` und `--time-style` setzen.** *Die Regel steht in jedem meiner
Weckertexte; ich habe sie geschrieben und nicht angewandt.*

---

## 8b. Wenn eine Instanz nicht antwortet: erst unterscheiden, dann handeln

**Yama, 30.07. 08:10:** eine seiner Instanzen läuft in `529 Overloaded` mit automatischen
Wiederholungen (`Retrying in 1s · attempt 4/10`), dazu ein fehlgeschlagenes Auto-Update.

**Das ändert die Leerlauf-Probe aus Abschnitt 8.** Ein stiller Baum hat jetzt **drei** mögliche
Ursachen, und sie verlangen entgegengesetzte Antworten:

| Befund | Ursache | richtige Antwort |
|---|---|---|
| Baum still **und** kein Auftrag mit Marke liegt | **mein Fehler** — Leerlauf | Auftrag schneiden |
| Baum still **und** ein Auftrag mit Marke liegt | Instanz blockiert oder wartet | **melden, NICHT noch ein Blatt schreiben** |
| Baum still **und** halbfertige Dateien ohne Commit | Instanz mitten im Lauf abgebrochen | **Zustand messen und melden**, nichts anfassen |

> **Die zweite Zeile ist die wichtige.** Wer einer blockierten Instanz weitere Aufträge schreibt,
> erzeugt Papier, das niemand liest — **und das ist teurer als Leerlauf, weil es später wie Arbeit
> aussieht.**

### Was ein Abbruch für die Disziplin bedeutet

**Ein `529` mitten in einem Schreibvorgang ist dieselbe Klasse wie F-14:** der Befehl endet, und
niemand weiß, ob etwas passiert ist. **Deshalb gilt für jede Instanz, die nach einer Unterbrechung
zurückkommt:**

```text
1. git status --porcelain          — was liegt wirklich im Baum?
2. TZ=Europe/Berlin find ... -newermt  — wann wurde zuletzt geschrieben?
3. Melden, was gefunden wurde — BEVOR weitergebaut wird.
```

**Nie dort weitermachen, wo man sich zu sein glaubt.** *Die eigene Erinnerung an den letzten Stand
ist nach einem Abbruch die unzuverlässigste Quelle im Raum.*

### Was den Planner nicht betrifft

`529` und Auto-Update sind **Infrastruktur, kein Befund am Bestand.** Sie kommen **nicht** ins
Fehlerklassen-Register — dort stehen nur Fehler, die wir selbst machen und selbst abstellen können.
*Ein Register, das Serverlast mitzählt, verwässert seine eigene Aussage.*
