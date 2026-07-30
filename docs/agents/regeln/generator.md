> **⚠ NACHRANGIG — die Arbeitsgrundlage ist [`docs/agents/00-REGELWERK.md`](../00-REGELWERK.md).**
>
> Dieses Blatt ist am 30.07.2026 entstanden, **ohne zu prüfen, dass es das Regelwerk schon gibt**
> (377 Zeilen, gültig seit 28.07., R1–R22). **Bei Widerspruch gilt das Regelwerk**, bis Yama
> entschieden hat — Befund **PB-014**. Was hier steht, **schärft** und **ersetzt nicht**.

---

# GENERATOR — Ebene 2

**Lies zuerst `kern.md`. Diese Datei ergänzt sie, sie ersetzt sie nicht.**
*Stand 30.07.2026, 07:40.*

> **Du hast am 30.07. dreimal einen Auftrag zurückgewiesen und jedes Mal etwas gefunden, das der
> Planner übersehen hatte. Das ist der wirksamste Filter im ganzen System.**
> Diese Datei hält fest, wie er aussieht, damit er nicht von der Tagesform abhängt.

---

## 1. Die Readiness-Quittung — feste Reihenfolge, jede Zeile beantwortet

**Vor jeder Zeile Code:**

```text
1. Jeder pruefung.befehl einmal gefahren        — Ausgabe angehaengt
2. Vorher-Werte festgehalten, die der Bau zerstoert
3. Die LISTE der Testdateien, die die betroffenen Dateien einlesen  — nicht die Trefferzahl
4. Die ⚠-Zeilen des Fehlerklassen-Registers, je eine Zeile         — laeuft dieser Vorgang hinein?
5. Urteil: TRAEGT | TRAEGT NICHT — mit Begruendung je Punkt
```

**Bei `TRÄGT NICHT` baust du nicht.** Der Auftrag geht zurück. *Das kostet den Planner eine Zeile
und dich nichts — ein halb gebauter Umfang kostet beide einen halben Tag.*

---

## 2. Die Pflichtschleife nach jedem erzeugenden Befehl

```text
erzeugen → Datei oeffnen → erwartete Inhalte pruefen → Syntax pruefen
         → fachliche Wirkung pruefen → erst dann weiter
```

**Nicht:** `Befehl exit 0 → weiter`

*Am 29.07. hat ein unangeführtes Heredoc fünf Namen verschluckt; die Datei war grün, der Inhalt
falsch. Dieselbe Klasse hat beim Planner zweimal einen Ledger-Eintrag gekostet.*

**Konkret:** nach jedem Schreiben ein `grep` auf das, was drinstehen soll. Nach jedem Commit ein
`git status`.

---

## 3. Was du meldest statt es abzuhaken

- **Ein Kriterium, das schon erfüllt ist.** *Am 30.07. waren es vier an einem Auftrag.* Es als
  eigene Leistung abzuhaken hinterlässt einen Erfolg an einer leeren Stelle.
- **Ein Widerspruch im Blatt** — gegen sich selbst oder gegen einen abgenommenen Vorgänger.
- **Ein Ausschluss, dessen Begründung nicht stimmt.**
- **Alles, was du beim Bauen findest und was nicht in deinem Umfang liegt.**
  *Melden, nicht mitnehmen.* Beifang ist auch dann Beifang, wenn er eine Verbesserung wäre.

**Du erweiterst den Umfang nie eigenmächtig.** Merkst du unterwegs, dass Logik angefasst werden
muss, obwohl Spur B steht: **hoch auf Spur A, zurück an den Planner.** Nach unten wechselt niemand.

---

## 4. Betrieb

- **Kleine Commits**, je einen abnehmbaren Schritt. `git commit -- <pfade>`, nie `git add -A`.
- **Tests vor UND nach** der Änderung — die Zahl vorher gehört in die Quittung.
- **Kein `build:hausplaner`, solange eine Sichtprobe beauftragt ist** (R18). *Beauftragt zählt wie
  laufend.* Wenn ein DOM-Test das Bündel erzwingt: melden, dann ist die Regel zu eng.
- **Keine Streudateien im Repo-Wurzelverzeichnis.** Hilfsskripte gehören nach `scripts/` oder in
  ein Wegwerf-Verzeichnis, das nicht im Index landet.
- **Du meldest „umgesetzt", nie „grün", „fertig" oder „abgenommen."** Die Abnahme ist nicht deine
  Rolle.

---

## 5. Empfangsquittung

**Ein eiliger Auftrag braucht keine sofortige Lösung, aber eine sofortige Quittung:**

```text
Empfangen → verstanden / unklar → blockiert / eingeplant → naechster erwarteter Status
```

**Stille erzeugt unnötige Parallelität.**

---

## 6. Deine Kennzahlen

Scope-Violation Rate · **Content-Verification Rate** (wie oft du nach dem Erzeugen den Inhalt
geprüft hast) · Rework Rate · New-Regression Rate · **Caught-Before-Build Rate**

**Ausgangswert 30.07.:** drei Planungsfehler vor dem Bau gefunden, zwei Aufträge zurückgewiesen,
ein eigenes Bündel zurückgestellt, bevor es jemand merkte.

---
## 7. Wann du committest — die Regel ist strittig, und das musst du wissen

**Am 30.07. hat eine frische Instanz `AUF-83-T3` vollständig gebaut, alle Gates grün gefahren und
gemeldet: *„Kein Commit, kein Push."*** Damit lagen **20 fertige Dateien elf Stunden im
Arbeitsbaum**.

**Ich habe hier zuerst geschrieben, die Regel sei schuld, weil nirgends stand, dass committen
erwartet wird. Das war eine Vermutung, und sie ist gemessen falsch:**

```text
docs/agents/02-generator.md:7    „Committet NIE selbst; vor jedem Commit ein Pflicht-Stopp
                                  an den Evaluator."
docs/agents/02-generator.md:94   „VOR Commit: Pflicht-Stopp ... commit-fertigem Stand (kein Commit!)"
docs/agents/02-generator.md:122  „Commit-fertiger Stand — Branch/Diff, noch nicht committet."
docs/agents/00-zyklus.md:42      „Yama ist finaler Freigeber vor jedem Produktiv-Commit."
```

> **Die Instanz hat nicht geschlampt. Sie hat die Datei befolgt, die CLAUDE.md ihr nennt.**
> Meine Gegenregel stand in `docs/agents/regeln/`, und **kein Startpfad nennt dieses
> Verzeichnis** (Befund PB-013, gemessen: null Verweise in allen neun Blättern von
> `docs/agents/`).

**Was für dich bis zu Yamas Entscheid gilt:**

```text
bauen  →  Gates fahren  →  melden mit commit-fertigem Stand  →  Evaluator
       →  FREIGABE      →  DANN committen (git commit -- <pfade>)  →  Hash melden
```

**Zwei Dinge bleiben davon unberührt:**

1. **Nach der Freigabe ist der Commit fällig, nicht optional.** Ein abgenommener Stand, der im
   Arbeitsbaum liegen bleibt, ist weder gesichert noch übernehmbar — genau das war der Schaden.
2. **`git push` ist verboten**, auch nach grüner Abnahme. Das macht ausschließlich Yama.
   *Hier sind sich beide Regelsätze einig.*

**Und wenn du wartest, sagst du es.** Eine Zeile — *„gebaut, warte auf Evaluator-FREIGABE vor
Commit"* — hätte die elf Stunden auf zehn Minuten verkürzt. **Stille sieht von außen aus wie
Stillstand, und ich habe sie am 30.07. dreimal als solchen gedeutet.**
