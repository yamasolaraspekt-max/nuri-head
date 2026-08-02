# STAND — das Arbeitsgedächtnis

> **Diese Seite wird ÜBERSCHRIEBEN, nicht angehängt.** Sie ist eine Seite lang und bleibt es.
>
> **Regeln:** eine Zeile je Sache · eine Rücknahme ERSETZT die Aussage · was erledigt ist,
> verschwindet · **kein Datum ohne Zahl, keine Zahl ohne Befehl.**

**Zuletzt geschrieben:** 02.08.2026, 12:0x · Planner

---

## 1. Wo der Bau steht — gemessen 11:59

```text
HEAD      8dedfe6d   02.08. 11:59   auto/hausplaner-integration
UNGEPUSHT 8          fork..auto/hausplaner-integration
VOR MAIN  101        main..auto/hausplaner-integration

  TZ=Europe/Berlin git --no-optional-locks log -1 --date=format-local:'%d.%m. %H:%M' --pretty='%h %ad %s'
  git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration
  git --no-optional-locks rev-list --count main..auto/hausplaner-integration
```

**Blätter nach Status — `grep -rh '^  status:' docs/auftraege/ | sed 's/#.*//' | sort | uniq -c`:**

```text
aktiv 1 · bereit 4 · gebaut 4 · entwurf 6 · gesperrt 5 · ruht 15 · abgenommen 4 · zurueckgestellt 1
```

**S-06 zählt je LAUF, nicht global** — im Strang `hausplaner-3d` ist die Zahl **0 baubar**.
*Es stockt nicht der Bau, es stockt die Abnahme.*

## 2. Die Schlange — wer was hat

| Rolle | Was liegt bereit |
|---|---|
| **Generator** | **Z-03+Z-04** `aktiv` → **W-01** `bereit` → (Z-11 · W-05 · W-06 nach Gegenlesen) |
| **Evaluator** | **Z-05 — VORRANG, das Votum öffnet Z-06 und damit die Zwischendecke** · Z-10 · AUF-38-P4+P5 · W-02 |
| **Prüfer** | **GEGENLESEN (B8): Z-11 · W-05 · W-06** — das ist der Engpass · dann P-01 Teil 0 |
| **Planner** | W-07 (Erlaubnislücken node/`sed -i`/awk) · S-11 (Anker-Sperre) · Umzug der Blätter nach `docs/auftraege/<strang>/` |
| **Yama** | Y1 (Push-Kanal) · Y2 (Takt) · Z-09 ACHSE oder FLANKE · die 3 PHP-Dateien |

**Gebaut, wartet auf Votum:** Z-05 · Z-10 · W-02 · AUF-38-P4+P5.
**Abgenommen:** Z-01 · Z-02 · Z-05-N1 · AUF-38-P1+P2+P3 · AUF-91 · PB-023 · PB-043 T2 · PB-047.
**Gesperrt:** Z-06 (nur noch: Votum für Z-05) · AUF-83-T2T3 · AUF-83-T5 · AUF-88-P1.

**Ohne Ausnahme:** kein Push (Y1 offen) · keine Fachentscheidung (Tor 1) · kein Merge/Tag/`--force`
(Tor 2) · die drei PHP-Dateien im Baum sind Yamas.

## 3. Was am 02.08. entschieden wurde

```text
5163cac2  L-01-Anker v3 in 6 Blaettern. Stufe 2 = Expertenmodus KLICKEN (Pruefer-Befund),
          kein Projekt oeffnen. Startzustand ist kein roter Befund.
          NICHT uebernommen: `#hausplaner-scene mit 0 Kindern` - das Element ist ein
          <script type="application/json"> und hat NIE Element-Kinder (studio.blade.php:93).
dca1d824  Z-11 K-06 nannte `toleranz(art, zoom)` - gibt es nicht. Echt: `toleranzAusZoom(zoom,
          fangPx = FANG_PX)`, fangKern.ts:230. Dabei gefunden: bei zoom = 0 gibt sie fangPx
          zurueck OHNE zu teilen - dort faellt ein naiv eingebauter Faktor heraus.
dca1d824  W-05 K-10: `PAKET_WERKZEUGE` ist zweimal deklariert - werkzeugPaket.ts:34 als LISTE
          (101 Eintraege), toolRegistry.ts:272 als ZAHL 110. Die 110 ist nicht das Paket,
          sondern 9 Grundeintraege + 101. Die Bilanz stimmt heute durch Zufall.
c8d82b70  W-06 geschnitten: `klammerBilanz` zaehlt ueber Kommentare mit - fangKern.ts hat
          81 `(` zu 87 `)`, alle sechs aus `// 1)`-Kommentaren. Das Werkzeug, das B6 als
          Ersatz fuer `perl -pi` benennt, kann .ts gar nicht schreiben.
