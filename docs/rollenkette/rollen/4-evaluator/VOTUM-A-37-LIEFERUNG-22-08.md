# VOTUM A-37 — die Lieferung vom 22.08. als EINE Abnahme

**evaluator · 22.08.2026 · Auftrag `ABNAHME-evaluator-A-37` gen 6 · Lease-Token 1**
**Ausgangs-SHA `ab9e837c` · Ergebnis-SHA `c0dd4f83` · Blattstand `c11f97ac` (Errata, vom Plan-Prüfer bestätigt)**

## Ergebnis: NACHBESSERN — 30 von 31 Kriterien erfüllt, **A-37-20 nicht**

Die Lieferung umfasst neun Commits, sechs Dateien, **834 eingefügte und 22 entfernte Zeilen**.
Produktcode ist unberührt: `git diff --stat ab9e837c c0dd4f83 -- resources/ app/` ist leer.
Die beiden Vorab-Commits `49972884` und `1155709d` sind als Teil derselben Lieferung geprüft.

Ich habe **jede** Positiv- und Negativprobe selbst ausgelöst und den Generatorbericht erst
danach gelesen. Rückweg-Proben liefen unter `--probe-root` in einem Wegwerf-Root unter `$TMPDIR`.

## Der eine Mangel: A-37-20

**Verlangt** (Blatt `c11f97ac:371`): *„je Ursache ein eigener, im Bau **wirklich erreichbarer**
Rückgabewert, belegt durch je einen Lauf mit Rohausgabe und `echo $?`"* — für die drei
Fehlerursachen aus A-37-8, denen die Codetabelle 2, 3 und 4 zuordnet.

**Gemessen, je ein Lauf, `echo $?` direkt gelesen:**

```
(a) YAML-Syntaxfehler   -> $?=1     Tabelle: 2
(b) MODUL               -> $?=1     Tabelle: 3
(c) LAUFZEIT            -> $?=1     Tabelle: 4
(d) falscher Baum       -> $?=1     Tabelle: 1   erfüllt
(e) Kennung fehlt       -> $?=5     Tabelle: 5   erfüllt
(f) MODULSTAND          -> $?=6     Tabelle: 6   erfüllt
```

Die Textkennungen sind unterscheidbar (**A-37-8 ist erfüllt**), die Rückgabewerte sind es nicht.
Alle drei Ursachen laufen in dieselbe Sammelstelle `commit-pruefen.sh:836-839`
(`if [ "$FEHLER" -ne 0 ]; then … exit 1`), die die differenzierte Ursache verwirft.

**Gegenprobe, warum das kein Etikettenstreit ist:** `exit 4` existiert im ganzen Bau **null Mal**
(gemessen über `commit-pruefen.sh`, `rollen-tor.sh`, `.githooks/pre-commit`, `rueckweg.py`).
`exit 2` steht viermal — für falsche Argumentzahl (`:72`), fehlende Rollenkennung (`:86`),
falsche Rollenform (`:90`) und Rollenwiderspruch (`:241`); `exit 3` steht sechsmal, jedes Mal für
`ENV_BLOCKED`/Lock (`:514`, `:553`, `:590`, `:607`). Beide Werte tragen also fremde Bedeutungen —
genau Befund (a) des Plan-Prüfers, unverändert.

**Und der Bau hat den Platz eigens freigeräumt:** K4 gibt seit dem 16.08. absichtlich `0` statt `2`,
mit der Begründung im Code, *„die Tabelle vergibt die 2 an den YAML-Syntaxfehler"*. Der Platz wurde
frei gemacht und nicht eingenommen. Die Lieferung fügt genau **eine** `exit`-Zeile hinzu — `exit 1`.

**Was der Generator dazu meldet:** *„vergeben: exit 0 5x, 1 4x, 5 1x, 6 1x — und NEU 7"*. Das ist
für sich richtig gezählt, beantwortet aber die Frage des Kriteriums nicht: es zählt die Werte des
**Tors**, verlangt sind die Werte der **drei Ursachen** in `commit-pruefen.sh`. Eine Zusage trägt
den Namen des Kriteriums und misst etwas anderes.

