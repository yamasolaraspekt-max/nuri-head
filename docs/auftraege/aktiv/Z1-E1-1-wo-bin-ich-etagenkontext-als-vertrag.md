# Z1-E1-1 — Wo bin ich: der Etagen-Kontext wird ein Vertrag statt sechsmal abgeschrieben

**ZIEL:** Der Benutzer sieht **jederzeit** die aktive Etage und wechselt sie per Kürzel. Jedes
Werkzeug bezieht die Etage aus **genau einer** Funktion — **ohne stillen Rückfall**.

```yaml
auftrag: "Z1-E1-1"
scheibe: "E1 — Wo bin ich (Etagen-Kontext als Vertrag)"
spur: W
art: "Bedienung + SSOT. KEIN Modell, KEIN Schema."
heimat_app: ticket
heimat_code: resources/planner/hausplaner
mess_sha: 97c610ca
konzept: "docs/konzept/etagenweiser-aufbau.md @ 8e4bb918 (Dirigent, 19:1x) — Scheibe E1, Luecken L2 + L3"
kennung_geprueft: "Z1-E1-1: KEIN Blatt in docs/auftraege/aktiv/ (0 Dateien mit der Kennung im Namen).
                   Die 4 docs-Treffer sind Steuerungsspiegel und ein Befund, KEINE Blattvergabe —
                   ueber den DATEINAMEN gemessen, nicht ueber den Dateiinhalt.
                   Positivkontrolle Z1-E0-1: 10 docs-Treffer."
dor_beleg: "steht aus — plan-pruefer, ein Durchgang je Blatt (Dirigent 23:50:43)"
basis_sha: 97c610ca
prioritaet: P0
ballbesitz: "plan-pruefer (DoR)"
zielreifegrad: "ABGENOMMEN (BROWSER)"
```

## Die Lücken L2 und L3, am Stand `97c610ca` selbst nachgemessen

```
SECHS Stellen fallen still auf levels[0] zurueck — DAS KONZEPT NENNT FUENF:

  app/HausplanerApp.tsx:373      scene?.levels.find((l) => l.id === activeLevelId) ?? scene…
  app/ConfigWizard.tsx:228/229   store.activeLevelId ?? scene.levels[0]?.id   +  find(…) ??
  app/ConfigWizard.tsx:244/245   dieselbe Doppelung noch einmal
  store/hausplanerStore.ts:116   activeLevelId: scene.levels[0]?.id ?? null    <- NICHT IM KONZEPT

  Messmuster: grep -rn 'levels\[0\]' --include='*.ts' --include='*.tsx' . | grep -v __tests__
  Grundmenge: alle .ts/.tsx unter resources/planner/hausplaner, Tests ausgeschlossen

KRITERIUM (4) BRAUCHT SEINE GRUNDMENGE:
  levelId: level.id        ->  10     ROT-WERT (das woertliche Muster des Konzepts)
  levelId: activeLevelId   ->   0
  ALLE 'levelId:'          ->  39     GRUNDMENGE
  Das Konzept sagt "grep 'levelId: level.id' -> 0 Handschreibstellen". Rot ist 10, nicht
  unbestimmt — und die 39 gehoeren daneben, sonst liest jemand "0 von 39".

WAS ES SCHON GIBT UND WIEDERVERWENDET WIRD:
  app/ableitungen.ts                  EXISTIERT      <- aktiveEtage() gehoert hierhin
  aktiveEtage(                        0 Treffer      <- neu
  nachbar(                            1 Treffer      <- pruefen, ob wiederverwendbar
  app/tools/vorbedingungen.ts:84      FAEHIGKEIT_GESCHOSS_DA = 'activeLevel.exists'
  app/tools/vorbedingungen.ts:136-137 faehigkeit(…, 'Kein aktives Geschoss.')
  app/tools/werkzeugVertrag.ts        42 von 113 Werkzeugen fordern 'activeLevel.exists'
  app/GuidedView.tsx:99               Chip-Muster (heute hart beschriftet)
  PageUp/PageDown                     0 Treffer      <- Kuerzel ist neu
```