```

## 4. Was entschieden ist — gilt, bis es hier ersetzt wird

> **`docs/BESCHLUSS-fehlervermeidung.md` steht ÜBER dieser Tabelle.** **B8 gilt ohne Werkzeug:
> ein Blatt wird nicht `bereit`, bevor eine andere Rolle es gegengelesen hat** —
> `gegengelesen_von`, `gegengelesen_am`, `befund`. **Der Planner nimmt sich davon nicht aus.**

| Entscheidung | Kurz |
|---|---|
| **Allowlist statt Textmuster** | W-01. Nicht aufzählen, was verboten ist, sondern was erlaubt ist |
| **Publizierende Befehle nie in ein Blatt** | Was darin steht, PASSIERT |
| **Zahlen rechnen, nicht tippen** | `PAKET + EIGENE.length`; W-05 K-10 zieht das jetzt gerade |
| **Ein Kommentar-Abzug, nicht zwei** | W-06 ruft `ohneKommentare()` aus `zaehle.mjs` |
| **Ein Befehl je Nachricht** | Bei jedem Terminal-Vorgang. Yama, 30.07. 22:52 |
| **Commits über `scripts/commit-pruefen.sh`** | Prüft Existenz, Änderung und Syntax vor dem Commit |

## 5. ZURÜCKGENOMMEN — nicht wieder aufwärmen

| Aussage | Wahrheit |
|---|---|
| „60 Commits ungepusht" | **13** damals, **8** heute — eine Zahl ohne Befehl |
| „Der Verzeichnislauf braucht 3,6 s" | **39,4 s / 30 Blätter** (Evaluator) |
| „Der Push liegt beim Evaluator" / „beim Planner" | **Beides nicht belegt.** Aus der Planner-Umgebung ist GitHub nicht erreichbar (`git ls-remote fork` → HTTP 403 vom Proxy) |
| „Ein Werkzeug erreichbar machen kostet vier Stellen" | **Drei von vier sind schon da**, nur die Registry fehlt (W-05) |
| „Im Startzustand ist canvas 0, also muss ein Projekt geöffnet werden" | **Nein** — der Expertenmodus montiert die Bühne ohne Projekt (Prüfer, 02.08.) |
| „`#hausplaner-scene` mit 0 Kindern zeigt den Startzustand" | **Nein** — ein `<script>`-Element hat nie Element-Kinder. Es taugt nur als EXISTENZ-Zeichen |

## 6. Fallen

```text
Ein `OK` im Validator-Bericht heisst: der Befehl LIEF. Wer nur nach FEHLSCHLAG filtert, sieht
   nicht, was ausgefuehrt wurde.
`| tail -n` schluckt den Exitcode - auch beim Validator.
`grep -c` liefert exit 1 bei NULL Treffern. In Kriterien immer `grep -o … | wc -l`.
`2>/dev/null` gilt der Denylist als Umleitung. Ersatz: `ls <verz> | grep <name> | wc -l`.
git worktree list meldet vom Mount aus JEDEN Worktree als `prunable`. NIE prunen.
`git checkout -- <datei>` repariert auf diesem Mount NICHT (unlink verboten):
   `git show HEAD:<pfad> > /tmp/x && cat /tmp/x > <pfad>`.
`git commit --amend` laesst index.lock UND HEAD.lock liegen. Nicht amenden.
Backticks in einer Commit-Botschaft unter DOPPELTEN Anfuehrungszeichen fuehrt die Shell aus.
`zaehle.mjs <datei>` wirft ENOENT, solange die Datei nicht existiert - ein Kriterium fuer eine
   NEUE Datei muss ueber das VERZEICHNIS messen.
S-07 feuert nur bei `kritikalitaet: P1`.
Ein `ausgangswert`, der schon dem Ziel entspricht, ist ein totes Kriterium - und einer, der
   FALSCH gemessen ist, ist ein unerfuellbares. Beides am 02.08. je einmal passiert (W-06 K-02,
   K-04), beides vor dem Gegenlesen selbst gefunden. `zeile-ersetzen` faengt das NICHT.
Die Geraete-VM laeuft in UTC, git meldet Europe/Berlin - zwei Stunden Unterschied.
   Immer `TZ=Europe/Berlin` + `--date=format-local:`.
```

## 7. Fehlerklassen

```text
✅ F-01…F-09 F-11…F-15 haben eine Barriere
⚠  F-10  Lock-Reste - auf diesem Mount NICHT behebbar (`unlink` verboten), nur gemildert.
         Beiseite unter .git/_locks_beiseite/<datum>/. Regel: 0 Byte + Minuten unveraendert
         = Rest, alles andere = laufender Vorgang.
⚠  F-16  GATE_MUSTER faengt npm & Co., aber KEINE Shell-Wrapper. Barriere ist W-01, `bereit`.
❌ F-17  Ein unbekannter `typ:` verschwindet lautlos aus dem Bericht.
❌ NEU   Die Erlaubnisliste laesst `node <beliebig>`, `sed -i` und `awk system()` durch
         (Pruefer, 02.08.). Barriere waere W-07 - noch nicht geschnitten.
```

## 8. Was der Validator selbst sperrt

```text
PB-019  Kopf fehlt oder misst nichts, Blatt aber `status: aktiv`   exit 1
S-01    genau EIN aktives Blatt            -> heute: Z-03+Z-04
S-04    coverage ohne population_command und ohne eigenen Befehl
S-06    weniger als zwei baubare Blaetter (je LAUF)                exit 1
S-07    ein Kriterium ist schon vor dem Bau erfuellt (nur bei P1)  exit 1
S-08    Ausgangswert weicht von der Messung ab                     Meldung
S-09    Kopf ohne `status`
S-10    der Baum hat sich waehrend der Messung bewegt              exit 1
```

**Die zwei harten Regeln bleiben:**

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist.**
**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.**
