# STAND — das Arbeitsgedächtnis

> **Diese Seite wird ÜBERSCHRIEBEN, nicht angehängt.** Sie ist eine Seite lang und bleibt es.
>
> **Regeln:** eine Zeile je Sache · eine Rücknahme ERSETZT die Aussage · was erledigt ist,
> verschwindet · **kein Datum ohne Zahl, keine Zahl ohne Befehl.**

**Zuletzt geschrieben:** 01.08.2026, 22:10 · Planner

---

## 1. Wo der Bau steht — gemessen 22:09

```text
main     d8612a63   19:01 · git diff 99f38da7 main LEER · 3 Merges
Zweig    9f203c81   auto/hausplaner-integration · 40 voraus vor main · Locks 0
UNGEPUSHT 13        <- der Rueckstand ist NICHT abgearbeitet worden, sondern durch den
                       ungewollten Push von 20:01 auf null gefallen. Siehe Abschnitt 3.

  git --no-optional-locks rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration
  git --no-optional-locks rev-list --count main..auto/hausplaner-integration

Kennzahl Produktivcode - `config` gehoert dazu:
  git log -1 --date=format-local:'%d.%m. %H:%M' --pretty='%h %ad %s' \
    -- resources app tests routes public database scripts config
```

## 2. Die Schlange — wer was hat

> **AUTONOM-MODUS seit 01.08. 23:0x.** Yama: *„ihr sollt auch alleine arbeiten ohne meine Hilfe …
> ihr sollt euch stark zeigen."* **Es wird nicht mehr gefragt, es wird entschieden und belegt.**
> Bei Yama liegen genau zwei Sätze (Y1, Y2 im Beschluss) — alles andere gehört uns.

| Rolle | Was liegt bereit |
|---|---|
| **Generator** | **Z-03+Z-04** `aktiv` (baut) · **W-01** `bereit` (ALLOWLIST bei 3, 69 Zusagen/0 fail) · **Z-10** `bereit` |
| **Evaluator** | **Z-05 — der EINZIGE Posten zwischen Yama und Z-06** · AUF-38-P4+P5 gebaut · W-01 abnehmen (**Planner-Befund im Blatt, vor der Abnahme lesen**) · W-02 gegenlesen |
| **Prüfer** | **P-01** — **Teil 0 hat Vorrang**: aus welcher Umgebung kamen die Pushes · **PW-01 gegenlesen** |
| **Planner** | B2-Blatt offen · Z-07/Z-08/Z-09 nach Z-06 |
| **Yama** | nur noch Y1 und Y2 · die 3 PHP-Dateien · Papierstopp PB-042 |

**Entwürfe nach B8** (kein `bereit` ohne Gegenlesen): **PW-01** der Bote `bacb3974` · **W-02**
`zeile-ersetzen` `09049b23`.

**Abgenommen am 01.08.:** Z-01 · Z-02 · AUF-38-P1+P2+P3 · PB-023 · PB-043 T2 · AUF-91 · PB-047 · **Z-05-N1**.
**Gesperrt:** Z-06 (nur noch: Votum für Z-05) · AUF-83-T2T3 · AUF-83-T5 · AUF-88-P1.

## 3. DER INZIDENT vom 01.08. — dreimal falsch zugeordnet, auch von mir

**Gemessen 22:3x, und dieser eine Befehl hätte alles davor erspart:**

```text
timeout 20 git --no-optional-locks ls-remote --exit-code fork HEAD
  exit=128 · HTTP 403 vom Proxy nach CONNECT
```

**Aus der Planner-Umgebung (`device_bash`, Geräte-VM) ist GitHub NICHT erreichbar.**

| Zeitpunkt | was wirklich gemessen ist |
|---|---|
| **20:01:03** | `push-result.log` geschrieben — direkt nach meinem Lauf um 20:00. **Der Wrapper LIEF.** Gepusht hat er nicht: kein Netz |
| **20:48:31** | `fork` springt auf `9ac24f7b` — **update by push**, aus einer anderen Umgebung |
| **22:11:27** | `fork` springt auf `1a86d21f` — **update by push**, elf Sekunden vor dem Log |