> **Der Ablehnungsgrund ist gebaut, und 42 Werkzeuge berufen sich bereits darauf.** *E1 baut ihn
> nicht — E1 macht ihn wirksam.* Das gehört ins Blatt, damit niemand eine zweite Fähigkeit daneben
> anlegt.

### Verschärfung gegenüber dem Konzept (mit Begründung, wie erlaubt)

**`store/hausplanerStore.ts:116` fehlt in der Lückenliste L2 — und es ist die Stelle, die den
Rückfall überhaupt erst erzeugt:** sie setzt `activeLevelId` beim Laden. *Wer nur die fünf
genannten umhängt, lässt die Quelle stehen.* **Die sechste Stelle ist Teil von Kriterium (a).**

Die ConfigWizard-Zeilen des Konzepts (`228`, `244`) **treffen am heutigen Stand exakt** — eine
frühere Messung von mir nannte `175/191`; **die war an einem 275 Commits alten Baum genommen und
ist zurückgezogen.**

### Zwei Schreibweisen, aber eine saubere Grenze — kein Befund

*Vom Plan-Prüfer in §509 gemessen und hier festgehalten, damit die DoR nicht darüber stolpert:*
`activeLevel` (englisch) steht auf der App-Ebene, `aktivesLevelId` (deutsch) **nur** in
`renderers/three-d/szene.ts` als privates Feld und Parameter. **Das ist eine Schichtgrenze, keine
zweite Wahrheit** — die App spricht englisch, der Renderer hält den Wert privat. *E1 vereinheitlicht
das nicht.*

## Abnahmekriterien (aus dem Konzept; Messbefehle ergänzt, nichts abgeschwächt)

