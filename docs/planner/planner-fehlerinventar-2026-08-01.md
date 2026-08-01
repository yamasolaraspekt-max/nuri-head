# Fehler-Inventar des Planners — Stand 01.08.2026, 11:45

**Auf Yamas Frage:** *„kannst du ein inventar machen und alle deine fehler welche noch offen sind
auflisten"*

**Regel für diese Seite:** *jede Zeile nennt den Befehl, mit dem man sie heute nachprüfen kann.*
Was hier steht, ist **offen** — Behobenes steht im Ledger und verschwindet hier.

---

## A · Offene Fehlerklassen — die, die mir wieder passieren werden

*Aus `docs/auftraege/FEHLERKLASSEN.md`. **Eine Klasse ohne Barriere ist ein Vorsatz**, und R9 sagt:
bei der zweiten Wiederholung kommt eine technische Sperre, kein dritter Vorsatz.*

| Klasse | × | Zustand | Was mir fehlt |
|---|--:|---|---|
| **F-09** Text wird gemessen, nicht Absicht | **8** | ❌ **keine Barriere** | Ein Helfer, der Kommentare abzieht, bevor gezählt wird. **Heute zweimal hineingelaufen** (751 statt 68 tote Pfade; 8 Rohfarben in Kommentaren) |
| **F-11** Zusage prüft Zeichenkette ohne Wortgrenze | 2 | ❌ **keine Barriere** | `\b`-Grenzen erzwingen. *Beide Male vom Prüfenden gefunden, nicht von mir* |
| **F-03** Messung älter als der Baum | 4 | ⚠ Regel | Die Uhrzeit allein trägt nicht |
| **F-07** Bestand nicht gemessen, sondern nachgebaut | 5 | ⚠ Regel | R15 verlangt Aufmerksamkeit im richtigen Moment |
| **F-08** Leerlauf eines Bauenden | 2 | ⚠ Regel | **Heute eingetreten**: der Generator hatte um 11:20 genau ein Blatt, und das kam zurück |
| **F-08b** Entscheidung ändert den Auftrag, steht nur in Tafel und Ledger | 4 | ⚠ Regel | — |
| **F-12** vorlaufender Baum kostet eine Messung | 4 | ⚠ Regel | Beim dritten Mal wurde es teuer |
| **F-14** Schreibvorgang scheitert, Commit gelingt | 3 | ⚠ teilweise | **Nur Punkt 2 (`assert`) ist mechanisch** — Punkt 1 und 3 bleiben Selbstdisziplin. *Der Prüfer hat das heute so eingetragen, und er hat recht* |
| **F-10** Lock-Reste auf dem Mount | **28** | ⚠ mildert | Sie entstehen bei **jedem** Commit weiter; ich räume sie nur hinterher weg |

```text
grep -E '^\| \*\*F-' docs/auftraege/FEHLERKLASSEN.md
```

## B · Offene Einzelfehler von heute

| # | Fehler | Nachweis heute | Zustand |
|---|---|---|---|
| 1 | **Meine Kennzahl für Produktivcode sieht `config/` nicht.** `PB-043 T2` lag in `config/logging.php` — mein Wächter hätte die Änderung nie bemerkt | `git log -1 -- resources app tests routes public database scripts` enthält kein `config` | ❌ **offen** |
| 2 | **17 Blätter tragen `status: aktiv`**, die Struktur-Zusage S-01 erwartet **genau eines** | `grep -rl 'status: aktiv' docs/auftraege/*.md \| wc -l` → **17** | ❌ **offen** — ich habe zwei korrigiert, fünfzehn nicht |
| 3 | **PB-031, Rest:** tote Code-Verweise in den lebenden Papieren | `bash scripts/pfade-pruefen.sh docs/planner docs/auftraege docs/architektur` → **6 von 275** | ⚠ von 20 auf 6 |
| 4 | **Mein Blatt PB-023+024 war nicht baubar.** K-01 verlangte eine Null, die nur über einen von mir selbst ausgeschlossenen Weg erreichbar war | Rückgabe des Generators, 11:2x | ✅ behoben `3e48536b` — **aber gefunden hat es der Generator** |
| 5 | **`grep -c` im eigenen Blatt**, obwohl die Falle in meinen eigenen Notizen steht (liefert `exit 1` bei null Treffern) | Validator, 11:1x | ✅ behoben — **gefunden vom Validator, nicht von mir** |
| 6 | **751 tote Pfade gemeldet, wo 75 sind** — die Papiere nennen Pfade relativ zur Insel-Wurzel | erster Lauf `pfade-pruefen.sh` | ✅ behoben, **vor der Meldung** |
| 7 | **Meine Richtigstellung erhöhte den Fehlerzähler** — der Vermerk „diese sechs Pfade gibt es nicht" nannte die sechs Pfade | 72 → 74 | ✅ behoben (`historisch`-Marke) |

## C · Was ich schulde, aber nicht gebaut habe

| Posten | Zustand | Befehl |
|---|---|---|
| **Z-02 … Z-11** — die zehn Zeichnen-Scheiben, an denen deine **Zwischendecke** hängt | **0 von 10 geschnitten** | `ls docs/auftraege/ \| grep -cE 'z(0[2-9]\|1[01])-'` → 0 |
| **PB-024-N1** — die 17 fehlenden `--sa-`-Tokens im CRM | angekündigt, nicht geschnitten | — |
| **PB-024-N2** — Brücke für die Zeichenflächen-Farben zur Laufzeit | angekündigt, nicht geschnitten | — |
| **AUF-38-P3…P5** — 22 offene Inline-Stellen | genannt, nicht geschnitten | — |
| **Die drei haltbaren Werkzeug-Blätter** (`drehen` · `erkennung-bestaetigen` · `pv-modul`) | 0 geschnitten | `ls docs/auftraege/ \| grep -ciE 'drehen\|erkennung-bestaetigen\|pv-modul'` → 0 |
| **Die 18 wackeligen Werkzeug-Spezifikationen** neu messen | offen | — |

**Das ist der Posten, der heute wirklich weh tut:** *der Generator stand um 11:20 mit einem
zurückgegebenen Blatt da und hatte nichts anderes.* **F-08, und ich habe sie selbst ausgelöst.**

## D · Struktur, die ich mitverantworte

| Sache | Zahl | Warum es mein Posten ist |
|---|--:|---|
| **Ledger** `docs/handoff-status.md` | **1 828 740 Byte** | Ich habe ihn mitgeschrieben. `STAND.md` ist die Antwort, aber der Ledger wächst weiter |
| **Papiere in `docs/`** | **980 Dateien** | PB-042 liegt bei dir — die Menge kommt von uns |

## E · Das Muster, das durch alles läuft

**Von den sieben Einzelfehlern in Abschnitt B habe ich vier selbst gefunden — 6, 7 und die beiden
Hälften von 3.** *Drei fand ein anderer: der Generator, der Validator, der Prüfer.*

**Gestern war das Verhältnis neun von zehn gegen mich.** Es wird besser, und der Grund ist nicht
Vorsatz, sondern **Werkzeug**: der Validator sperrt, das Pfad-Skript misst, der Brücken-Test fängt
Mutationen. **Jedes Mal, wenn ich eine Klasse in eine Barriere verwandelt habe, hat sie mich beim
nächsten Mal selbst erwischt** — statt jemand anderen.

**Was daraus folgt, und es ist die einzige Zusage auf dieser Seite:** *die nächsten beiden Runden
gehen an F-09 und F-11* — die zwei Klassen mit Zähler 8 und 2 und **ohne jede Barriere**. Nicht an
neue Blätter, nicht an Papier.
