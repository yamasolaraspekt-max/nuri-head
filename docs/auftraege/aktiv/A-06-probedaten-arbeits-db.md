# A-06 — Probedaten in der Arbeits-DB: gemessen, nicht gelöscht

```yaml
auftrag: A-06
titel: "Sieben Fremdzeilen in der Arbeits-DB ticket bereinigen - Freigabe Yama"
basis_sha: 54ef3017
block: DECISION_BLOCKED
entscheidung_gehoert: Yama
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```

## Anlass

Der Evaluator hat es **gegen sich selbst gemeldet** (`13c65f6f`): seine L-01-Probedaten vom 03.08.
liegen noch in der Arbeits-DB `ticket`. **Er hat gemessen und NICHT gelöscht** — §15 verlangt einen
eigenen Auftrag und eine Freigabe. *Das war richtig, und es ist der Grund, warum dieser Auftrag
existiert statt eines stillen `DELETE`.*

## Was drinliegt — vollständig gemessen an `54ef3017`

```text
FALL A  fuenf Hausplaner-Dokumente auf ECHTEN Alternativ-Zeilen
  doc 20-24  ->  alternative_id 143, 142, 141, 140, 139
  angelegt   03.08. 23:11-23:26     scene_json 477-1006 B     schema v3
  die Alternativen selbst: angelegt 29.06. 02:30, in lead_alternative_adds (74 Zeilen)

FALL B  zwei SYNTHETISCHE Zeilen in einer Geschaeftstabelle
  lead_alternative_adds id 990002 und 990004   (2 von 74)
  dazu die Dokumente doc 18 und 19, angelegt 03.08. 14:13/14:17
```

### Es wurde NICHTS überschrieben — belegt

```text
Alternativen 139-143   angelegt 29.06. 02:30
Dokumentzeilen         angelegt 03.08. 23:11-23:26, created_at == updated_at (ausser 143: +2 min)
hausplaner_snapshots   0 Zeilen  (es gibt keine Revisionshistorie)

-> Die Dokumentzeilen ENTSTANDEN an dem Abend. Diese Alternativen trugen vorher KEIN
   Hausplaner-Dokument. Die Revisionsnummern 2 und 3 stammen aus seinem eigenen
   mehrfachen Speichern innerhalb von Minuten.
```

**Das ist die wichtigste Zeile des Auftrags.** *Ohne sie wäre die Frage „was ist verloren gegangen"
unbeantwortbar — und mit leerem Snapshot-Tabellenstand auch nicht nachträglich zu klären.*

### Eine Annahme von mir, die die Messung widerlegt hat

**Ich bin von einer Arbeits-DB mit echten Kundendaten ausgegangen.** Gemessen:

```text
customers   0 Zeilen
leads       0 Zeilen
lead_alternative_adds   74 Zeilen, die auf lead_id 141-145 zeigen - die es nicht gibt
```

**Die lokale `ticket` trägt keine Kundendaten.** Die betroffenen Alternativ-Zeilen sind
verwaiste Struktur­daten. **Das senkt das Risiko erheblich — und es ändert nichts an der Grenze:**
§15 und `CLAUDE.md` verbieten Testdaten in der Arbeits-DB unabhängig davon, wie viel dort steht.

> *Ich schreibe es hierhin, weil ich es vorher anders vermutet und beinahe so berichtet hätte.
> Die Grenze gilt wegen der Regel, nicht wegen des Schadens.*

## Was zu tun ist — und was ausdrücklich NICHT

**Vor jedem Löschen: die sieben Zeilen als JSON in eine Datei sichern**, Pfad im Bericht nennen.
*Ohne Snapshot-Tabelle ist die Datei der einzige Rückweg.*

```text
FALL A   die fuenf Dokumente doc 20-24 loeschen.
         Die ALTERNATIV-Zeilen 139-143 bleiben unangetastet - sie sind Bestand.
FALL B   Reihenfolge zwingend: erst die Dokumente 18/19, dann die Zeilen 990002/990004.
         Umgekehrt entstehen verwaiste Dokumente.
```

**NICHT Gegenstand:** keine weitere Bereinigung der 74 Alternativ-Zeilen · keine Aufräumarbeit an
verwaisten `lead_id`-Bezügen · **kein `TRUNCATE`, kein Löschen ohne `WHERE id IN (...)` mit
ausgeschriebenen Werten.**

## Kriterien

**A-06-1:** Vor dem ersten `DELETE` liegt die Sicherungsdatei mit allen **sieben** Zeilen und wird
im Bericht mit Pfad und Zeilenzahl genannt.

**A-06-2:** Nach der Bereinigung: `hausplaner_documents` hat **15** statt 22 Zeilen,
`lead_alternative_adds` hat **72** statt 74. **Gegenprobe:** die Alternativ-Zeilen 139–143
existieren unverändert weiter (`updated_at` unverändert).

**A-06-3:** Kein anderer Datensatz hat sich geändert. *Nachweis: Zeilenzahlen aller
`hausplaner_*`-Tabellen und von `lead_alternative_adds` vorher/nachher.*

**A-06-4 (`must_preserve`):** `docs 3–17` (18.07.–30.07.) bleiben unberührt. *Sie sind älter als
der Vorfall und gehören nicht zu ihm — sie sehen nur ähnlich aus.*

## Auswirkungen

```text
Schema · Migration · Code · Bundle    KEINE - reine Datenoperation
Bestandsdaten                          JA, deshalb DECISION_BLOCKED
Rueckweg                               die Sicherungsdatei; ohne sie kein Rueckweg,
                                       weil hausplaner_snapshots leer ist
```

## Offen — und das ist der ganze Punkt

**Yama gibt frei oder nicht.** *Bis dahin wird nichts gelöscht. Der Auftrag liegt fertig
geschnitten da, damit die Entscheidung eine Ja/Nein-Frage ist und keine Rechercheaufgabe.*