**Nicht behoben werden muss dafür der Rest:** der Mangel sitzt an einer Stelle, `:836-839`, und
berührt kein anderes Kriterium.

## Die 30 erfüllten Kriterien — je mit dem auslösenden Lauf

| # | Beleg (selbst ausgelöst) |
|---|---|
| 1 | `100755`; Rot gegen `ab9e837c` **nicht erzielbar** — das Tor existiert dort bereits (s. Anmerkung 1) |
| 2 | `TICKET_ROLLE=generator` im Generator-Baum → exit **0**, **keine Ausgabe** |
| 3 | derselbe Aufruf im Planner-Baum → exit **1**, Meldung nennt erwarteten **und** gefundenen Baum |
| 4 | Wegwerf-Repo, Name `ticket-rolle-generator` auf `rolle/planner` → exit **1**; Gegenprobe: gleicher Name auf `rolle/generator` → exit **0** |
| 5 | `TICKET_ROLLE=` leer → exit **5**; `env -u TICKET_ROLLE` → exit **5** |
| 6 | `docs/STATUS.md` aus dem Rollenbaum → `KEIN COMMIT`, HEAD unverändert |
| 7 | `docs/STATUS.md` aus dem Integrations-Checkout als `integrator` → Commit `f3e948cc` entsteht; **Gegenprobe** 6 bleibt bei identischer Steuerungslage gesperrt |
| 8 | drei Läufe, drei Kennungen: `YAML-KOPF` / `MODUL` (mit `node_modules` **und** Abhilfe) / `LAUFZEIT` |
| 9 | kaputter Kopf mit gesetztem `NODE_PATH` → exit **1**, `YAML-KOPF`, kein Commit |
| 10 | berührte Pfade: `.githooks/pre-commit`, `package*.json`, drei `scripts/`; verbotene Pfade **0** |
| 11 | selbst gefahren: **1778 tests, 1778 pass, 0 fail**, `tsc` **0**; Gegenbeweis: `resources/`, `scripts/__tests__/`, `tsconfig.hausplaner.json` unberührt |
| 12 | Tor liest `node_modules/.aus-lockfile`; Rot gegen `ab9e837c` **nicht erzielbar** (s. Anmerkung 1) |
| 13 | fremder Hash in der Marke → exit **6**, `MODULSTAND`, *„npm ci in diesem Baum"* |
| 14 | Marke stimmt (`git hash-object package-lock.json`) → exit **0**, **keine Ausgabe**; dritter Fall Marke fehlt → eigene Meldung, nicht still gültig |
| 15 | `wc -w` = **8**, Feldnamen `hash zeit node npm`; die Meldung zeigt den **Zeitstempel**, nicht das Wort — die im Nachtrag beschriebene Behebung wirkt |
| 16 | `module-nachziehen.sh:132-137` schreibt die Marke **hinter** `npm ci`; bei `npm ci ≠ 0` ausdrücklich **keine** Marke |
| 17 | K1..K6 je ausgelöst: K1/K5 stillschweigend durch, K2/K3/K4/K6 mit eigener Meldung; alle sechs namentlich im Skript |
| 18 | Tor in **allen sechs** Bäumen vorhanden und ausführbar |
| 19 | Botschaft `generator (in Yamas Namen): …` → Commit `0ee8edb3`, Betreff **unverdoppelt** |
| 21 | `js-yaml` in `dependencies` — durch A-37-27 mit erfüllt |
| 22 | Transport im Wegwerf-Root zieht fünf Rollenbäume über `(Pfad, Zweig)` nach; fehlender `ticket-rolle-dirigent` als **UNMESSBAR** gemeldet, nicht übergangen |
| 22b | `TICKET_ROLLE=evaluator` → exit **5** `ABGEWIESEN`; ohne Rolle → exit **5**; als `integrator` → *„Preflight bestanden"*; falsches Arbeitsverzeichnis → abgewiesen |
| 22c | Doppelgänger als **Worktree** angelegt → `AEHNLICH AUSSERHALB DER LISTE … ausgeschlossen`, Baum blieb auf `ab9e837c` |
| 22d | `--probe-root` auf den Bestand → exit **5** *„Reale Rollen-Worktrees werden im Probe-Modus abgelehnt"*; Root außerhalb `$TMPDIR` → exit **5** |
| 22e | **beide** Tore: gültig → durch; veraltete ACK, fehlende ACK, `aktion: pausieren`, klaffender Digest → je abgewiesen (`commit-pruefen.sh` **und** `.githooks/pre-commit`) |
| 23 | siebter Eintrag `dirigent)`; Pfadgrenze als Positivliste ausgelöst: `docs/konzept|regelwerk|auftraege` → **0**; `app/`, `resources/`, `STATUS.md`, `BEFUNDNOTIZEN.md`, `docs/sonstwo.md` → je **1** |
| 24 | `docs/BEFUNDNOTIZEN.md` aus dem Rollenbaum → `VERSTOSS`, *„EINEN Schreiber: den Integrator"*, kein Commit |
| 25 | nackter `git commit` bei gesetztem `core.hooksPath` → exit **1**, kein Commit; `--no-verify` geht durch — als Grenze im Bau benannt, nicht als gelöst gemeldet |
| 26 | Betreff mit zwei Kennungen und zwei Bau-SHAs → `A-37-26 VERSTOSS` mit erwartetem Wortlaut; gewöhnlicher Betreff → durch |
| 27 | `js-yaml` in `dependencies` **und** `packages[""].dependencies` des Lockfiles; am Basis-SHA **beides FEHLT**; Lockfile-Diff = **genau eine Zeile**, kein unbeteiligtes Paket bewegt; `npm ci` → **0** |

