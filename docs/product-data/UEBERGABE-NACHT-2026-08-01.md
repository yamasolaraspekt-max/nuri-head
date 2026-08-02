# Übergabe — Nacht auf den 02.08.2026

> **Geschrieben:** 01.08.2026, 23:30 · Planner · **Heimat-App:** `ticket`
> **Regel:** kein Datum ohne Zahl, keine Zahl ohne Befehl.
> Dies ist **nicht** `docs/STAND.md` — die gehört einem anderen Strang und wurde nicht angefasst.

---

## 1 · Zuerst, in dieser Reihenfolge

**a) Sichern.** Gemessen 23:28:

```bash
git --no-optional-locks rev-list --count '@{u}..HEAD'      # -> 29
```

29 Commits ohne Kopie außerhalb der Maschine. Dazu 860 KB Normquellen, die es nirgends sonst gibt.

```bash
git add docs/product-data docs/quellen docs/verfahren-rollen-und-ablauf.md
git commit -m "IDS-Normquellen + Spezifikationen aus der Nachtsitzung"
git push fork auto/hausplaner-integration
```

**Kein `git add -A`.** Im Baum liegen fremde Sachen: `docs/planner/PRUEFER-BEFUNDE.md` steht auf
`MM` (teilweise gestaged), und `scripts/zeile-ersetzen.mjs` ist neu und nicht von mir. Ein zweiter
Strang arbeitet parallel.

**b) Messen.** Weiterhin offen, und ohne diese Zahlen bleibt Schritt 3 blockiert:

```bash
php artisan test                          # Baseline, Zahl notieren
php artisan route:list | grep -i ids      # Kriterium (a)
mysql -h 127.0.0.1 -P 3307 -u <user> -p ticket \
  < docs/product-data/evidence/query-results/datenqualitaet-pruefabfragen.sql \
  > docs/product-data/evidence/query-results/ergebnis-2026-08.txt
```

---

## 2 · Was in der Nacht entstand

| Datei | Was |
|---|---|
| `docs/quellen/ids/` (860 KB) | Die vollständige IDS-Norm: PDF 39 Seiten, 7 XSD, 6 echte Beispiele, 2 Shop-Referenzformulare, dazu `LIESMICH.md` mit 403 Zeilen Auswertung |
| `docs/product-data/11-identitaetsspezifikation-paket1-schritt3-4.md` | Paket 1 Schritt 3+4 ausformuliert, Entscheidungen getroffen |
| `docs/product-data/12-e4a-weg-d-entscheidung.md` | **E4a entschieden** |
| `docs/product-data/13-auftrag-produktbild-name.md` | Auftrag für den committeten Defekt |
| `docs/product-data/14-preis-spezifikation.md` | Paket-3-Vorlage, nicht beauftragt |
| `docs/product-data/evidence/` | Evaluator-Befund und die zwei Nachbesserungsaufträge |
| `docs/verfahren-rollen-und-ablauf.md` | Rollen, Kreislauf, vier Konstruktionsfehler im Verfahren |
| zwei `.skill`-Dateien im Chat | `ids-connect` neu geschrieben, `open-masterdata` |

---

## 3 · Die fünf Befunde, die etwas ändern

**1 · Es sind neun Schreibpfade auf `products`, nicht sechs.** Belegt per Zensus. Vier fehlten in
`10-target-domain-model.md`, einer (`SpecImportService`) schreibt gar nicht, er liest nur. Der
Verriegelungstest aus Schritt 4 hätte gegen sechs gebaut **drei Pfade offengelassen und wäre
grün gewesen**. → `11-…md` §1

**2 · `SupplierConnectorService:657` sucht Preise ohne `distributor_id`.** Der Schwesterpfad
filtert korrekt. Bei zwei Großhändlern mit derselben Artikelnummer landet der Preis am falschen
Artikel — still. → `11-…md` §2.1

**3 · E4a: Weg D ist ein OCI-Punchout, kein IDS-Parser** — und laut `MapperRegistry:25` die
**produktive** Strecke (Sonepar). Stilllegen wäre Funktionsverlust. Er bleibt, wird angeschlossen,
drei Geldfehler werden behoben — darunter eine **aus dem Nichts erfundene Marge von 20 %**
(`normalizeItem:516`). Weg D fällt damit aus Paket 1 Schritt 5 heraus. → `12-…md`

