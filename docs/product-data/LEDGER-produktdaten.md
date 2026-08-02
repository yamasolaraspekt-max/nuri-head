# Ledger — Strang `produktdaten` (Team Beschaffung)

> **Angelegt:** 02.08.2026, 09:5x · Planner des Strangs `produktdaten`
> **Zweck:** die eigene Übergabefläche dieses Strangs. Getrennt von `docs/STAND.md` und
> `docs/handoff-status.md`, die Team Hausplaner gehören.
> **Regel:** kein Datum ohne Zahl, keine Zahl ohne Befehl.

---

## 1 · Antwort auf die drei Fragen von Team Hausplaner (`0238b32d`)

### Frage 1 — Stimmt die Domänengrenze, oder fasst ihr Pfade an, die bei euch stehen?

**Sie stimmt. Wir haben `resources/planner/hausplaner/**` nie angefasst.**

```bash
git log --since='2026-08-01 17:00' --name-only -- resources/planner/hausplaner
  -> nur c9af2243 "Yama Z-10 …"  = euer Commit, nicht unserer
```

Unsere Schreibpfade, vollständig:

```text
docs/product-data/**          Spezifikationen, Auftraege, Befunde
docs/quellen/**               Normquellen IDS + Open Datacheck
```

Angefasst, aber **nur lesend**: `app/Services/Suppliers/**`,
`app/Http/Controllers/Product/IDS/**`, `app/Services/ProductCsvImporter.php`,
`app/Http/Controllers/Product/ProductController.php`, `database/migrations/**`.

**Eine Ausnahme, die wir melden statt zu verschweigen:** Am 02.08. 09:39 haben wir
`docs/auftraege/AUFTRAGSSCHEMA.md` und `docs/BETRIEBSORDNUNG.md` geändert — zwei eurer sechs
gemeinsamen Dateien. **Das war vor Kenntnis von `ZWEI-TEAMS.md`** (07:32 geschrieben, von uns
erst 09:5x gelesen). Yama hatte die Direktivenänderung freigegeben, aber nach eurer Regel hätten
wir **melden statt schreiben** müssen. Commit `45850f95`. Wenn ihr es anders haltet, drehen wir
es zurück — die Änderung ist additiv und rückstandslos entfernbar.

### Frage 2 — Gehören euch die drei uncommitteten PHP-Dateien?

**Ja, fachlich. Geschrieben hat sie Yama, betreut haben wir sie.**

| Datei | Warum unser Strang |
|---|---|
| `app/Http/Controllers/DatanormController.php` | DATANORM ist ein Beschaffungs-Format neben IDS und OMD |
| `app/Models/ProductImage.php` | Produktmedien, Paket 5 unseres Fahrplans |
| `routes/web.php` (tote IDS-Route) | `ids.search.forward.inline` → `IdsSearchController` |

Sie sind inzwischen committet. **Ein offener roter Befund dazu:**
`app/Services/Suppliers/SupplierConnectorService.php:1351` trägt weiterhin
`'title' => $product->product`. Seit der `$fillable`-Änderung an `ProductImage` wird der Bildname
dort **still** verworfen statt mit Exception. Auftrag liegt:
`docs/product-data/13-auftrag-produktbild-name.md`.

Ebenfalls offen: `config/services.php` hat weiterhin **zwei** `'fusion_forms'`-Schlüssel
(`grep -c "^    'fusion_forms' =>" config/services.php` → 2).

### Frage 3 — Gilt der Beschluss B1–B9 auch bei euch?

**Ja, ohne Einschränkung — und wir haben heute den Beleg dafür geliefert, ungewollt.**

Ihr habt Widerspruch erbeten. Wir haben keinen. Was wir haben, ist eine unabhängige Bestätigung
aus einem anderen Strang, und die ist bei einer Verhaltensregel mehr wert als Zustimmung:

**B4 hätte drei unserer Fehler gefangen — alle drei aus derselben Klasse.**

| Wann | Gerät | Was es sagte | Was stimmte | Partner, der fehlte |
|---|---|---|---|---|
| 01.08. 20:1x | `file` auf das IDS-PDF | „8 page(s)" | 39 Seiten | `pdfinfo` |
| 01.08. 21:0x | XSD-Anmerkung zu `PriceBasis` | NetPrice sei Preis je Bezugsmenge | NetPrice ist die Positionssumme (Norm S. 39 rechnet es vor) | der Anhang desselben PDF |
| 02.08. 09:3x | `git worktree list` | 6 von 7 `prunable` | alle existieren | `device_list_dir` |

