# STAND — das Arbeitsgedächtnis

> **Diese Seite wird ÜBERSCHRIEBEN, nicht angehängt.** Sie ist eine Seite lang und bleibt es.
> Wer sie liest, weiß, wo wir stehen — ohne 1,7 MB Ledger zu lesen.
>
> **Warum es sie gibt** *(Yama, 31.07. 08:15)*: „euer größtes Problem ist, dass ihr
> Gedächtnisverlust habt." Der Ledger ist append-only und auf **1 770 052 Byte** gewachsen.
> Eine zurückgenommene Aussage bleibt dort stehen, die Richtigstellung steht 800 Zeilen später,
> und niemand liest beides. **Hier steht nur, was JETZT gilt.**
>
> **Regeln für diese Datei:** eine Zeile je Sache · eine Rücknahme ERSETZT die Aussage, sie steht
> nicht darunter · was erledigt ist, verschwindet (es steht im Ledger) · **kein Datum ohne Zahl,
> keine Zahl ohne Befehl.**

**Zuletzt geschrieben:** 31.07.2026, 10:30 · Planner

---

## 1. Wo der Bau steht

```text
main                    39b18514   byte-identisch gemergt, Evaluator: 16 von 16 Commits, alle Gates gruen
Zweig                   9297b857   20 Commits voraus
laravel.log             229 327 885 Byte
```

| Sache | Zustand |
|---|---|
| AUF-48 (Hausplaner-Zerlegung) | **fertig**, acht Scheiben, alle abgenommen, `HausplanerApp.tsx` 2511 → 1155 |
| Z-01 (langer Strich) | **fertig und grün**. Ein Ort beendet Werkzeuge, Canvas-Verlassen pausiert |
| M2 (`scripts/bestand.sh`) | **fertig**, quittiert |
| AUF-38-P1 (Panel-Stile) | **gebaut + Evaluator grün** (08:07). Panel 71/37 → 34/0, global 195/77 → 158/40 |
| PB-043 Teil 2 (Log-Rotation) | **gebaut** (07:59), Prüfer: vollständig erledigt (08:02) |
| PB-047 (CRM-Fehler) | **Blatt liegt, nicht gebaut.** `grep user?->name` → **1** |
| AUF-91 (Hinweis unter 1024 px) | **Blatt liegt, nicht gebaut.** `MindestbreiteHinweis.tsx` existiert **nicht** |

## 2. Wer ist am Ball

- **Generator:** **PB-047** (laufender CRM-Fehler, 464 Mal seit 07.07.) → dann **AUF-91**.
- **Evaluator:** **AUF-25** (`17c8be22`, gebaut 25.07., **sechs Tage ohne Votum**) · **Z-01/K-04 mit
  dem korrigierten Befehl nachziehen** · AUF-86 (56 px) · fehlende `.env.testing.example`.
- **Yama:** Papierstopp · PB-042 (Takt) · **sollen die 18 dichtesten Blades in den Browser?**
- **Planner:** nichts Blockierendes. Schneidet erst wieder, wenn PB-047 und AUF-91 weg sind.

## 3. Was entschieden ist — gilt, bis es hier ersetzt wird

| Entscheidung | Kurz |
|---|---|
| **Merge gesperrt** | Bis PB-047 und AUF-91 gebaut UND abgenommen sind. Prüfer, 00:44. PB-043 T2 ist inzwischen erledigt |
| **Merge-Vorprüfung** | `rev-list auto..main` erwartet die **Zahl der bisherigen Merges**, nicht 0. Jeder davon muss zwei Eltern haben |
| **Ein Befehl je Nachricht** | Bei jedem Terminal-Vorgang. Yama, 30.07. 22:52 |
| **PB-047: kein `(int)`** | `user?->name` → `user?->employeeId()`. Ein `(int)` macht aus einem Namen still `0` — falscher Zähler statt leerer |
| **Stil-Brücken-Test** | Jede weitere Scheibe, die Inline-Stile in Klassen verlagert, trägt ihn. Sonst kamen 7 von 8 Mutationen durch |
| **`style={bezeichner}`** | Nicht Gegenstand von AUF-38-P1/P2. Eigenes Blatt P3, das zuerst das Messwerkzeug erweitert |
| **21 Werkzeug-Spezifikationen** | 3 haltbar (`drehen` · `erkennung-bestaetigen` · `pv-modul`), 18 wackelig. **Werden erst geschnitten, wenn die liegenden Blätter weg sind** |

## 4. ZURÜCKGENOMMEN — nicht wieder aufwärmen

*Wer eine dieser Aussagen irgendwo im Ledger findet: sie gilt nicht mehr.*

| Aussage | Wahrheit |
|---|---|
| „Der Wächter läuft nicht mehr" | Er läuft. Er hatte einen Commit übersprungen |
| „Sechs Blätter liegen bereit" | Es waren fünf. **AUF-90 hat kein Blatt, AUF-93 existiert gar nicht** |
| „Der Evaluator hat nichts getan" | Er hatte S4e um 22:31 abgenommen; der Reset um 22:36 fraß den Beleg |
| „Log wächst 10 KB/Minute" | Aus zwei Punkten gerechnet. Es wächst in Schüben |
| „Vorprüfung erwartet 0" | Nur beim ersten Merge |
| „22 Testdateien lesen `HausplanerApp.tsx`" | 16 direkt, 29 indirekt, 35 zusammen. Die 22 galt für eine 2308-Zeilen-Datei |

## 5. Zwei harte Regeln, die aus den Fehlern kommen

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist.**
*Auch gegen einen absichtlich ROTEN Fall.* `VORLAGE.md` Regel 9.
Gemessen: **vier von vier Blättern** trugen ein Kriterium, das nie hätte laufen können —
Prosa statt Befehl · Platzhalter ohne Dateinamen · ein Pfeil, den die Denylist wegfiltert ·
`| tail -4`, das `# pass`/`# fail` abschneidet und den Rückgabewert schluckt.
**Letzteres ging so durch eine Abnahme.**

**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.**
Der Reset am 30.07. um 22:36 fraß **zwei Rollen ihre Voten**, weil sie nur im Arbeitsbaum lagen.
Der Generator saß am 31.07. **sieben Stunden** auf uncommittetem Stand.
*Was nicht committet ist, existiert nur, bis jemand `reset --hard` tippt.*

## 6. Der erste Eintrag: meine Fehler in der Nacht zum 31.07.

*Steht hier, weil eine Liste ehrlicher ist als eine Zusage — und weil sie das Muster zeigt.*

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
Rollentrennung nicht Bürokratie ist — und der Grund, warum diese Seite existiert: damit derselbe
Fehler nicht dreimal gefunden werden muss.*
