# STAND — das Arbeitsgedächtnis

> **Diese Seite wird ÜBERSCHRIEBEN, nicht angehängt.** Sie ist eine Seite lang und bleibt es.
> Wer sie liest, weiß, wo wir stehen — ohne 1,7 MB Ledger zu lesen.
>
> **Warum es sie gibt** *(Yama, 31.07. 08:15)*: „euer größtes Problem ist, dass ihr
> Gedächtnisverlust habt."
>
> **Regeln für diese Datei:** eine Zeile je Sache · eine Rücknahme ERSETZT die Aussage, sie steht
> nicht darunter · was erledigt ist, verschwindet (es steht im Ledger) · **kein Datum ohne Zahl,
> keine Zahl ohne Befehl.**

**Zuletzt geschrieben:** 01.08.2026, 09:50 · Planner

---

## 1. Wo der Bau steht — gemessen 09:47 (01.08.)

```text
main                    39b18514   byte-identisch gemergt, Evaluator: 16 von 16 Commits, alle Gates gruen
Zweig                   b1da5194   21 Commits voraus   Baum: 1 Eintrag (siehe unten)   Locks: 0
letzter Commit          31.07. 10:28  ->  23 Stunden 19 Minuten her
letzter Produktivcode   31.07. 07:55  7a6c8112  (AUF-38-P1)
```

**Der Bau steht seit gestern früh still.** Generator: letzter Commit 31.07. 07:59 → **25 h 48**.
Evaluator: letzter Commit 31.07. 08:07 → **25 h 40**. **Der Prüfer läuft** — er hat um 09:45 heute
seine dritte Mahnung geschrieben (liegt noch uncommittet in `docs/handoff-status.md`, seine Zeilen,
nicht meine).

| Sache | Zustand |
|---|---|
| AUF-48 · Z-01 · M2 · AUF-38-P1 · PB-043 T2 | **fertig und abgenommen** (Einzelheiten im Ledger) |
| **PB-047** (CRM-Fehler) | **Blatt liegt seit 31.07. 01:00 — 32 h, gebaut 0.** `grep -oE 'user[?]-.name' … \| wc -l` → **1**. Die Datei ist seit 28.06. unberührt |
| **AUF-91** (Hinweis unter 1024 px) | **Blatt liegt seit 30.07. 22:17 — 35 h, gebaut 0.** `MindestbreiteHinweis.tsx` → **NEIN** |

## 2. Wer ist am Ball

- **Generator:** **PB-047**, dann **AUF-91**. Steht seit 25 h. **Ohne ihn geht nichts weiter.**
- **Evaluator:** AUF-25 (`17c8be22`, gebaut 25.07., **sieben Tage ohne Votum**) · Z-01/K-04 mit dem
  korrigierten Befehl nachziehen · AUF-86 (56 px) · fehlende `.env.testing.example`. Steht seit 25 h.
- **Yama:** **die beiden Instanzen anstoßen** — das ist der einzige Hebel, den es gerade gibt ·
  Papierstopp · PB-042 (Takt) · 18 dichteste Blades in den Browser?
- **Planner:** nichts Blockierendes. Schneidet erst wieder, wenn PB-047 und AUF-91 weg sind.
- **Prüfer:** misst weiter, schließt beide erst nach eigener Nachmessung.

## 3. Was entschieden ist — gilt, bis es hier ersetzt wird

| Entscheidung | Kurz |
|---|---|
| **Merge gesperrt** | Bis PB-047 und AUF-91 gebaut UND abgenommen sind. Prüfer, 00:44 |
| **Antwort auf die Mahnung des Prüfers** | **Ich ziehe beide Blätter NICHT zurück.** Sie sind geschnitten, validatorfest und richtig; ihnen fehlt ein Bauer, keine Begründung. Der Planner baut nicht und nimmt nicht ab — Rollentrennung |
| **Merge-Vorprüfung** | `rev-list auto..main` erwartet die **Zahl der bisherigen Merges**, nicht 0 |
| **Ein Befehl je Nachricht** | Bei jedem Terminal-Vorgang. Yama, 30.07. 22:52 |
| **PB-047: kein `(int)`** | `user?->name` → `user?->employeeId()`. Ein `(int)` macht aus einem Namen still `0` |
| **Stil-Brücken-Test** | Jede weitere Stil-Scheibe trägt ihn. Sonst kamen 7 von 8 Mutationen durch |
| **`style={bezeichner}`** | Nicht Gegenstand von P1/P2. Eigenes Blatt P3, das zuerst das Messwerkzeug erweitert |
| **21 Werkzeug-Spezifikationen** | 3 haltbar (`drehen` · `erkennung-bestaetigen` · `pv-modul`), 18 wackelig. Erst schneiden, wenn die liegenden Blätter weg sind |

## 4. ZURÜCKGENOMMEN — nicht wieder aufwärmen

| Aussage | Wahrheit |
|---|---|
| „Der Wächter läuft nicht mehr" | Er läuft. Er hatte einen Commit übersprungen |
| „Sechs Blätter liegen bereit" | Es waren fünf. **AUF-90 hat kein Blatt, AUF-93 existiert gar nicht** |
| „Der Evaluator hat nichts getan" | Er hatte S4e um 22:31 abgenommen; der Reset um 22:36 fraß den Beleg |
| „Log wächst 10 KB/Minute" | Aus zwei Punkten gerechnet. Es wächst in Schüben |
| „Vorprüfung erwartet 0" | Nur beim ersten Merge |
| „22 Testdateien lesen `HausplanerApp.tsx`" | 16 direkt, 29 indirekt, 35 zusammen |
| „Die Fehlerzahl wächst nicht mehr, also ist es behoben" | Am 31.07. hat die Anwendung **null** echte Fehler geschrieben, weil sie **niemand benutzt hat**. Stille ist keine Gesundheit — der TypeError wartet auf den ersten Nutzer |

## 5. Zwei harte Regeln, die aus den Fehlern kommen

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist.**
*Auch gegen einen absichtlich ROTEN Fall.* `VORLAGE.md` Regel 9.
Gemessen: **vier von vier Blättern** trugen ein Kriterium, das nie hätte laufen können —
Prosa statt Befehl · Platzhalter ohne Dateinamen · ein Pfeil, den die Denylist wegfiltert ·
`| tail -4`, das `# pass`/`# fail` abschneidet und den Rückgabewert schluckt.
**Letzteres ging so durch eine Abnahme.**

**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.**
Der Reset am 30.07. um 22:36 fraß **zwei Rollen ihre Voten**, weil sie nur im Arbeitsbaum lagen.
*Was nicht committet ist, existiert nur, bis jemand `reset --hard` tippt.*

## 6. Der Brückenausfall — 31.07. 17:40 bis 01.08. 09:47

**16 Stunden ohne Sicht auf das Repo** (Desktop-App zu). **Nichts verloren:** der Baum war beim
letzten Blick um 16:40 sauber und alles committet, und er ist es heute noch. Ich habe stündlich
einen einzigen Lesebefehl versucht und sonst nichts angefasst. *Der Unterschied zum Ausfall am
30.07. um 23:35: damals lag eine geschriebene, nicht committete Datei auf der Platte.*

## 7. Meine Fehler in der Nacht zum 31.07.

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
```

**Neun von zehn hat ein anderer gefunden, nicht ich.** *Das ist der Grund, warum die
Rollentrennung nicht Bürokratie ist — und der Grund, warum diese Seite existiert.*
