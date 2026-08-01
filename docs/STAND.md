# STAND — das Arbeitsgedächtnis

> **Diese Seite wird ÜBERSCHRIEBEN, nicht angehängt.** Sie ist eine Seite lang und bleibt es.
>
> **Warum es sie gibt** *(Yama, 31.07. 08:15)*: „euer größtes Problem ist, dass ihr
> Gedächtnisverlust habt."
>
> **Regeln für diese Datei:** eine Zeile je Sache · eine Rücknahme ERSETZT die Aussage · was
> erledigt ist, verschwindet (es steht im Ledger) · **kein Datum ohne Zahl, keine Zahl ohne Befehl.**

**Zuletzt geschrieben:** 01.08.2026, 10:25 · Planner

---

## 1. Wo der Bau steht — gemessen 10:08 (01.08.)

```text
main                    39b18514   byte-identisch gemergt, Evaluator: 16 von 16 Commits, alle Gates gruen
Zweig                   154e4867   24 Commits voraus   Locks 0
Baum                    1 Eintrag: docs/handoff-status.md (Zeilen des PRUEFERS, uncommittet seit 09:45)
```

**Der Stillstand ist vorbei.** Nach 25 Stunden sind beide Instanzen um 09:59 bzw. 10:02 wieder
gelaufen.

| Sache | Zustand |
|---|---|
| AUF-48 · Z-01 · M2 · AUF-38-P1 | **fertig und abgenommen** |
| **PB-043 Teil 2** (Log-Rotation) | **abgenommen** `085f285c` (01.08. 09:59) — grün, mit Wirkungsnachweis statt Konfigurationslesen |
| **AUF-91** (Hinweis unter 1024 px) | **GEBAUT** `154e4867` (01.08. 10:02). 7 Dateien, +258/−29, neue Testdatei `mindestbreiteHinweis.test.ts` (122 Z.). **Beim Evaluator, nicht abgenommen** |
| **PB-047** (CRM-Fehler) | **Blatt liegt seit 31.07. 01:00 — 33 h, gebaut 0.** `grep -oE 'user[?]-.name' … \| wc -l` → **1**. Die Datei ist seit 28.06. unberührt |

**Prüfbefehle, beide auf Yamas Mac lauffähig:**

```text
AUF-91  ls resources/planner/hausplaner/app/rahmen/MindestbreiteHinweis.tsx
PB-047  grep -oE 'user[?]-.name' app/Http/Controllers/Dashboard/SidebarCountController.php | wc -l
```

### Die Kennzahl für Produktivcode — **korrigiert 01.08.**

```text
git log -1 --date=format-local:'%d.%m. %H:%M' --pretty='%h %ad %s' \
  -- resources app tests routes public database scripts config
```

**`config` fehlte bis heute.** `PB-043 Teil 2` lag in `config/logging.php` — **mein Wächter hätte
die Änderung nie gesehen**, und ich hätte gemeldet, der Generator stehe still, während er baut.
*Eine Kennzahl, die einen Ordner nicht kennt, ist keine Kennzahl, sondern eine Stichprobe.*

## 2. Wer ist am Ball

- **Evaluator:** **AUF-91 abnehmen** (`154e4867`) · AUF-25 (`17c8be22`, gebaut 25.07., **sieben Tage
  ohne Votum**) · Z-01/K-04 mit dem korrigierten Befehl nachziehen · AUF-86 (56 px) ·
  fehlende `.env.testing.example`.
- **Generator:** **PB-047** — das einzige, was den Merge noch von der Bauseite her sperrt.
  **Danach liegt AUF-38-P2 bereit** (`7a421871`): 18 offene Inline-Stellen in
  `GruppenzeileUndSchiene.tsx`, Stil-Brücken-Test als Pflichtteil.
- **Prüfer:** eine offene **Rückfrage** zu AUF-91 (kein Befund): schlägt ein Objektkopf-Menü durch
  die Sperre, wenn man es bei ≥1024 px öffnet und dann schmaler zieht? Er hat es **nicht im Browser
  gesehen** und schreibt es deshalb bewusst nicht als Befund. Drei Zeilen Probe liegen im Ledger.
- **Yama:** Papierstopp · PB-042 (Takt) · 18 dichteste Blades in den Browser?
- **Planner:** **AUF-38-P2 ist geschnitten** (01.08. 10:25). Als Nächstes: die drei haltbaren
  Werkzeug-Blätter (`drehen` · `erkennung-bestaetigen` · `pv-modul`) · **Z-02** (fangKern
  anschließen).

## 3. Was entschieden ist — gilt, bis es hier ersetzt wird

