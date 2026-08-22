# VOTUM A-43 — Kennungsmuster mehrgliedrig + Aktionsvokabular

**evaluator · 22.08.2026 · Auftrag `ABNAHME-evaluator-A-43` gen 10 · Lease-Token 1**
**Basis `c82df498` · Endstand `8a08d625` · Blatt @ `rolle/planner 1e5ac476`**

## Ergebnis: ABGENOMMEN — 12 von 12 Kriterien erfüllt

Posten 1 und 2 als eine Lieferung. Posten 3 (Dirigent-Bereich) ist laut Auftrag **nicht** Teil
dieser Lieferung und wird von mir weder geprüft noch als Lücke gewertet.

| # | Beleg (selbst ausgelöst) |
|---|---|
| **1** | Acht Formen über den **echten Ausleseweg** (`KERN`/`WORTLAUT` aus der Quelle extrahiert und ausgeführt, nicht nachgebaut): **rot 4/8 → grün 8/8** |
| **2** | Dieselben acht Formen gegen `BEINAHE`: **rot 4/8 → grün 8/8**, deckungsgleich mit `KERN` — keine Divergenz |
| **3** | Zwei Kennungen → `VERSTOSS`, **`echo $?` = 1**; `Z0-I1` → durchgelassen, **= 0**; kein `UNGEPRUEFT` |
| **4** | Drei Zählungen geankert am Stand `a82403f1`: **A 71 · B 30 · C 23**; Differenz A−C = 48, B−C = 7 |
| **5** | `Z2-W0-1 · Z2-W0-3` → **1**; `Z0-I1 · Z1-W1-1` → **1**; `A-38 · A-42` → **1** |
| **6** | `zustand: FOO` → **1**; leere Kennung → **1**; Kennung wird **ganz** gegriffen (`Z1-W1-1`, nicht `Z1`) |
| **7** | Diff des Baus: nur `scripts/rollen-tor.sh` und `scripts/status-erzeugen.sh`; `resources/ app/ docs/STATUS.md` **leer** |
| **8** | Alle **elf** im Blatt genannten Verben → **RC 0, keine Meldung** |
| **9** | `warten`, `parken`, `pausieren` und alle `warten_dann_*` → **7 mit „keine Arbeitsanweisung"** — Pause, nicht unbekannt |
| **10** | `quatsch`, `bauenX`, `wartenXYZ`, `rueckweg_x` → je **7 „unbekannte aktion"**; **kein Glob-Loch** bei `wartenXYZ` |
| **11** | Messbefehl des Blatts zitiert: **genau 1 Datei** (`scripts/rollen-tor.sh`), **genau 1** `case "$AKTION" in` |
| **12** | Alle sieben tatsächlich gesetzten `aktion`-Werte laufen durch: 6× Arbeit (0), 1× Pause (7 mit Pause-Meldung) — **keines fällt in „unbekannt"** |

## Zwei Zahlen, die ich nicht schönrede

**A-43-4, zweiter Teil.** Von den 23 Betreffs der geankerten Grundmenge tragen 20 genau eine
Kennung; erkannt werden **17 — in rot wie in grün**. Der Bau ändert diese Zahl **nicht**. Grund,
selbst nachgemessen statt vermutet: die Grundmenge enthält **keine einzige** einzelne Z-Kennung —
alle drei Z-Betreffs der Historie sind Mehrfach- oder Bereichsangaben (`Z2-W0-5 · Z2-W0-10 · …`,
`Z1-W1-1..5`). Der Nutzen von A-43 ist deshalb **zukunftsgerichtet**: er zeigt sich am nächsten
Z-Zustandscommit, nicht an der Vergangenheit.

Die drei nicht erkannten Betreffs liegen **nicht am Kennungsmuster**, und das habe ich einzeln
belegt statt behauptet — jeder wird erkannt, sobald das *andere* Feld berichtigt ist:

```
Z1-W1-1..5 (Bereichsschreibweise)      Original NICHT · mit Z1-W1-1 ERKANNT
W-17/1 · BETRIEBSBESTAETIGT · —        Original NICHT · mit Rolle statt Gedankenstrich ERKANNT
A-41   · BETRIEBSBESTAETIGT · —        Original NICHT · mit Rolle statt Gedankenstrich ERKANNT
```

Der Gedankenstrich im Rollenfeld ist ein Altbefund aus der A-37-Matrix („das Ballfeld verlangt eine
Rolle, keinen Gedankenstrich") und kein Gegenstand von A-43.

**A-43-12.** Erfüllt — aber die Grundmenge ist **heute einwertig**: sechs von sieben Rollen tragen
`bauen`, weil der Dirigent die Tor-Wörter gesetzt hat. Ein Kriterium, das „alle tatsächlich
gesetzten Werte" misst, misst heute also fast nur ein Wort. Die Vielfalt habe ich über A-43-8
hergestellt (elf Verben einzeln). Ich sage es, weil es dieselbe Klasse ist, die ich heute bei
A-37-26 an mir selbst gefunden habe: **eine Probe, die den Fall nicht herstellt, belegt ihn nicht.**

## Eigene Fehler in dieser Abnahme — vier, alle meine

1. **`BEINAHE` mit dem falschen Betreff gemessen.** Erst 0/8 in rot *und* grün. Ursache: `BEINAHE`
   greift auf Betreffs **ohne** `zustand:` — es ist der Melder für Beinahe-Zustandsmeldungen. Mit
   passendem Betreff: 4/8 → 8/8. Beinahe hätte ich eine Divergenz gemeldet, die es nicht gibt.
2. **`$?` nach einer Kommandosubstitution gelesen.** In `echo "… $(…) exit=$?"` läuft die
   Substitution zuerst und überschreibt den Rückgabewert; A-43-5 und A-43-6 schienen dadurch mit
   **0** zu scheitern. Mit `rc=$?` vor der Ausgabe: je **1**, wie verlangt. **Das wäre ein schwerer
   Falschbefund geworden** — „Mehrfachkennungen gehen durch" ist so ziemlich das Gegenteil dessen,
   was der Bau tut.
3. **`freigeben` geraten** statt `release_pruefen` aus dem Blatt gelesen; mein Wort fiel durch, das
   verlangte nicht. Der Auftrag sagt „Messbefehl des Blattes zitieren, nicht nachbauen" — für
   Vokabeln gilt dasselbe.
4. **Klassifikation „eine Kennung" zählte den Zustand mit** (`ABGENOMMEN` beginnt auch groß), damit
   fielen alle 23 in „mehrere". Erst mit der Zustandsliste getrennt.

Kein einziger dieser vier war ein Mangel des Baus. In drei von vier Fällen hätte ich ohne die
Aufbauprüfung einen Mangel gemeldet, den es nicht gibt.

## Nähe zum Gegenstand, offengelegt

Ich habe den Befund, der zu A-43 führte, mitgemeldet (`evaluator-eigener-anteil-a37-26-kennungs-
formen.yaml`, 12:06) und dort meinen eigenen Prüfanteil eingeräumt: ich hatte A-37-26 nur mit
A-Kennungen geprobt. Gebaut habe ich nichts, also bin ich nicht befangen — aber ich sage es, weil
ich hier abnehme, dessen Lücke ich mitgefunden habe. Gemessen habe ich die Kriterien des Blatts,
nicht meine Erwartung.

## Ball

**Dirigent** — A-43 abgenommen. Damit sind die Z-Zustandscommits entsperrt und die Tor-Wörter
können zurückgenommen werden.
