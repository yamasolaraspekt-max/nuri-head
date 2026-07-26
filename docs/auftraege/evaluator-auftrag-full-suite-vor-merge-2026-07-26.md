# ⇒ EVALUATOR-AUFTRAG — Die volle Suite vor dem Merge

**Vom:** Planner · **26.07.2026, 10:25** · **Anlass:** deine Merge-Reife-Prüfung und dein eigenes
Angebot, die Full-CRM-Suite zu fahren. **Angenommen — aber vor dem Merge, nicht erst vor dem Deploy.**

**Vorher gelesen/gemessen:** deine Prüfung · `git diff --numstat main...HEAD`, nach Verzeichnis
summiert · `git diff --name-status main...HEAD -- database/migrations/` (**leer**) ·
`git diff --stat main...HEAD -- routes/ app/Http/` · Ledger „PLANNER 26.07., 10:15".

---

## 0. Zuerst: deine Selbstbegrenzung war richtig

Du zertifizierst die **Insel**, nicht den CRM-Stand, und sagst das dazu. **Das ist §12.3 in Anwendung
auf dich selbst und die siebte Selbstbegrenzung von dir an einem Tag.** Ein Prüfer, der die Grenze
seiner Prüfung mitliefert, ist mehr wert als einer, der alles grün meldet.

**Ich habe deine Sorge nachgemessen, und sie fällt kleiner aus, als du annimmst:**

| außerhalb der Insel | |
|---|---|
| `app/Http` | **38 Zeilen, 1 Datei** (`HausplanerController`) |
| `tests/Feature` | 223 Zeilen, 2 Dateien |
| `database/migrations` | **0** |
| `routes/` | **0** |
| Skript im Wurzelverzeichnis | 21 Zeilen, 1 Datei |

**Das ist kein Grund, die volle Suite zu lassen — es ist der Grund, warum sie billig ist.**

## 1. Was ich von dir will

**Die vollständige PHP-Testsuite gegen `ticket_testing`, gegen den committeten HEAD.** Nicht nur
`tests/Feature/Hausplaner`.

**Und zwei Zahlen, die in deiner bisherigen Prüfung fehlen:**

1. **Dieselbe Suite gegen `main`** — der Vergleichsstand. **Ohne ihn ist „grün" wertlos:** wenn dort
   dieselben drei Tests rot sind, hat der Branch nichts kaputt gemacht, und das ist eine völlig
   andere Aussage als „alles grün".
2. **Die Laufzeit beider Läufe.** Eine Suite, die nach dem Merge doppelt so lange braucht, ist ein
   Befund — auch wenn sie grün ist.

## 2. Wie gemeldet wird

**Rohausgabe, nicht Zusammenfassung** — Testzahl, Assertions, Fehlerliste, Laufzeit, je Lauf.
Danach ein Satz je Zeile:

```
main    <sha>   … passed / … failed / … skipped   … Assertions   … s
HEAD    <sha>   … passed / … failed / … skipped   … Assertions   … s
Differenz: …
```

**Ein Test, der auf beiden Ständen rot ist, ist kein Merge-Hindernis** — er ist ein Befund für die
Tafel und gehört benannt, nicht verschwiegen und nicht repariert (§12.1).

## 3. Was du **nicht** tust

- **Du reparierst nichts** (§12.1). Findest du einen roten Test, meldest du ihn — auch wenn er
  einzeilig aussieht.
- **Du legst keine Daten an** (§12.9). Braucht die Suite etwas, das fehlt, ist das ein Befund.
- **Du merged nicht und pusht nicht** (§12.10). **Tor 2 gehört Yama.**
- **Du weitest nicht aus** (§12.3): keine Migrationsprüfung, kein Lasttest, keine Browserläufe. Das
  ist eine Testsuite, kein Audit.
- **Du unterbrichst nichts.** Läuft gerade ein Bau im Baum, arbeitest du auf einer Kopie — der
  Arbeitsbaum gehört in diesem Moment dem Generator.

## 4. Der Zeitpunkt

**Jetzt, parallel zu AUF-74.** Der Generator baut, du misst — ihr braucht denselben Baum nicht.

**Und die Bedingung, unter der dein Ergebnis gebraucht wird:** Es geht **nicht** darum, ob heute
gemerged wird. Es geht darum, dass die Zahl **vorliegt**, wenn der Zeitpunkt kommt. **Eine Messung,
die erst am Tag der Entscheidung angefangen wird, entscheidet nichts — sie verzögert nur.**

**Ballbesitz nach deiner Meldung: Planner.**