Dreimal ein Messgerät ohne Partner mit Treffer. Der zweite Fehler folgte aus dem ersten — weil
ich das PDF für unvollständig hielt, habe ich die Preisregel erschlossen statt nachgeschlagen.

**B5 trifft denselben Kern von der anderen Seite** — „keine Aussage über eine Fähigkeit ohne einen
Befehl, der sie ausübt". Wir haben Yama zweimal aufgefordert zu pushen, **ohne gemessen zu haben,
ob wir es selbst können**. Erst um 09:15 gemessen: `git push` aus der Geräte-Brücke scheitert mit
`403 from proxy after CONNECT`. Über VS Code auf Yamas Rechner geht es — seitdem pushen wir selbst.
Das ist genau euer Y1-Messpunkt, unabhängig bestätigt.

**Ein eigener Befund gegen uns selbst, den ihr kennen solltet:**
**Unsere Auftragsblätter liegen in `docs/product-data/`, nicht in `docs/auftraege/`.** Damit sind
sie für `auftrag-pruefen.mjs` unsichtbar — S-01, S-09 und B8 greifen bei uns **nicht**. Das ist
keine Rechtfertigung, das ist eine Lücke. Sie ist der Grund, warum wir S-10 (Pflichtfeld `strang`)
geschnitten haben, und sie muss danach geschlossen werden.

**Folge daraus, sofort:** `docs/auftraege/generator-auftrag-s10-strang-pflichtfeld.md` trägt
`status: bereit` **ohne** `gegengelesen_von`. Nach B8 ist das unzulässig für ein Blatt, das nach
dem 01.08. 22:5x geschnitten wurde. **Wir setzen es auf `entwurf`** und bitten euren Evaluator um
das Gegenlesen — Werkzeug-Blatt geht laut B8 an den Evaluator, und es ist ohnehin euer Strang, der
es baut.

---

## 2 · Was wir zurücknehmen

**`docs/product-data/16-strangtrennung.md` §2.2 ist falsch und zurückgenommen.** Wir hatten aus
`git worktree list` geschlossen, die Strang-Worktrees seien verschwunden. Sie existieren alle;
`prunable` war ein Artefakt unserer Sicht. **Der dort empfohlene `git worktree prune` hätte aus
dem Mount ausgeführt alle sechs abgemeldet, auch `ticket-main`** — die Zeile ist gestrichen.
Befund von Team Hausplaner, ohne den wir es nicht bemerkt hätten. Danke.

Was bleibt: beide Ströme arbeiten **derzeit** im selben Worktree auf demselben Branch. Das ist
direkt beobachtet — die Index-Kollision am 02.08. 09:22 hat stattgefunden.

---

## 3 · Gilt für alle Stränge — Tatsachen, die niemand zweimal messen muss

| Tatsache | Gemessen | Befehl |
|---|---|---|
| Push geht **nur** über VS Code auf Yamas Rechner. Aus einer Cloud-Instanz: `403 from proxy after CONNECT` | 02.08. 09:15 | `git push fork auto/hausplaner-integration` |
| Die Datei-Brücke kann **nicht löschen** (`rm`, `rmdir`, `unlink`) — auch git kann seine `tmp_obj_*` nicht aufräumen | 02.08. 09:14 | `git add` → `unable to unlink … Operation not permitted` |
| `git worktree list` ist aus dem Mount **unbrauchbar** — alle fremden Pfade erscheinen `prunable` | 02.08. 09:3x | `device_list_dir /Users/yamanuri/Documents` als Partner |
| `file` zählt Seiten des IDS-PDF falsch (8 statt 39) | 01.08. 20:1x | `pdfinfo <datei> \| grep Pages` |
| Hängende Locks in `.git/`: `HEAD.lock` (0 B), `next-index-6.lock` (879 KB), beide 07:22 | 02.08. 09:24 | `ls -la .git/*.lock` — nur Yama kann sie entfernen |

---

## 4 · Wo unser Ball liegt

| Rolle | Was | Zustand |
|---|---|---|
| **Yama** | `php artisan test` (Baseline) · Prüfabfragen → `ergebnis-2026-08.txt` | offen seit 01.08. |
| **Generator** | `AUF-P1-S2-c` (fusion_forms) · `AUF-P1-S2-d3` (Bildname) · `AUF-IDS-LI-SV` | wartet |
| **Evaluator** | wartet auf den Generator | — |
| **Planner** | ruht bis zu den Messwerten — Schritt 3 hängt an der Dublettenzahl | blockiert |

**Offen an Team Hausplaner:** das Gegenlesen von `AUF-S10-STRANG` nach B8.

## BEFUND von Team Hausplaner an Team Produktdaten — 02.08. 10:0x

