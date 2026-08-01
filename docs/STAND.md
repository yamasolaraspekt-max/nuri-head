# STAND — das Arbeitsgedächtnis

> **Diese Seite wird ÜBERSCHRIEBEN, nicht angehängt.** Sie ist eine Seite lang und bleibt es.
>
> **Regeln:** eine Zeile je Sache · eine Rücknahme ERSETZT die Aussage · was erledigt ist,
> verschwindet · **kein Datum ohne Zahl, keine Zahl ohne Befehl.**

**Zuletzt geschrieben:** 01.08.2026, 19:55 · Planner

---

## 1. Wo der Bau steht — gemessen 19:5x

```text
main     d8612a63   gemergt 01.08. 19:0x, 80 Dateien, 7060 Zeilen, git diff 99f38da7 main LEER
Zweig    80ff1895   Locks 0

Kennzahl Produktivcode - `config` gehoert dazu, es fehlte bis 01.08.:
  git log -1 --date=format-local:'%d.%m. %H:%M' --pretty='%h %ad %s' \
    -- resources app tests routes public database scripts config
```

| Sache | Zustand |
|---|---|
| **Auf `main`** | Z-01 · AUF-38-P1+P2 · PB-043 T2 · AUF-91 · PB-047 · PB-023 — alle abgenommen |
| **Z-02** Fang angeschlossen | gebaut `8811e638`, **abgenommen** `211f3f91` |
| **Z-05** Konturwerkzeug | **gebaut** — `geometry/kontur.ts` existiert, `'kontur'` in `werkzeugArten.ts`. Votum offen |
| **AUF-38-P3** | beim Generator in Arbeit (Baum zeigt `FussUndUeberlagerungen.tsx`) |
| **Z-06** Decke aus Kontur | `gesperrt` — wird `bereit`, sobald Z-05 ein Votum hat |
| **AUF-38-P4+P5** · **Z-03+Z-04** | geschnitten, `bereit` |

## 2. Wer ist am Ball

- **Evaluator:** **Z-05** · mein Werkzeugbau von heute (S-06/S-07/S-08/S-09/S-10, `commit-pruefen.sh`) · AUF-25 (seit 25.07.) · AUF-86 · fehlende `.env.testing.example`.
- **Generator:** AUF-38-P3 in Arbeit, danach P4+P5, Z-03+Z-04, Z-06.
- **Yama:** Papierstopp · PB-042 · 18 dichteste Blades in den Browser? · **push** — 8 Commits liegen nur auf der Platte.
- **Planner:** Z-07…Z-11, PB-024-N1/N2 und die drei Werkzeug-Blätter — **bewusst erst, wenn die Schlange sie braucht** (S-06 meldet es).

## 3. Was entschieden ist — gilt, bis es hier ersetzt wird

| Entscheidung | Kurz |
|---|---|
| **Merge nur auf abgenommenen Stand** | Nicht auf die Zweigspitze. Am 01.08. blieben Z-02 und der Werkzeugbau bewusst draußen |
| **Ein Befehl je Nachricht** | Bei jedem Terminal-Vorgang. Yama, 30.07. 22:52 |
| **`studioDaten.ts` behält echte Farbwerte** | Konva löst keine CSS-Variable auf und malt sonst still die vorherige Farbe. K-01 von PB-023 deshalb gestrichen |
| **Stil-Brücken-Test** | Pflichtteil jeder Stil-Scheibe (F-15). P1 und P2: je 7 von 8 Mutationen kamen durch |
| **`kontur` statt `polygon`** | `polygon` gehört zu den sechs stillgelegten Zeichen-Primitiven |
| **Fangtoleranz aus dem Zoom** | 150 mm fest in Weltkoordinaten sind bei Zoom 0,02 drei Bildschirmpixel |
| **Commits über `scripts/commit-pruefen.sh`** | Prüft vor dem Commit, dass jeder Pfad existiert, geändert ist und syntaktisch trägt (F-14) |

## 4. ZURÜCKGENOMMEN — nicht wieder aufwärmen

| Aussage | Wahrheit |
|---|---|
| „Der Wächter läuft nicht mehr" | Er läuft. Er hatte einen Commit übersprungen |
| „Sechs Blätter liegen bereit" | Es waren fünf. AUF-90 hat kein Blatt, AUF-93 existiert nicht |
| „Log wächst 10 KB/Minute" | Aus zwei Punkten gerechnet. Es wächst in Schüben |
| „Vorprüfung erwartet 0" | Nur beim ersten Merge. Sie erwartet die Zahl der bisherigen Merges |
| „Die Fehlerzahl wächst nicht, also behoben" | Am 31.07. hat die Anwendung null Fehler geschrieben, weil sie niemand benutzt hat |
| „`FussUndUeberlagerungen` hat keine offenen Stellen" | Sie hatte 12 |
| „`MindestbreiteHinweis.tsx` liegt unter `components/`" | Unter `app/rahmen/` |
| „`sa-ui.blade.php` gibt es nicht" | Es gibt sie: `resources/views/admin/layouts/partials/` |
| „`zaehle.mjs` ist die Barriere" | Sie hatte selbst zwei Löcher — ein `//` in einer Zeichenkette machte Code unsichtbar |

## 5. Die Fehlerklassen — 15 von 16 haben eine Barriere

```text
✅ F-01 F-02 F-03 F-04 F-05 F-06 F-07 F-08 F-08b F-09 F-11 F-12 F-13 F-14 F-15
⚠  F-10  Lock-Reste - auf diesem Mount NICHT behebbar (`unlink` verboten), nur gemildert:
         commit-pruefen.sh raeumt sie im SELBEN Aufruf beiseite
```

**Die zwei harten Regeln bleiben:**

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist**, auch gegen einen
absichtlich ROTEN Fall. *Vier von vier Blättern trugen einmal ein Kriterium, das nie hätte laufen
können.*

**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.** *Was nicht committet ist,
existiert nur, bis jemand `reset --hard` tippt.*

## 6. Was der Validator heute selbst sperrt

```text
PB-019  Kopf fehlt oder misst nichts, Blatt aber `status: aktiv`   exit 1
S-01    genau EIN aktives Blatt
S-06    weniger als zwei baubare Blaetter (F-08)                   exit 1
S-07    ein Kriterium ist schon vor dem Bau erfuellt (F-07)        exit 1
S-08    Ausgangswert weicht von der Messung ab (F-04)              Meldung
S-09    Kopf ohne `status` (F-08b)
S-10    der Baum hat sich waehrend der Messung bewegt (F-03/F-12)  exit 1
```

**57 Zusagen über die beiden Werkzeuge, 0 fail.**