```text
TZ=Europe/Berlin git --no-optional-locks reflog show \
  --date=format-local:'%H:%M:%S' fork/auto/hausplaner-integration
```

**Drei Zuordnungen, alle drei falsch.** Der Evaluator schrieb den Push sich selbst zu. Ich habe
ihm widersprochen und ihn mir zugeschrieben — auf Basis einer Datei-mtime, während der Reflog
danebenlag. **Keiner von uns hat geprüft, ob er die Fähigkeit überhaupt hat.**

**Was von meinem Fehler bleibt, und es bleibt genug:** mein Verzeichnislauf hat einen
publizierenden Befehl **ausgeführt**. Dass er wirkungslos blieb, liegt an einem Proxy, nicht an
meiner Sorgfalt. *Ich habe abgedrückt; die Waffe war nicht geladen.*

**OFFEN und nicht vom Planner zu klären:** aus welcher Umgebung die beiden echten Pushes kamen.
Das ist die erste Frage von P-01 — **und sie ist wichtiger als die Befehls-Inventur**, weil sie
bestimmt, ob eine Barriere im Validator überhaupt am richtigen Ort sitzt.

**Bitter und deshalb ausdrücklich:** der Rückstand von 55 Commits ist draußen. Der Regelverstoß
hat den Posten geschlossen, auf den Yama seit Tagen wartete.

## 4. Was entschieden ist — gilt, bis es hier ersetzt wird

> **`docs/BESCHLUSS-fehlervermeidung.md` (`2adad9e6`) steht ÜBER dieser Tabelle.** Acht Beschlüsse
> mit Wirkstufe und Merkmal. **B8 gilt ab sofort ohne Werkzeug: ein Blatt wird nicht `bereit`,
> bevor eine andere Rolle es gegengelesen hat** — `gegengelesen_von`, `gegengelesen_am`, `befund`.
> Ohne diese drei Zeilen bleibt es `entwurf`. Der Planner nimmt sich davon nicht aus.

| Entscheidung | Kurz |
|---|---|
| **Allowlist statt Textmuster** | W-01. Nicht aufzählen, was verboten ist, sondern was erlaubt ist — jedes Glied der Kette, `bash`/`sh` nur unter `scripts/` |
| **Publizierende Befehle nie in ein Blatt** | Ein Blatt ist eine Datei, die ein Werkzeug ausführt. Was darin steht, PASSIERT |
| **Bilanz getrennt, nicht hochgezählt** | `PAKET 110 + EIGENE.length`. Vom Evaluator in `a0a6e250` bestätigt: fängt auch den Zuwachs-Fall |
| **Z-05-N1 vor Z-06** | Yama, 01.08. Ohne Erreichbarkeit gäbe es nie eine gezeichnete Kontur |
| **Ein Befehl je Nachricht** | Bei jedem Terminal-Vorgang. Yama, 30.07. 22:52 |
| **`studioDaten.ts` behält echte Farbwerte** | Konva löst keine CSS-Variable auf |
| **Commits über `scripts/commit-pruefen.sh`** | Prüft Existenz, Änderung und Syntax vor dem Commit (F-14) |

## 5. ZURÜCKGENOMMEN — nicht wieder aufwärmen

| Aussage | Wahrheit |
|---|---|
| „60 Commits ungepusht" (Planner, 21:5x, in W-01) | **13.** Eine Zahl ohne Befehl, in einem Blatt, das ich gerade schrieb |
| „Der Verzeichnislauf braucht 3,6 s" | Der Evaluator misst **39,4 s / 30 Blätter**, b01 allein 33,8 s |
| „Der Push liegt beim Evaluator" (Evaluator, 21:17) | Nicht belegt |
| „Der Push liegt beim Planner" (Planner, 22:0x) | **Auch nicht.** Aus meiner Umgebung ist GitHub nicht erreichbar — `git ls-remote fork` → HTTP 403 vom Proxy. Ich habe eine Datei-mtime für einen Push-Zeitpunkt gehalten, während der Reflog danebenlag |
| „8 / 13 Commits nur auf der Platte" | Waren 55, bis der ungewollte Push sie hinausschob |
| „`GATE_MUSTER` ist die Barriere" | Sie fängt npm & Co., **nicht** Shell-Wrapper — die gefährlichste Klasse |
| „Die drei gesperrten Blätter machen den Lauf langsam" | **46 npm-Befehle** über 20 Blätter waren es |
| „Der Wächter läuft nicht mehr" | Er läuft. Er hatte einen Commit übersprungen |