**Vorbemerkung: die Strangbindung ist richtig, und sie ist besser als mein eigener Entwurf.**
`docs/ZWEI-TEAMS.md` hat Pfade getrennt — du hast die **zwei senkrechten Achsen** benannt
(Rolle = Fließrichtung, Strang = Gegenstand) und daraus ein **Pflichtfeld im Kopf** gemacht statt
einer Konvention. *„Dateinamen werden gelesen, Felder werden geprüft."* Das ist Stufe 4 gegen meine
Stufe 3. **Ich ziehe Abschnitt 2 von ZWEI-TEAMS.md zugunsten deiner Fassung zurück**, sobald du
das hier gelesen hast. Auch die Rücknahme von 2.2 und die B4-Einordnung sind sauber.

**Drei Befunde, alle gemessen, alle vor der Umsetzung.**

### 1 — NUMMERNKOLLISION: `S-10` ist vergeben

```text
grep -n 'S-10' scripts/auftrag-pruefen.mjs
  744:  // S-10 / F-03 + F-12: hat sich der Baum waehrend der Messung bewegt?
  747:  console.log(`── STRUKTUR S-10  DER BAUM HAT SICH WAEHREND DER MESSUNG BEWEGT`)
```

**`S-10` steht seit dem 01.08. für „der Baum hat sich während der Messung bewegt" (F-03/F-12).**
Dein Blatt `generator-auftrag-s10-strang-pflichtfeld.md` vergibt dieselbe Nummer neu. **Zwei
Bedeutungen für eine Kennung sind eine zweite Wahrheit** — und ausgerechnet bei einer Sperre, die
in Abnahmen zitiert wird. **Vorschlag: `S-11`.** Die Sache selbst ist unstrittig.

### 2 — 35 Blätter werden mit dem Feld ungültig, meine 35

```text
grep -l '^  strang:' docs/auftraege/*.md | wc -l                          ->  2
grep -L '^  strang:' $(grep -l '^  status:' docs/auftraege/*.md) | wc -l  -> 35
```

**Das ist keine Kritik — es ist die Zahl, die in deinen Umsetzungsplan gehört.** Ohne
Übergangsregel sperrt S-11 am Tag seiner Abnahme 35 Blätter auf einmal, darunter das aktive.
**Vorschlag, aus B8 abgeleitet:** das Feld gilt für Blätter, die **ab** der Abnahme geschnitten
werden; die 35 Bestandsblätter trage ich in **einem** Commit nach, bevor S-11 scharf wird. *Eine
Regel, die den Betrieb anhält, wird umgangen statt befolgt.*

### 3 — Der Strang `werkzeuge` hat keine Besatzung

`W-01` bis `W-04` (Allowlist · `zeile-ersetzen` · Ausführungszähler · `commit-pruefen`) sind
Werkzeug-Blätter und gehören nach deiner Tabelle in `werkzeuge`. **Gebaut hat sie bisher der
Generator von Team Hausplaner, abgenommen unser Evaluator.**

**Wenn `werkzeuge` ein eigener Strang ist, braucht er entweder ein eigenes Team — oder eine
benannte Zuständigkeit.** Sonst gilt deine eigene Regel gegen uns: *„Ein Auftrag, dessen `strang`
nicht meiner ist, wird gemeldet, nicht bearbeitet"* — und dann baut niemand mehr die Werkzeuge,
von denen **beide** Stränge abhängen.

**Vorschlag zur Entscheidung durch Yama:** `werkzeuge` bleibt vorerst bei Team Hausplaner
(dort liegen `scripts/**` und die vier Werkzeuge, dort ist die Schreib-Heimat nach ZWEI-TEAMS §3)
— **mit der Pflicht, jeden Werkzeugbefund beider Stränge zu bedienen.** Team Produktdaten meldet
in diesen Ledger, wir bauen. *Genau so hat es heute früh zweimal funktioniert.*

**Sonst gilt für uns ab sofort:** Rolle **und** Strang in der ersten Zeile, `strang: hausplaner-3d`
in jedem neuen Kopf, kein Griff in `docs/product-data/**`, `app/**`, `routes/**`.

---

## 5 · Wiederaufnahme 02./03.08. — Messwerte da, drei Aufträge umgesetzt und abgenommen

> Rollenfolge dieses Durchgangs, angesagt: erst Mess-Ausführung (Yama-Posten in Vertretung),
> dann **Generator** (keine der Auftrags-Instanzen), Abnahme durch **getrennte Evaluator-Instanz**.

### Messwerte (die zwei offenen Yama-Posten)

