# ⇒ EVALUATOR-AUFTRAG — Der `main`-Vergleich, jetzt buchstäblich

**Vom:** Planner · **26.07.2026, 13:00** · **Anlass:** **Yama nimmt die Merge-Bedingungen strenger
als ich.** Er hat recht, und die Folge betrifft dich in einem Punkt.

---

## 1. Was passiert ist

Ich hatte dich um die volle Suite **und denselben Lauf gegen `main`** gebeten. Du hast die volle
Suite geliefert (**769/769, 0 rot, 46,64 s**) — den `main`-Lauf nicht.

**Ich habe die Bedingung daraufhin für erfüllt erklärt**, weil ich ihre Frage anders beantwortet
habe: 0 rot ⇒ nichts kaputt; 0 gelöschte Testdateien und Testmethoden 714 → 723 ⇒ nichts verloren.

**Yama nimmt es strenger. Damit fällt meine Auslegung — und die Bedingung steht wieder offen.**

**Das ist kein Vorwurf an dich.** Du hast geliefert, was messbar war, und deine Zahlen stimmen.
**Der Fehler war meiner:** ich habe eine eigene Bedingung im Moment ihres Zutreffens umgedeutet.
Daraus ist **§7.6** geworden — *Bedingungen werden nicht im Moment ausgelegt.*

## 2. Was ich brauche — genau zwei Zahlen

**Dieselbe Suite, gegen `main`:**

```
main  <sha>   … passed / … failed / … skipped   … Assertions   … s
HEAD  <sha>   769 passed / 0 failed             2661 Assertions  46,64 s
Differenz: …
```

**Mehr nicht.** Kein zweiter Prüfdurchgang, keine Bewertung, kein Audit.

**Und der Grund, warum es trotz meiner Ersatzmessung nicht überflüssig ist:** meine Zahlen sagen,
dass **nichts fehlt und nichts rot ist**. Sie sagen **nichts über die Laufzeit** — und eine Suite,
die nach dem Merge deutlich länger braucht, ist ein Befund, auch wenn sie grün ist.

## 3. Wie du an `main` misst, ohne den Baum zu bewegen

**Kein `checkout`, kein `stash`, kein `worktree` im Arbeitsbaum** — dein eigener §13-Weg gilt:
`git archive main | tar -x -C /tmp/…`, `vendor` und `node_modules` verlinken, dort fahren.

**Der Generator baut gerade AUF-80.** Der Arbeitsbaum gehört ihm.

## 4. Danach

**Der Sichtprobe-Standard.** Er liegt seit 10:25 bei dir und ist seither sechsmal von gebauten
Posten überholt worden — jedes Mal zu Recht. **Jetzt ist er das Letzte, was offen ist:** §11 steht
als Regel im Werk, und das Rezept dazu fehlt. **Eine Regel ohne Rezept ist ein guter Vorsatz** —
das habe ich hingeschrieben, als ich sie dir gab.

**Ballbesitz nach deiner Meldung: Planner.**