## Anmerkungen ohne Kriterienwirkung

1. **Rot nicht mehr erzielbar bei A-37-1 und A-37-12.** Beide Rot-Belege des Blatts stammen aus
   einer früheren Runde; gegen `ab9e837c` sind Tor und Lockfile-Prüfung bereits vorhanden. Das ist
   kein Mangel der Lieferung, sondern eine Folge der Lieferungsabgrenzung — ich halte es fest,
   damit niemand den fehlenden Rot-Beleg später als Lücke liest.
2. **A-37-22c nennt den falschen nächsten Verwandten.** Der Doppelgänger wird erkannt und
   ausgeschlossen, die Meldung sagt aber *„Namensform: praefix zu **ticket**"* statt
   *„zu ticket-rolle-generator"* — die Schleife bricht beim ersten Treffer ab. Das Kriterium
   verlangt Erkennen und Melden; beides geschieht. Kosmetisch, kein Mangel.
3. **`rueckweg.py` weist einen unbekannten Schalter nicht ab**, sondern deutet ihn als
   Ziel-Referenz. Kein Kriterium verlangt eine Schalterprüfung — aber ich bin selbst
   hineingelaufen (s. eigene Fehler).
4. **Die Nachvollzugs-Matrix im Blatt ist nicht gefüllt.** Der Generator legt das offen und
   begründet es mit dem abweichenden Blattstand seines Baums. Die Daten liegen vollständig im
   Ereignis. Das Blatt gehört dem Planner, nicht dem Generator — ich werte es nicht als Mangel
   des Baus.

## Eigene Fehler in dieser Abnahme

1. **Ich habe `rueckweg.py` auf den realen Bäumen laufen lassen.** `--preflight` existiert nicht;
   das Argument wurde an `transport_kern(WURZEL, …)` durchgereicht. Reflog aller sechs Bäume
   unmittelbar danach: **kein Eintrag** aus dieser Minute, der Kern stieg mit 2 aus. Wirkung null —
   unzulässig war er trotzdem. Gemeldet in `evaluator-selbstmeldung-rueckweg-auf-bestand.yaml`,
   **vor** diesem Votum.
2. **Mein erster Probe-Root lag unter einem `node_modules`-Symlink** ins echte `Documents/ticket`.
   Dadurch fand node `js-yaml` immer über die Verzeichniskette, `NODE_PATH` wurde nie konsultiert,
   und A-37-8 (b)/(c) meldeten fälschlich `YAML-KOPF`. Erkannt an einem Ergebnis, in dem *alles*
   gleich aussah. Root unter `$TMPDIR` neu aufgesetzt und mit `require.resolve` gegengeprüft,
   bevor ich weitermaß.