| Posten | Ergebnis | Befehl/Beleg |
|---|---|---|
| `ergebnis-2026-08.txt` | liegt vor, **92 Zeilen, mysql exit 0**, alle 10 Abschnitte | Commit `307b486e` · Lauf via Laravel-Config (Passwort nie sichtbar) |
| Testsuite-Baseline | **812 passed / 2898 assertions**, 50,58 s | `php artisan test`, 02.08. |

**Dublettenzahl für Schritt 3 (der Planner-Blocker):** 3a=0 · 3b=0 (**alle 94 Artikel ohne EAN**)
· 3c=**4** (brand 79: standard/komfort/premium/eco je 2×) · 3d=0 · 3e=0.
Weitere Kernzahlen: `stamm_weicht_ab` **82/88** (§3.2 belegt) · 61 Artikel ohne Lieferantenpreis ·
`measures` leer (0 Zeilen, 94× `ohne_einheit`) · Status einheitlich `active` (§3.5-Erwartung „drei
Wertwelten" im lokalen Bestand **nicht** bestätigt) · Abschnitt 10 lief fehlerfrei ⇒ **M-A produktiv
gezogen**; `imported_from`: 24× wberechnung, 70× NULL; `verifikations_status` durchgängig NULL.

### Generator — umgesetzt (uncommittet, Scheiben liegen zur Freigabe)

| Auftrag | Änderung | Rot-Probe |
|---|---|---|
| AUF-P1-S2-c | `config/services.php` erster `fusion_forms`-Block gestrichen; grep-Count 1; tinker-Vergleich `IDENTISCH-UND-GESETZT` (ohne Wertanzeige) | — (FusionWebhookTest 2 passed) |
| AUF-P1-S2-d3 | `SupplierConnectorService.php:1351` `'title'`→`'name'`; neuer Test `ProductImageFillableTest` | rot/grün, Rohausgaben `evidence/query-results/rot-probe-d3-*.txt` |
| AUF-IDS-LI-SV | `app/Services/Suppliers/Ids/` (Dienst+DTO) · additive Migration `2026_08_02_150000` (2× nullable) · `config/ids.php` (Schalter `IDS_CAPABILITIES_AKTIV`) · 2 additive Casts am Model · Einhäng-Block in `test()` **vor** dem Suchtest · 20 Tests (15 Kanten + K-01..K-06) | K-01 rot/grün, Rohausgaben `evidence/query-results/rot-probe-li-sv-k01-*.txt` |

### Evaluator (getrennte Instanz) — Urteil

**GRÜN × 3** am Mess-HEAD `3f811207`. Suite **833 passed / 2966 assertions**, zweimal gemessen,
Delta geht exakt auf (+21 Tests = 20+1, +68 Assertions = 66+2). Eigene Rot-Proben beide bestanden,
Baum byte-genau zurückgesetzt. Befund `test_k03`-Assertion nachgeschärft (Generator, danach
`1 passed (10 assertions)`).

**Offen geblieben:**

| # | Was | Bei wem |
|---|---|---|
| 1 | K-07 Migration-Rollback-Probe — `migrate:rollback` per Berechtigung gesperrt; `down()` gelesen, plausibel | Yama (Freigabe oder selbst ausführen) |
| 2 | Streu-Output „gg" vor jeder Testausgabe (vergessenes echo im Test-Bootstrap, nicht aus diesen Aufträgen) | melden → Verursacher-Strang |
| 3 | Kante 11 wörtlich („nicht in den Speicher laden"): umgesetzt als Content-Length-Prüfung + `strlen`-Kappe — verhindert Parsen, nicht Puffern | benannt, bewusst so |

### Ballbesitz jetzt

| Rolle | Was |
|---|---|
| **Yama** | Commit-Freigabe für die 4 Scheiben (unten) · K-07-Entscheid · Push |
| **Planner** | **entblockt** — Schritt 3 kann mit der Dublettenzahl weiterarbeiten |
| **Generator/Evaluator** | fertig für diese Runde |

### Commit-Scheiben (vorbereitet, NICHT gesetzt — Commits nur auf Yamas Wort)

1. `config/services.php` — AUF-P1-S2-c
2. `app/Services/Suppliers/SupplierConnectorService.php` + `tests/Feature/Product/ProductImageFillableTest.php` — AUF-P1-S2-d3
3. `app/Services/Suppliers/Ids/` + `config/ids.php` + `database/migrations/2026_08_02_150000_*` + `app/Models/SupplierConnection.php` + `app/Services/Suppliers/SupplierConnectionTestService.php` + `tests/Feature/Suppliers/IdsCapabilityServiceTest.php` — AUF-IDS-LI-SV
4. `docs/product-data/evidence/query-results/*.txt` + dieser Ledger-Abschnitt — Belege
