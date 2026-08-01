> **⚠ NACHRANGIG — die Arbeitsgrundlage ist [`docs/agents/00-REGELWERK.md`](../00-REGELWERK.md).**
>
> Dieses Blatt ist am 30.07.2026 entstanden, **ohne zu prüfen, dass es das Regelwerk schon gibt**
> (377 Zeilen, gültig seit 28.07., R1–R22). **Bei Widerspruch gilt das Regelwerk**, bis Yama
> entschieden hat — Befund **PB-014**. Was hier steht, **schärft** und **ersetzt nicht**.

---

# KERNREGELN — Ebene 1

**Eine Seite. Sie wird von allen Rollen vor jedem Vorgang gelesen — aber NACHRANGIG zum Regelwerk oben.**
*Der frühere Satz „diese Regeln werden IMMER geladen" widersprach dem Kopf dieser Seite zehn Zeilen weiter oben (Befund PB-026). Es gilt: gelesen ja, vorrangig nein.*
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

- **Der Generator committet selbst — auf der Aufgaben-Branch, niemals nach `main`.**
  *(Entscheidung Yama, 30.07.2026, 09:45 — sie beendet den Streit von heute früh.)*
  **Ein Commit ist ein prüfbarer Zwischenstand, kein Freigabeakt.** Wer baut, liest vor dem
  Commit den vollständigen Diff, prüft den Scope, fährt die betroffenen Tests, stagt **nur
  freigegebene Pfade** (`git commit -- <pfade>`) und meldet **Basis-SHA und Generator-SHA**.
  Der Evaluator prüft `git diff <basis>..<generator>` in einem frischen Worktree.
- **Kein Ball bleibt liegen** *(Yama, 30.07., 19:07: „es darf nichts liegen bleiben egal wer die
  aufgabe hat muss sofort erledigen, damit wir schnell weiter kommen")*.
  **Jede Übergabe nennt einen Ballbesitzer und eine Uhrzeit.** Wer den Ball hat, fängt sofort an
  oder sagt in einer Zeile, warum nicht und wann.
  **Der Planner führt in jeder Wachrunde eine Ballbesitz-Uhr:** Rolle · Gegenstand · seit wann ·
  läuft oder still. **Ein Ball, der ohne Wort still liegt, wird beim nächsten Blick gemeldet** —
  nicht einmalig, sondern bis er sich bewegt.
  **Die einzige Ausnahme ist die, die Yama selbst gesetzt hat:** P2/P3-Befunde an Papier werden
  im Produktionsmodus registriert und nicht abgehandelt. *Sie blockieren nichts — deshalb hat er
  sie gestoppt.* **Alles, was Code, Abnahme oder Sicherheit blockiert, wird sofort erledigt.**
- **Niemand merged nach `main`, niemand pusht, niemand gibt den eigenen Commit frei.**
  Merge und Deploy sind Tor 2 und gehören Yama allein.
- **Niemals `git add -A` oder `.`** — `git commit -- <pfade>`.
- **Kein `rm`/`unlink` auf dem Mount** — `mv` nach `_to_delete/` bzw. `.git/_locks_beiseite/`.
- **Persistierte Schema-Werte nicht umbenennen** (`type: wall|window|door|ceiling`, `objectType`,
  `zoneType`, `routeType`).
- **Tor 2 (main-Merge, Deploy) gehört Yama allein.**
- **Schreib-Heimat einhalten:** lesen überall, schreiben nur in der zugewiesenen App.

---

## Die Votumszeile — eine Zeile, die sechs Kennzahlen mechanisch macht

**Gemessen am 30.07., 07:48:** die Voten sind heute **nicht zählbar**.

```text
"TRÄGT NICHT"   12   ·   "TRAEGT NICHT"    1     zwei Schreibweisen
"NICHT PRÜFBAR" 13   ·   "NICHT PRUEFBAR"  0
"FREIGABE"     151                               das Wort steht ueberall im Fliesstext
```

**Ein `grep` auf Fließtext zählt Erwähnungen, nicht Voten.** *Das ist F-09 — „Text wird gemessen,
nicht Absicht" — und sie hätte hier ihre dritte Ausprägung bekommen.*

> ### Jedes Votum und jede Quittung beginnt mit EINER maschinenlesbaren Zeile
>
> ```text
> VOTUM: auftrag=AUF-83-T3 rolle=generator ergebnis=TRAEGT-NICHT commit=fe0af8df datum=2026-07-30T06:25
> ```
>
> **Feste Felder:** `auftrag` · `rolle` (planner|generator|evaluator|plan-reviewer|pruefer) ·
> `ergebnis` · `commit` · `datum` (ISO).
>
> **Erlaubte Werte für `ergebnis`:**
> `TRAEGT` · `TRAEGT-NICHT` · `FREIGABE` · `NACHBESSERN` · `NICHT-PRUEFBAR` ·
> `BEREITS-ERFUELLT` · `PLANUNGSREIF` · `NICHT-PLANUNGSREIF` · `NICHT-NOTWENDIG` ·
> `PLANUNGSBLOCKIERT` · `ANGENOMMEN` · `ABGELEHNT` · `BEHOBEN`
>
> **Keine Umlaute, keine Leerzeichen im Wert.** Die Prosa darunter bleibt, wie sie ist —
> *sie ist für Menschen, diese Zeile ist für `grep`.*

**Was das kostet:** eine Zeile je Votum. **Was es liefert:** Rejection Rate, Already-Satisfied
Rate, No-Build-Detection Rate, Time-to-Verdict, Wiederholungsrate und die Rollenverteilung —
**alle aus `grep '^VOTUM: ' docs/handoff-status.md`**, ohne zweite Buchführung.

**Und die Regel, die am ersten Tag nötig wurde:** eine **echte** Votumszeile steht **am
Zeilenanfang**. **Jedes Beispiel wird um zwei Leerzeichen eingerückt.**

*Gemessen 30.07., 07:54: `grep -c "^VOTUM: auftrag="` lieferte **3**, obwohl nur **2** Voten
geschrieben waren — die dritte war das Beispiel aus der Regel selbst. **Ein Beispiel, das wie ein
Datensatz aussieht, IST ein Datensatz, sobald jemand zählt.** Dritte Ausprägung von „Text wird
gemessen, nicht Absicht" — diesmal im Mechanismus, der genau das verhindern sollte.*
