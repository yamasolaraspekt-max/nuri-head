# KERNREGELN — Ebene 1

**Eine Seite. Diese Regeln werden IMMER geladen, von allen Rollen, vor jedem Vorgang.**
Alles Weitere steht in `regeln/<rolle>.md` (Ebene 2) und `regeln/<fach>.md` (Ebene 3).
**Der Ledger `docs/handoff-status.md` ist Historie und Beweisarchiv — kein Regelwerk.**

*Stand 30.07.2026, 07:30. Aus dem Konzept `docs/agents/KONZEPT-EVIDENZBASIERTE-PLANUNG.md`.*

---

## Die zwölf Kernregeln

| # | Regel |
|---|---|
| **K1** | **Bestand vor Entwurf.** Erst messen, was steht — dann beschreiben, was fehlt. |
| **K2** | **Lesen vor Schreiben.** Suche findet nur, wonach schon gefragt wurde. Im Zweifel die Datei lesen, nicht das Muster raten. |
| **K3** | **Tests sind Verhaltensdokumentation.** Wer eine Datei ändert, liest die Tests, die sie einlesen — die Liste, nicht die Trefferzahl. |
| **K4** | **Keine Behauptung ohne Beleg.** Rohausgabe schlägt Prosa. Prosa daneben ist erlaubt, statt dessen nie. |
| **K5** | **Keine Zahl ohne Messbefehl und Commit.** `command` · `observed_value` · `observed_at_commit` · `observed_at` · `freshness_rule` · `purpose`. Weicht der Commit von HEAD ab, wird neu gemessen. |
| **K6** | **Kein neuer Code, wenn bestehender wiederverwendet, angepasst, extrahiert oder konsolidiert werden kann.** Der Standardfall ist nicht „neu bauen". |
| **K7** | **Kein Auftrag ohne nachgewiesene Abweichung.** Was schon steht, ist kein Kriterium. |
| **K8** | **Jedes Kriterium hat einen Prüfbefehl oder einen eindeutig definierten Sichttest** — und einen Gegenbeweis. Nicht Messbares wird als `wunsch` gekennzeichnet und ist kein Abnahmekriterium. |
| **K9** | **Zusagen prüfen die Wirkung, nicht die Gestalt.** Eine Mutation gilt erst als Gegenbeweis, wenn die Datei danach noch lädt und der erwartete Test aus dem richtigen Grund fällt. |
| **K10** | **Ergebnis lesen, nie den Rückgabewert.** Nach jedem erzeugenden Befehl den Inhalt prüfen. Ein `exit 0` beweist nichts. |
| **K11** | **Keine stillen Scope-Erweiterungen und keine stillschweigende Änderung abgenommener Zusagen.** Wer mehr braucht, geht zurück an den Planner. |
| **K12** | **Rollentrennung.** Niemand nimmt eigene Arbeit ab. Wer zwei Rollen übernimmt, sagt es an. |

---

## Die sechs Aussagetypen

Jede relevante Aussage wird gekennzeichnet:

```text
FACT · MEASUREMENT · INFERENCE · HYPOTHESIS · DECISION · OPEN QUESTION
```

**Eine Hypothese darf nicht als Fakt in einen Bauauftrag gelangen.**

---

## Die Grundformel

```text
Bestand → Messung → Abweichung → Entscheidung → erst dann Auftrag
```

**Nicht:** `Idee → Beschreibung → der Bauende soll den Bestand prüfen`

---

## Die vier Gates vor jedem Auftrag

| Gate | Nachweis |
|---|---|
| **A** | **Bestand** — gelesene Dateien mit Grund, vorhandenes Verhalten mit Beleg, verwandte Tickets mit Beziehung |
| **B** | **Abweichung** — `existing` / `missing` / `must_preserve` |
| **C** | **Wirkung** — direkte Wirkung → indirekte → mögliche Regression → nötiger Gegenbeweis |
| **D** | **Prüfbarkeit** — je Kriterium Aussage, Ausgangszustand, Aktion, erwartetes Ergebnis, Befehl, Gegenbeweis |

**Fehlt ein Nachweis: `NICHT PLANUNGSREIF`.** Nicht „vorsichtiger sein".

---

## Die drei Phasen

```text
DISCOVERY  read-only, kein Bauauftrag erlaubt   → discovery-report
DECISION   kein Bau / korrigieren / erweitern /
           konsolidieren / neu bauen            → decision-record
BUILD      erst jetzt der Generator-Auftrag     → build-contract
```

---

## Rhythmus

| | |
|---|---|
| **Wache** | alle 3 Minuten: lesen, Git-Status, Blocker aufnehmen, **Empfangsquittung senden** |
| **Denken** | ohne Veröffentlichungszwang: lesen, messen, Hypothesen widerlegen |
| **Schreiben** | **ereignisbasiert** — ein Auftrag entsteht, wenn das Readiness-Gate grün ist. Kein Zeitziel |

**Stille erzeugt unnötige Parallelität:** `Empfangen → verstanden/unklar → blockiert/eingeplant →
nächster erwarteter Status`.

---

## Betriebsgrenzen (unverhandelbar)

- **Niemals pushen.** Kein `git push` aus irgendeiner Instanz, auch nicht nach grüner Abnahme.
- **Niemals `git add -A` oder `.`** — `git commit -- <pfade>`.
- **Kein `rm`/`unlink` auf dem Mount** — `mv` nach `_to_delete/` bzw. `.git/_locks_beiseite/`.
- **Persistierte Schema-Werte nicht umbenennen** (`type: wall|window|door|ceiling`, `objectType`,
  `zoneType`, `routeType`).
- **Tor 2 (main-Merge, Deploy) gehört Yama allein.**
- **Schreib-Heimat einhalten:** lesen überall, schreiben nur in der zugewiesenen App.
