# STAND — das Arbeitsgedächtnis

> **Diese Seite wird ÜBERSCHRIEBEN, nicht angehängt.** Sie ist eine Seite lang und bleibt es.
>
> **Regeln:** eine Zeile je Sache · eine Rücknahme ERSETZT die Aussage · was erledigt ist,
> verschwindet · **kein Datum ohne Zahl, keine Zahl ohne Befehl.**

**Zuletzt geschrieben:** 01.08.2026, 19:40 · Planner
*(Die Vorfassung trug „19:55" — ein Zeitstempel, der 30 Minuten in der Zukunft lag. Zurückgenommen.)*

---

## 1. Wo der Bau steht — gemessen 19:37

```text
main     d8612a63   19:01 · git diff 99f38da7 main LEER · 3 Merges
Zweig    86090de8   auto/hausplaner-integration · 18 voraus vor main · Locks 0
UNGEPUSHT 48        <- kein Backup ausserhalb der Platte

  TZ=Europe/Berlin date '+jetzt %d.%m.%Y %H:%M'
  git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration
  git --no-optional-locks rev-list --count main..auto/hausplaner-integration

Kennzahl Produktivcode - `config` gehoert dazu:
  git log -1 --date=format-local:'%d.%m. %H:%M' --pretty='%h %ad %s' \
    -- resources app tests routes public database scripts config
  -> bbd4be07  01.08. 19:21  AUF-38-P3
```

| Sache | Zustand |
|---|---|
| **Auf `main`** | Z-01 · AUF-38-P1+P2 · PB-043 T2 · AUF-91 · PB-047 · PB-023 — abgenommen |
| **AUF-38-P3** | **abgenommen** — gebaut `bbd4be07` 19:21, Votum GRÜN `fa1da402` 19:37 |
| **Z-02** Fang angeschlossen | gebaut `8811e638`, abgenommen `211f3f91` |
| **Z-05** Konturwerkzeug | **gebaut** `264ab9dc` 19:09 · Votum offen · L-01 nach Z-05-N1 verschoben |
| **Z-05-N1** Werkzeug erreichbar | **aktiv**, geschnitten `e061e017` 19:36 — das EINE aktive Blatt |
| **AUF-38-P4+P5** · **Z-03+Z-04** | `bereit` — zusammen mit Z-05-N1 sind 3 baubar (R16 verlangt 2) |
| **Z-06** Decke aus Kontur | `gesperrt` — braucht Z-05 mit Votum UND Z-05-N1 |

## 2. Wer ist am Ball

- **Generator:** **Z-05-N1** — der achte Registry-Platz für `kontur`. Danach P4+P5, Z-03+Z-04.
- **Evaluator:** **Z-05** (nur noch K-01…K-06, L-01 ist heraus) · mein Werkzeugbau vom 01.08. · AUF-25 (seit 25.07.) · AUF-86 · fehlende `.env.testing.example`.
- **Yama:** **push (48)** · Papierstopp PB-042 · PB-048 · die 3 PHP-Dateien unten.
- **Planner:** Z-07…Z-11 und die drei Werkzeug-Blätter — **erst, wenn S-06 sie meldet.**

## 3. Was offen im Baum liegt — Regel B ist verletzt

```text
git --no-optional-locks status --short --branch
```

| Datei | seit | wem |
|---|---|---|
| `app/Http/Controllers/DatanormController.php` · `app/Models/ProductImage.php` · `routes/web.php` | 18:56 | **Yamas eigene**, 01.08. bestätigt — bleiben liegen, niemand fasst sie an |
| `docs/planner/PRUEFER-BEFUNDE.md` | **13:04** | Prüfer — 6 h uncommittet. Fremder Pfad, der Planner stagt ihn nicht |
| `docs/product-data/` | — | unverfolgt, von keiner Rolle angekündigt |

## 4. Was entschieden ist — gilt, bis es hier ersetzt wird

| Entscheidung | Kurz |
|---|---|
| **Z-05-N1 vor Z-06** | Yama, 01.08. Ohne Erreichbarkeit gibt es nie eine gezeichnete Kontur — der Kontur-Zweig in Z-06 wäre toter Code |
| **Bilanz getrennt, nicht hochgezählt** | `PAKET 110 + EIGENE.length`, nicht die nackte `111`. Eine nackte 111 verliert die Verlusterkennung |
| **`kontur` ersetzt nicht `polygon`** | `polygon` bleibt bei den sechs stillgelegten Primitiven in `03-zeichnen-cad` |
| **Merge nur auf abgenommenen Stand** | Nicht auf die Zweigspitze |
| **Ein Befehl je Nachricht** | Bei jedem Terminal-Vorgang. Yama, 30.07. 22:52 |
| **`studioDaten.ts` behält echte Farbwerte** | Konva löst keine CSS-Variable auf und malt sonst still die vorherige Farbe |
| **Fangtoleranz aus dem Zoom** | 150 mm fest sind bei Zoom 0,02 drei Bildschirmpixel |
| **Commits über `scripts/commit-pruefen.sh`** | Prüft Existenz, Änderung und Syntax vor dem Commit (F-14) |

## 5. ZURÜCKGENOMMEN — nicht wieder aufwärmen

| Aussage | Wahrheit |
|---|---|
| „8 Commits liegen nur auf der Platte" / „13" | **48**, gemessen 19:37 gegen `fork/…` ohne fetch — mehr kann es sein, weniger nicht |
| „Zweig 80ff1895" | war schon beim Schreiben überholt |
| „AUF-38-P3 ist beim Generator in Arbeit" | gebaut 19:21, abgenommen 19:37 |
| „Z-05 ist gebaut, Votum offen" | stimmt — aber L-01 kam **blockiert** zurück, das fehlte |
| „Der Wächter läuft nicht mehr" | Er läuft. Er hatte einen Commit übersprungen |
| „Vorprüfung erwartet 0" | Sie erwartet die Zahl der bisherigen Merges |
| „Die Fehlerzahl wächst nicht, also behoben" | Am 31.07. hat niemand die Anwendung benutzt |
| „`zaehle.mjs` ist die Barriere" | Sie hatte selbst zwei Löcher |

## 6. Neue Fallen vom 01.08. abends

```text
git worktree list meldet vom Mount aus JEDEN Worktree als `prunable` - auch ../ticket-main -
   weil die /Users/...-Pfade aus der Geraete-VM nicht aufloesen.
   -> `git worktree prune` von hier aus meldet den Merge-Worktree ab. NIE von hier ausfuehren.
S-07 feuert nur bei `kritikalitaet: P1`. Ein absence-Kriterium ohne P1 kommt durch, auch wenn es
   schon vor dem Bau erfuellt ist. Wer sich darauf verlaesst, umgeht die Barriere versehentlich.
Vier Backslashes in einem YAML-Befehl sind zwei zu viel: "\\\\{" wird zur Regex \\{ und misst 0
   statt 1 (Z-03+Z-04 K-01, 01.08. behoben).
5 Stashes vom 07.07.-24.07. liegen unerwaehnt: git stash list
`| tail -n` schluckt den Exitcode - auch beim Validator. Er wurde am 01.08. so gefahren.
```

## 7. Die Fehlerklassen — 15 von 16 haben eine Barriere

```text
✅ F-01 F-02 F-03 F-04 F-05 F-06 F-07 F-08 F-08b F-09 F-11 F-12 F-13 F-14 F-15
⚠  F-10  Lock-Reste - auf diesem Mount NICHT behebbar (`unlink` verboten), nur gemildert
```

**Die zwei harten Regeln bleiben:**

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist**, auch gegen einen
absichtlich ROTEN Fall. *Z-05-N1 hat für jeden Zähler einen Partner mit Treffer ≠ 0.*

**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.** *Siehe Abschnitt 3 — die
Regel ist gerade verletzt, und zwar sichtbar.*

## 8. Was der Validator selbst sperrt

```text
PB-019  Kopf fehlt oder misst nichts, Blatt aber `status: aktiv`   exit 1
S-01    genau EIN aktives Blatt            -> heute: Z-05-N1
S-06    weniger als zwei baubare Blaetter                          exit 1  -> heute: 3
S-07    ein Kriterium ist schon vor dem Bau erfuellt (nur bei P1)  exit 1
S-08    Ausgangswert weicht von der Messung ab                     Meldung
S-09    Kopf ohne `status`
S-10    der Baum hat sich waehrend der Messung bewegt              exit 1
```

**57 Zusagen über die beiden Werkzeuge, 0 fail.**
