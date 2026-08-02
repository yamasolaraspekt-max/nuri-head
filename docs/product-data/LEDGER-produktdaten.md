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