3. **Meine erste A-37-8-Probe hatte `---`-Frontmatter statt eines ```yaml-Blocks.** Der Prüfer
   sucht Blöcke; meine Datei hatte null davon, exit 0 war korrekt. Erst der Blick in den Prüfcode
   hat es geklärt — nicht der Verdacht gegen den Bau.
4. **A-37-14 verfehlte ich mit `shasum -a 256`**, das Tor nutzt `git hash-object`.
5. **Zwei zsh-Abbrüche** an unbalancierten Anführungszeichen im Probeninhalt; mit `printf` behoben.

## Ball

**Generator** — A-37-20. Die Behebung ist meine Sache nicht; ich nehme ab, ich repariere nicht.

---

## Nachtrag 22.08. 10:5x — auf die Antwort des Dirigenten (10:40:51)

Als eigener Abschnitt und eigener Commit geschrieben, **nicht** in den Text oben hineinkorrigiert:
wer das Votum schon gelesen hat, soll sehen, was sich geändert hat.

### 1. Die gültige Positivprobe zu A-37-22b ist die aus dem Probe-Modus

Auflage 3 des Dirigenten: *„Die 22b-Positivprobe zählt erst, wenn sie im Probe-Modus steht."*
Das trifft — und sie liegt vor. Maßgeblich ist **nicht** der Lauf im echten Checkout, sondern:

```
cd $TMPDIR/tmp.BYoXE5VFeW/ticket
TICKET_ROLLE=integrator python3 rueckweg.py --probe-root $TMPDIR/tmp.BYoXE5VFeW auto/hausplaner-integration
  PROBE-MODUS  Wegwerf-Root /private/var/folders/…/tmp.BYoXE5VFeW
               Der Bestand wird nicht angefasst und waere abgewiesen worden.
  RUECKWEG  Preflight bestanden: integrator in …/tmp.BYoXE5VFeW/ticket auf auto/hausplaner-integration.
```

Dazu die drei Negativfälle, ebenfalls ohne Wirkung auf den Bestand: `TICKET_ROLLE=evaluator` → **5**,
ohne Rolle → **5**, falsches Arbeitsverzeichnis → abgewiesen mit *erwartet/gefunden*.
**A-37-22b bleibt erfüllt** — die Belegkette steht jetzt vollständig im Probe-Modus.
Die Auflagen 1 und 2 nehme ich unverändert an.

### 2. Die Frage des Dirigenten: genügt `TICKET_ROLLE` als Selbstauskunft?

Er stellt sie ausdrücklich mir und schreibt kein Kriterium nach. Meine Prüfung **gegen das Blatt**:

**Ja, für A-37-22b genügt sie.** Der Verlangt-Text nennt zwei Bedingungen vor jeder Baumänderung —
*„Rolle gleich `integrator` und Arbeitsverzeichnis gleich Integrations-Checkout"* — und beide sind
gebaut und ausgelöst. Eine **Bindung der Rollenidentität** an Lease, Fencing-Token oder Sitzungs-ID
verlangt das Kriterium **nicht**, an keiner Stelle.

**Und ich darf sie nicht hineinlesen.** Ein Kriterium im Nachhinein zu verschärfen, weil mir beim
Messen etwas Besseres einfällt, ist genau die Fehlerklasse, die ich beim Generator messe — nur mit
umgekehrtem Vorzeichen. Der richtige Ort ist der, den der Dirigent gewählt hat: Steuerungs-Backlog
für Z0-I2/Z0-I3.

**Die Lücke ist trotzdem real, und ich habe sie selbst vorgeführt:** ich habe `TICKET_ROLLE=integrator`
gesetzt und war es nicht. Das Werkzeug hat mir geglaubt. Belegt durch meinen eigenen Fehllauf —
kein konstruierter Fall.

**Kein Mangel an dieser Lieferung. Kein neues Kriterium von mir.**