**4 · Die IDS-Preisregel ist belegt.** `NetPrice` ist die **Positionssumme**, inklusive Rabatt und
Rohstoffzuschlag. Die Norm rechnet es auf Seite 39 selbst vor. `PriceBasis` gehört zu
`OfferPrice`. Neun Lückenposten gegen `distributor_prices`, größte: `PriceBasis` fehlt,
`decimal(10,2)` statt 10,4, keine Zeitachse, Rohstoffanteil und DEL-Notierung fehlen ganz.
→ `14-…md`

**5 · Der committete Defekt.** `SupplierConnectorService:1351` trägt weiter
`'title' => $product->product`. Seit deiner `$fillable`-Änderung wird der Bildname **stumm**
verworfen statt mit Exception. Ein lauter Fehler wurde gegen einen leisen getauscht, und er steht
jetzt in `HEAD`. → `13-…md`

---

## 4 · Zwei eigene Fehler, offen protokolliert

**Ich habe behauptet, das IDS-PDF sei unvollständig (8 von 39 Seiten). Das war falsch.** `file`
zählt bei diesem PDF falsch; `pdfinfo` meldet 39. Dazu hatte ich selbst nur acht Seiten
angefordert und die Rückgabe als Messwert gelesen. Drei Indizien, alle von mir erzeugt.

**Daraus folgte ein zweiter Fehler:** Ich habe die Preisregel aus der XSD erschlossen und
26,10 € statt 522 € behauptet. Der Anhang, der es klärt, stand im selben PDF, das ich für
unvollständig erklärt hatte. **Dein `03-data-quality-report.md` hatte recht** und bleibt gültig.

Beide Rücknahmen stehen in `docs/quellen/ids/LIESMICH.md` §0, mit dem Prüfbefehl, der die Frage
entscheidet.

---

## 5 · Ballbesitz

| Rolle | Was |
|---|---|
| **Yama** | push (29) · die drei Messbefehle · Kenntnisnahme E4a |
| **Generator** | `AUF-P1-S2-c` (fusion_forms) · `AUF-P1-S2-d3` (Bildname) · danach D-3 aus `12-…md`. **Nicht die Instanz, die die Aufträge geschnitten hat.** |
| **Evaluator** | wartet auf den Generator; die Rot-Probe ist in beiden Aufträgen als Pflichtbeleg verankert |
| **Planner** | ruht bis zu den Messwerten. Schritt 3 hängt an der Dublettenzahl. |

---

## 6 · Offen, benannt

| # | Was | Bei wem |
|---|---|---|
| 1 | `ergebnis-2026-08.txt` | Yama |
| 2 | Testsuite-Baseline | Yama |
| 3 | `warenkorb_senden_2_5_1.xsd` | ITEK |
| 4 | Codeliste `ReferenzType` (2.5.1) | ITEK |
| 5 | Open-Masterdata-Spezifikation — nicht öffentlich, für Verbandsmitglieder kostenlos | Yama bei ITEK |
| 6 | Defekt c: `config/services.php` hat weiterhin **zwei** `'fusion_forms'`-Schlüssel | Generator |
| 7 | `docs/handoff-status.md` ist 1,9 MB und laut eigener Kopfzeile Archiv — der Skill `governance-zyklus` zeigt trotzdem darauf | nur Yama (Direktive) |

---

## 7 · Was als Nächstes sinnvoll ist, ohne dich

- **`LI` und `SV` bauen** (`LIESMICH.md` §9.6/§9.7). Zwei winzige Funktionen, vollständig
  spezifiziert, ohne Warenkorb und ohne Preise. Sie beantworten je Verbindung, welche Zugangsdaten
  nötig sind und welche Version ein Shop kann — statt beides zu raten. Billigster echter Fortschritt
  am IDS-Strang.
- **XSD-Validator als Test.** Die Schemata liegen jetzt im Repo; ein Test, der die echten
  ITEK-Beispiele validiert, ist der erste belastbare IDS-Test überhaupt.
- **`10-target-domain-model.md` §1 korrigieren** — die Pfadliste dort ist durch `11-…md` überholt.