- **Z1-E1-1-a** · **EINE FUNKTION, KEIN STILLER RÜCKFALL — UND DIE QUELLE IST DABEI.**

  **Verlangt:** `aktiveEtage(scene, activeLevelId)` in `app/ableitungen.ts`. **Alle sechs**
  `levels[0]`-Rückfälle entfernt — **einschließlich `store/hausplanerStore.ts:116`**. Wo keine
  aktive Etage bestimmbar ist, steht die **Ablehnung mit Grund** (`vorbedingungen.ts:137`,
  „Kein aktives Geschoss."), **nicht** ein Rückfall auf die erste Etage.

  **Messbefehl:**
  ```
  cd resources/planner/hausplaner
  grep -rn 'levels\[0\]' --include='*.ts' --include='*.tsx' . | grep -v __tests__   ->  0
  grep -rn 'aktiveEtage(' --include='*.ts' --include='*.tsx' . | grep -v __tests__  ->  >= 7
                                                    (1 Definition + mind. 6 Aufrufer)
  Positivkontrolle: dasselbe Muster auf 'dickeMm' -> 52 (nicht 0)
  ```

  **Heutiges (rotes) Ergebnis:** `levels[0]` → **6** · `aktiveEtage(` → **0**.

  **Absage-Regel:** Ein Rückfall, der nur **verschoben** wird — etwa in eine Hilfsfunktion, die
  intern weiter `levels[0]` nimmt — erfüllt (a) **nicht**. *Gemessen wird das Muster, nicht der Ort.*

- **Z1-E1-1-b** · **`levelId: level.id` FÄLLT AUF NULL — VON HEUTE 10, GRUNDMENGE 39.**

  **Verlangt:** Kein Werkzeug schreibt die `levelId` mehr von Hand aus einer lokalen `level`-Variable;
  sie kommt aus der einen Funktion.

  **Messbefehl:**
  ```
  grep -rn 'levelId: level\.id' --include='*.ts' --include='*.tsx' . | grep -v __tests__  ->  0
  grep -rn 'levelId:'           --include='*.ts' --include='*.tsx' . | grep -v __tests__  ->  39
                                                    (Grundmenge — MUSS gleich bleiben)
  ```

  **Heutiges (rotes) Ergebnis:** **10 von 39**.

  **Absage-Regel:** Sinkt die **Grundmenge** 39 mit, wurden Zuweisungen gelöscht statt umgehängt —
  das erfüllt (b) **nicht**.

- **Z1-E1-1-c** · **DER CHIP IST BEI JEDEM ZOOM SICHTBAR, IN 2D UND IN 3D.**

  **Verlangt:** Ein Chip „Erdgeschoss · ±0 mm" nach dem Muster `app/GuidedView.tsx:99`, sichtbar
  **in beiden Ansichten** und **bei jedem Zoomgrad**.

  **Messbefehl:**
  ```
  Browser, headful (Puppeteer/Chrome — WebGL): 2D und 3D, je min./max. Zoom
  Bildbeleg je Ansicht und Zoomstufe
  ```

  **Heutiges (rotes) Ergebnis:** Kein Etagen-Chip im Canvas — `GuidedView.tsx:99` trägt die
  **fest verdrahtete** Beschriftung, nicht die aktive Etage.

- **Z1-E1-1-d** · **BILD↑ / BILD↓ WECHSELT DIE ETAGE, DER FOKUS BLEIBT.**

  **Verlangt:** Kürzel wechselt die aktive Etage, **der Tastaturfokus bleibt stehen**, der Chip
  aktualisiert sich.

  **Messbefehl:**
  ```
  grep -rn 'PageUp\|PageDown' --include='*.ts' --include='*.tsx' . | grep -v __tests__  ->  >= 1
  Browser: Fokus auf Canvas, Bild-hoch/Bild-runter, Chip liest die neue Etage
  am obersten/untersten Geschoss: kein Sprung, kein Fehler
  ```

  **Heutiges (rotes) Ergebnis:** `PageUp`/`PageDown` → **0 Treffer**.

- **Z1-E1-1-e** · **OHNE AKTIVE ETAGE LEGT KEIN WERKZEUG ETWAS AN — MIT SICHTBAREM GRUND.**

  **Verlangt:** Die vorhandene Fähigkeit `activeLevel.exists` **wirkt**. Rot-Probe: ohne aktive
  Etage bleibt die Bühne leer und der Grund **„Kein aktives Geschoss."** steht sichtbar.

  **Messbefehl:**
  ```
  grep -c 'activeLevel.exists' app/tools/werkzeugVertrag.ts   ->  42 (Grundmenge, bleibt)
  Browser: Zustand ohne aktive Etage herstellen, ein Werkzeug der 42 anklicken
           -> nichts entsteht, Grundtext sichtbar, Bildbeleg
  ```

  **Heutiges (rotes) Ergebnis:** Die Fähigkeit ist **deklariert** (42 Werkzeuge) und der Grundtext
  **gebaut** (`vorbedingungen.ts:137`) — **erzwungen wird sie nicht**, weil der Rückfall auf
  `levels[0]` den Zustand „keine aktive Etage" nie entstehen lässt.

  > **Das ist der Kern von E1.** *Eine Ablehnung, die nie ausgelöst werden kann, ist keine
  > Ablehnung.* **Erst wenn (a) den Rückfall entfernt, kann (e) überhaupt rot werden.**

  **Absage-Regel** *(Halbsatz 2026-08-23T00:0x — **Verschärfung** nach dem ausdrücklichen
  Vorschlag des Plan-Prüfers in §513; DoR bleibt gültig, Nachtrag 1.5, kein neuer Durchgang)*:
  **Eine Prüfung, die nicht im selben Pfad liegt wie die Befehls-Absetzungen in
  `app/HausplanerApp.tsx`, erfüllt (e) nicht.**

  > **Warum diese Regel nötig ist — selbst nachgemessen am Stand `97c610ca`:**
  > ```
  > type: 'ADD_  in app/HausplanerApp.tsx  ->  6 Absetzungen
  >   :949 ADD_NODE · :992 ADD_NODE · :1052 ADD_ROOF · :1080 ADD_FOUNDATION_SLAB
  >   :1120 ADD_CEILING · :1150 ADD_NODE
  > regelnFuer · heuteUnerfuellbar · activeLevel.exists  ->  je 0 in dieser Datei
  > FAEHIGKEIT_GESCHOSS_DA  ->  1 Treffer, und der ist die IMPORTZEILE (:87)
  > handlungZuGrund         ->  1 Treffer, und der ist die IMPORTZEILE (:48)
  > Verwendung innerhalb 40 Zeilen vor einer Absetzung:  KEINE
  > Positivkontrolle: 'ADD_' in derselben Datei -> 7 (die 7. ist ein Kommentar, :1116)
  > ```
  > **Die Datei importiert die Fähigkeit und benutzt sie nie.** *Wer nur zählt, ob
  > `vorbedingungen` in `HausplanerApp.tsx` vorkommt, bekommt 2 und hält den Pfad für geprüft —
  > beide Treffer sind Importe.* **Ort ist nicht Wirkung.**
  >
  > **Ohne diese Regel ist (e) formal erfüllbar und sachlich verfehlt:** ein Prüfaufruf
  > irgendwo im Code erfüllt „`activeLevel.exists` wirkt" auf dem Papier, und an den sechs
  > Absetzungen ändert sich nichts.

  > **Eine Zahl des Plan-Prüfers habe ich berichtigt, die Sache trägt trotzdem:** er nennt
  > **sieben** `ADD_`-Aufrufe. Es sind **sechs Absetzungen** — der siebte Treffer ist ein
  > Kommentar (`:1116`). *Sein tragender Befund — null Prüfungen im Klick-Pfad — ist damit
  > nicht schwächer, sondern genauer belegt.*

- **Z1-E1-1-f** · **`tsc` 0, Suite grün, Bündel in der Lieferung.**

  **Messbefehl:** `npm run build:hausplaner` · `npm run test:hausplaner` (beide hinter dem
  Schema-Tor `scripts/hausplaner-schema.mts`).

  **Heutiger Wert:** **grün am Ausgangsstand** — *und genau deshalb ein Kriterium: (f) ist kein
  Fortschrittsmaß, sondern die Zusage, dass die Scheibe nichts zerbricht.*

- **Z1-E1-1-g** · **KEIN MODELL-DIFF.**

  **Messbefehl:**
  ```
  git diff --stat <basis>..<ergebnis> -- resources/planner/hausplaner/domain/ \
      resources/planner/hausplaner/**/scene-document-v2.schema.json          ->  LEER
  ```

  **Heutiger Wert:** **leer** (basis = ergebnis am Ausgangsstand). *Ein Schutzkriterium ist am
  ersten Tag immer erfüllt — es misst, was der Bau **nicht** tun darf.*

  **Absage-Regel:** Jede Zeile in `domain/` erfüllt (g) **nicht** — E1 ist eine Bedien- und
  Ableitungsscheibe.

## Nicht-Ziele

- **Der „Geist" der Nachbaretage** (Konzept L3, zweiter Teil) — gehört ausdrücklich zu **E5**.
- **Vereinheitlichung `activeLevel` / `aktivesLevelId`** — Schichtgrenze, kein Befund (siehe oben).
- **Etage einfügen/löschen/duplizieren** — **E2** und **E5**.

## Nachvollzugs-Matrix (§5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| a eine Funktion, 6 Rückfälle weg | AP-1 `aktiveEtage()` + umhängen | n.U. | n.U. |
| b `levelId: level.id` 10 → 0 (von 39) | AP-1 umhängen | n.U. | n.U. |
| c Chip in 2D + 3D, jeder Zoom | AP-2 Canvas-Chip | n.U. | n.U. |
| d Bild↑/↓, Fokus bleibt | AP-2 Kürzel | n.U. | n.U. |
| e Ablehnung wird wirksam | AP-1 + Rot-Probe | n.U. | n.U. |
| f `tsc`/Suite/Bündel | AP-3 Lieferung | n.U. | n.U. |
| g kein Modell-Diff | AP-3 Schutzbeleg | n.U. | n.U. |

## Rückweg

Ein Commit zurück. **Kein Schema, kein Modell, keine Migration** — der Rückweg ist vollständig,
solange (g) hält. *Fällt (g), ist der Rückweg nicht mehr ein Commit — deshalb ist (g) ein
Kriterium und keine Empfehlung.*