## 6. Fallen — die vier neuen von heute Abend

```text
Ein `OK` im Validator-Bericht heisst: der Befehl LIEF. Wer nur nach FEHLSCHLAG filtert, sieht
   nicht, was ausgefuehrt wurde. Genau so ist mir der Push durchgegangen.
git worktree list meldet vom Mount aus JEDEN Worktree als `prunable` - auch ../ticket-main.
   `git worktree prune` von hier aus meldet den Merge-Worktree ab. NIE von hier ausfuehren.
head/tail-Splices: DREIMAL heute an der Grenzzeile verrutscht. Vor jedem Splice die Grenzen
   anzeigen. `git checkout -- <datei>` repariert auf diesem Mount NICHT (unlink verboten):
   `git show HEAD:<pfad> > /tmp/x && cat /tmp/x > <pfad>`.
`git commit --amend` laesst index.lock UND HEAD.lock liegen. Auf diesem Mount nicht amenden.
Backticks in einer Commit-Botschaft unter DOPPELTEN Anfuehrungszeichen fuehrt die Shell aus.
`zaehle.mjs <datei>` wirft ENOENT, solange die Datei nicht existiert - ein Kriterium fuer eine
   NEUE Datei muss ueber das VERZEICHNIS messen, sonst kann es vor dem Bau nicht laufen.
S-07 feuert nur bei `kritikalitaet: P1`.
`| tail -n` schluckt den Exitcode - auch beim Validator.
```

## 7. Die Fehlerklassen — 15 von 18 haben eine Barriere

```text
✅ F-01 F-02 F-03 F-04 F-05 F-06 F-07 F-08 F-08b F-09 F-11 F-12 F-13 F-14 F-15
⚠  F-10  Lock-Reste - auf diesem Mount NICHT behebbar (`unlink` verboten), nur gemildert
⚠  F-16  HALB. GATE_MUSTER faengt npm & Co., aber KEINE Shell-Wrapper - ROT vom Evaluator.
         Die Barriere ist W-01 (Allowlist), geschnitten, noch nicht gebaut.
❌ F-17  Ein unbekannter `typ:` verschwindet lautlos aus dem Bericht.
```

**Die zwei harten Regeln bleiben:**

**A — Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist.** *Z-10 trug beim ersten
Schnitt ein Kriterium, das vor dem Bau gar nicht laufen konnte — der Validator hat es sofort
gefangen.*

**B — Keine Arbeit liegt länger als zwanzig Minuten uncommittet.** *`PRUEFER-BEFUNDE.md` liegt seit
13:04 im Baum — neun Stunden. P-01-06 misst das.*

## 8. Was der Validator selbst sperrt

```text
PB-019  Kopf fehlt oder misst nichts, Blatt aber `status: aktiv`   exit 1
S-01    genau EIN aktives Blatt            -> heute: Z-03+Z-04
S-04    coverage ohne population_command und ohne eigenen Befehl
S-06    weniger als zwei baubare Blaetter                          exit 1  -> heute: 3
S-07    ein Kriterium ist schon vor dem Bau erfuellt (nur bei P1)   exit 1
S-08    Ausgangswert weicht von der Messung ab                     Meldung
S-09    Kopf ohne `status`
S-10    der Baum hat sich waehrend der Messung bewegt              exit 1
```

**63 Zusagen über die beiden Werkzeuge, 0 fail** — `node --test scripts/__tests__/auftragPruefen.test.mjs scripts/__tests__/zaehle.test.mjs`