| Entscheidung | Kurz |
|---|---|
| **Merge gesperrt** | Bis **PB-047 gebaut** und **AUF-91 abgenommen** ist |
| **Antwort auf die Mahnung des Prüfers** (01.08. 09:45) | Ich ziehe die Blätter **nicht** zurück. AUF-91 ist seither gebaut; PB-047 bleibt stehen und ist richtig geschnitten — ihm fehlt ein Bauer, keine Begründung |
| **Merge-Vorprüfung** | `rev-list auto..main` erwartet die **Zahl der bisherigen Merges**, nicht 0 |
| **Ein Befehl je Nachricht** | Bei jedem Terminal-Vorgang. Yama, 30.07. 22:52 |
| **PB-047: kein `(int)`** | `user?->name` → `user?->employeeId()`. Ein `(int)` macht aus einem Namen still `0` |
| **Stil-Brücken-Test** | Jede weitere Stil-Scheibe trägt ihn. Sonst kamen 7 von 8 Mutationen durch |
| **`style={bezeichner}`** | Nicht Gegenstand von P1/P2. Eigenes Blatt P3, das zuerst das Messwerkzeug erweitert |
| **AUF-38-P2 geschnitten** | `7a421871`. Restweg danach gemessen, nicht geschätzt: `FussUndUeberlagerungen` 12 offen · `Kopfrahmen` 9 · `HausplanerApp` 1 — zusammen **22**, genau der Erwartungswert von K-01b |
| **21 Werkzeug-Spezifikationen** | 3 haltbar (`drehen` · `erkennung-bestaetigen` · `pv-modul`), 18 wackelig |

## 4. ZURÜCKGENOMMEN — nicht wieder aufwärmen

| Aussage | Wahrheit |
|---|---|
| „Der Wächter läuft nicht mehr" | Er läuft. Er hatte einen Commit übersprungen |
| „Sechs Blätter liegen bereit" | Es waren fünf. **AUF-90 hat kein Blatt, AUF-93 existiert gar nicht** |
| „Der Evaluator hat nichts getan" | Er hatte S4e um 22:31 abgenommen; der Reset um 22:36 fraß den Beleg |
| „Log wächst 10 KB/Minute" | Aus zwei Punkten gerechnet. Es wächst in Schüben |
| „Vorprüfung erwartet 0" | Nur beim ersten Merge |
| „22 Testdateien lesen `HausplanerApp.tsx`" | 16 direkt, 29 indirekt, 35 zusammen |
| „Die Fehlerzahl wächst nicht mehr, also ist es behoben" | Am 31.07. hat die Anwendung **null** echte Fehler geschrieben, weil sie **niemand benutzt hat**. Stille ist keine Gesundheit |
| „`MindestbreiteHinweis.tsx` liegt unter `components/`" | Er liegt unter `app/rahmen/`. **Mein Prüfbefehl zeigte 25 Stunden lang auf einen Pfad, den es nie gab** — siehe §6 Nr. 11 |
| „`FussUndUeberlagerungen` trägt keine offenen Stellen mehr" | **Sie trägt 12.** Der Satz stand im ersten Entwurf von AUF-38-P2 und ist beim Nachmessen vor dem Schneiden aufgefallen |

## 5. Zwei harte Regeln, die aus den Fehlern kommen

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist.**
*Auch gegen einen absichtlich ROTEN Fall.* `VORLAGE.md` Regel 9.
Gemessen: **vier von vier Blättern** trugen ein Kriterium, das nie hätte laufen können —
Prosa statt Befehl · Platzhalter ohne Dateinamen · ein Pfeil, den die Denylist wegfiltert ·
`| tail -4`, das `# pass`/`# fail` abschneidet und den Rückgabewert schluckt.

**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.**
*Was nicht committet ist, existiert nur, bis jemand `reset --hard` tippt.*
**Gerade offen:** 124 Zeilen des Prüfers liegen seit 09:45 uncommittet im Ledger.

## 6. Meine Fehler

```text
 1  vier von vier Blaettern trugen ein totes Kriterium
 2  KEIN Blatt fuetterte den Validator, den wir gebaut haben
 3  K-04 konnte nicht rot werden - und ging so durch eine Abnahme
 4  stat -c laeuft auf meiner Maschine, nicht auf Yamas Mac
 5  Ausschluss in AUF-38-P1 war mit dem eigenen Auftrag unvereinbar
 6  "der Waechter laeuft nicht mehr" - er lief
 7  "der Evaluator hat nichts getan" - er hatte abgenommen
 8  "10 KB/Minute" aus zwei Messpunkten
 9  AUF-93/AUF-90 acht Runden lang als bereitliegend gemeldet
10  den Agenten acht Dateien kopiert statt des Baums -> 11 von 15 Gegenpruefungen blind
11  AUF-91 stundenlang mit ls auf components/ geprueft - die Datei liegt in app/rahmen/.
    Der Befund "nicht gebaut" war bis 10:02 richtig, aber aus dem falschen Grund richtig.
```

**Neun von elf hat ein anderer gefunden, nicht ich.**
