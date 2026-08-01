# Übergabe-Prompt für die neue Sitzung

*Alles unter diesem Strich in die neue Sitzung kopieren.*

---

Du arbeitest an meiner Laravel-App **`ticket`** (CRM/ERP für einen Elektro-/SHK-/PV-Fachbetrieb, Solar Aspekt). Wir bauen sie zu einer zentralen Produktdaten-, Beschaffungs- und Kalkulationsplattform aus, angebunden an **IDS-Connect**, **Open Masterdata (OMD)**, **ETIM/BMEcat** und **E-Rechnung (ZUGFeRD/XRechnung) + ODX**. Die App liegt lokal auf meinem Rechner unter `/Users/yamanuri/Documents/ticket` (per Ordnerfreigabe erreichbar). Stack: Laravel 11.44, PHP 8.2, MySQL (`ticket`, Port 3307).

## Zuerst lesen — hier steht alles

Der vollständige Stand liegt auf meiner Platte unter **`docs/product-data/`**. Lies **vor** jeder Aussage diese Dateien; sie sind die Wahrheit, nicht deine Vermutung:

- `01-repository-inventory.md` — Architektur, Prozesse, Standards-Ist
- `02-database-inventory.md` — Produkt-/Preis-/Medien-Tabellen mit Bewertung
- `03-data-quality-report.md` — Datenqualität + 47 lesende Prüfabfragen (`evidence/query-results/datenqualitaet-pruefabfragen.sql`)
- `04-crm-erp-capability-matrix.md` — CRM/ERP-Abdeckung
- `10-target-domain-model.md` — **Identitätsspezifikation** (Entwurf, mit offenen Entscheidungen E1–E5, §4a `sku`)
- `20-implementation-roadmap.md` — Fahrplan: 8 Pakete, Paket 1 mit 5 Schritten

Lade außerdem den Skill **`governance-zyklus`** — er ist der verbindliche Arbeitsrahmen.

## Arbeitsweise (verbindlich)

- **Rollen trennen:** Planner ≠ Generator ≠ Evaluator. Sag an, in welcher Rolle du bist. Melde als Generator „umgesetzt", nie „grün" — die Abnahme macht ein unabhängiger Evaluator.
- **Spur je Vorgang:** A = voller Zyklus (Geld/Daten/Schema/Recht), B = Kurzspur (nur Markup/Text). Im Zweifel A.
- **Jede Ist-Aussage braucht eine Fundstelle (Datei:Zeile).** Behaupte **nie** „unbenutzt / harmlos / sicher", ohne den **vollständigen Codepfad gelesen** zu haben — ein grep beweist nur, dass ein Wort vorkommt, nicht was es bedeutet. Was nur gegrept ist, ist ANNAHME oder OFFEN, kein Befund. *(Das war die Fehlerquelle der Vorsitzung — bitte konsequent vermeiden.)*
- **Nur lesende Analyse ohne Auftrag.** Kein Schreiben am Bestand ohne Freigabe.
- **Umgebungsgrenze:** In der Cloud-Analyse gibt es **kein `php` und kein `mysql`**, und die DB auf `127.0.0.1:3307` ist von dort **nicht** erreichbar. Bestandszahlen kann nur ich (Yama) per Prüfabfragen liefern.

## Was schon feststeht (Kernbefunde, alle belegt)

1. **Kein Artikelschlüssel.** `products` hat `article_no/ean/sku/model` **ohne einen einzigen Index oder Unique**. **Sechs** Schreibpfade legen Artikel nach sechs verschiedenen Schlüsseln an, keiner normalisiert. → Zwillinge zwangsläufig. (`03` §3.1, `10`)
2. **`sku` ist dreideutig**, nicht unbenutzt: an `products` = Zweitname der Artikelnr, an `offer_product_lists` = Positionsrolle (`SET-18-MAT`), im Connector = Lieferantennr. Codekommentar `SupplierProductImportService.php:251-255` legt aber die richtige Absicht fest: `article_no` = Hersteller-Nr, `sku` = Lieferanten-Nr. (`10` §4a)
3. **Preise ohne Zeitachse**, vierfach abgelegt, `decimal(10,2)` statt der von IDS geforderten 10,4. Keine Historie, keine Staffel, keine Gültigkeit. (`03` §3.2)
4. **IDS vierfach gebaut, nur Warenkorb rein**, Bestellung raus fehlt. OMD-Client fertig aber nicht verdrahtet. DATANORM Skelett. **Keine E-Rechnung** trotz Frist 31.12.2026. (`01`, `04`)
5. **Preislogik-Kernfehler:** `NetPrice` ist die Positionssumme, nicht der Stückpreis — heute Faktor 50 falsch (50-m-Kabel: 26.100 statt 522 €). Am echten ITEK-Beispiel verifiziert.
6. **Beschaffungsstrang datenseitig leer** (supplier_*, imported_ids_items, goods_receipts = 0 Zeilen) → günstigster Moment für Strukturänderungen.

## Offene Entscheidungen, die ich (Yama) treffen muss

- **E1** Darf die unterste Leiterstufe (Textvergleich) automatisch zuordnen? *(Vorschlag: nein, nur vorschlagen)*
- **E2** Hersteller vs. Marke trennen? *(Vorschlag: ja, aber erst Paket 2)*
- **E3** Pflichtfeldkatalog „vollständiger Artikel" — sind die 3 Stufen richtig geschnitten?
- **E4** Vorlagen-Punchout (`OfferTemplateSupplierController`) stilllegen oder auf den zentralen Service umhängen?
- **E5** Bestätigen wir `article_no` = Hersteller-Nr, `sku` = Lieferanten-Nr? *(Vorschlag: ja — spart eine Spalte)*
- **fusion_forms:** `config/services.php:34,38` hat den Schlüssel doppelt (Webhook-Auth!). Welches Token gilt — `FUSION_FORMS_TOKEN` oder `FUSION_WEBHOOK_TOKEN`? Bis dahin **nicht anfassen**.

## Repo-Zustand beim Übergeben (Stand 2026-08-01)

- Branch `auto/hausplaner-integration`, **43 Commits ungepusht** (= kein Backup außerhalb der Maschine — bitte pushen).
- **Drei uncommittete Code-Änderungen von mir** (Paket 1 Schritt 2, verifiziert, aber vom Evaluator noch **nicht** abgenommen):
  - `routes/web.php` — tote Route `ids.search.forward.inline` entfernt
  - `app/Http/Controllers/DatanormController.php` — View `datanorm.upload` → `admin.datanorm.upload`
  - `app/Models/ProductImage.php` — `$fillable` `title`→`name` (Spalte `title` existiert nicht)
- `docs/product-data/` liegt als **untracked** auf Platte (noch nicht `git add`).
- Weitere modifizierte Dateien im Baum (`hausplaner*`, `PRUEFER-BEFUNDE.md`) sind **nicht von mir** — nicht mit `git add -A` einsammeln, nur die eigenen Pfade stagen.

## Nächster sinnvoller Schritt

Entweder (a) ich beantworte E1–E5, dann formulierst du Paket 1 Schritt 3+4 als Spezifikation aus — oder (b) du lässt als **Evaluator** erst die drei Code-Änderungen gegen die Abnahmekriterien in `20` Schritt 2 prüfen (Testsuite läuft nur auf meinem Rechner). Frag mich, womit ich anfangen will.
