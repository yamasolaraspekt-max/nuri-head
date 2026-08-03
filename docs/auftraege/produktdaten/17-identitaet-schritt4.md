# 17 · AUF-P1-S4 — ProductIdentityService: eine Leiter, neun Pfade, ein Riegel

```yaml
auftrag:
  id: AUF-P1-S4
  strang: produktdaten
  status: bereit           # B8-Gegenlesen 03.08.: TRAEGT MIT AUFLAGE, Auflage eingearbeitet
  spur: A
  heimat: ticket
  anlass: "Neun Schreibpfade mit acht Identitaetsbegriffen auf products (Zensus 11-…md §1);
           Preis-am-falschen-Artikel-Defekt §2.1 latent, faellt beim ersten echten Import an."
  ziel: "ProductIdentityService (Leiter §4, Normalisierung §5, Schnittstelle §6) bauen,
         additive Migration §7 ziehen, alle 9 Stellen nach §8 umstellen, Verriegelungstest
         setzen. Schalter produkt.identitaet.aktiv, Default false."
  nicht_ziel: "Kein unique-Constraint. Keine Bestandsbereinigung, kein Backfill, kein
               Marken-Typisieren (Paket 2). Keine Trennung Hersteller/Marke (E2). Keine
               Stilllegung Weg A/D (Schritt 5). Keine Preislogik (Paket 3). Keine Aenderung
               an offer_product_lists.sku (Paket 2, Posten 0). Siehe 11-…md §12."
  vorbedingung: "Stand gepusht (Remote = einzige Kopie ausserhalb der Maschine). Baseline
                 der Suite notieren (Stand 03.08.: 833 passed / 2966 assertions)."
  spezifikation: "docs/product-data/11-identitaetsspezifikation-paket1-schritt3-4.md ist die
                  fuehrende Spezifikation (§4-§12 + Nachtrag §14). Dieses Blatt DUPLIZIERT sie
                  nicht - es bindet sie."
  gegengelesen_von: evaluator   # getrennte Instanz; Urteil TRAEGT MIT AUFLAGE
  gegengelesen_am: 2026-08-03
  befund: >
    Zensus 9/9 heute unveraendert bestaetigt (gleiche Stellen, gleiche Zeilen ausser
    SupplierConnectorService §2.1: heute :654 statt :657). Kanten 7/8 und §2.1 am Code
    nachgeprueft, wahr. §14-Messwert brands manufacturer 0/50 unabhaengig nachgemessen.
    Auflage (eingearbeitet): sechster §12-Ausschluss offer_product_lists.sku ins nicht_ziel.

scope:
  population_command: "grep -rn 'Product::firstOrCreate\\|Product::updateOrCreate\\|Product::create\\|new Product(' app/ database/ --include=*.php | grep -v 'ProductImage\\|ProductPrice\\|ProductMedia' | wc -l"
  ausgangswert: "9 Schreibstellen (Zensus 11-…md §1, 01.08.)"
  pfade:
    - app/Services/Product/Identity/ProductIdentity.php
    - app/Services/Product/Identity/IdentityMatch.php
    - app/Services/Product/Identity/ProductIdentityService.php
    - config/produkt.php
    - database/migrations/  # eine additive Migration products + product_identity_suggestions
    - app/Services/Suppliers/SupplierProductImportService.php
    - app/Services/Suppliers/SupplierConnectorService.php
    - app/Http/Controllers/Product/IDS/gconline/IdsController.php
    - app/Http/Controllers/Product/ProductImportController.php
    - app/Services/ProductCsvImporter.php
    - app/Http/Controllers/Product/ProductController.php
    - database/seeders/HeatpumpSeeder.php
    - tests/Feature/Product/Identity/
  ausschluesse:
    - stelle: "app/Services/Spec/SpecImportService.php"
      grund: "Kein Schreibpfad (nur :352 lesend, belegt). Uebernimmt nur die Normalisierungsregel."
      entschieden_von: planner
    - stelle: "supplier_article_map"
      grund: "Eigener korrekter Unique, bleibt unangetastet."
      entschieden_von: planner

kriterien:   # = 11-…md §10, hier als pruefbare Posten
  - id: K-01
    typ: adversarial
    kritikalitaet: P1
    aussage: "Die Verriegelung greift und KANN rot werden."
    pruefung:
      typ: gate
      schritte: "Verriegelungstest (11-…md §8) gruen; dann Rot-Probe: probeweise ein
                 Product::create([]) irgendwo einfuegen -> Test MUSS rot werden. Rohausgabe
                 beider Laeufe vorlegen, danach zuruecksetzen."
      erwartet: "gruen / rot / gruen, drei Rohausgaben"
    ausgefuehrt_von: generator

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "Doppelimport erzeugt EINEN Artikel."
    pruefung:
      typ: gate
      schritte: "Dieselbe Datei zweimal durch Pfad 1 (gegen ticket_testing);
                 SELECT COUNT(*) auf die article_no"
      erwartet: "1"
    ausgefuehrt_von: generator

  - id: K-03
    typ: coverage
    kritikalitaet: P1
    aussage: "Alle 16 Kantenfaelle aus 11-…md §9 haben je einen benannten Test."
    pruefung:
      typ: gate
      schritte: "Testnamen gegen §9 abgleichen, 16/16, keine Luecke. Kante 14 ist laut
                 Nachtrag §14 der REGELFALL im Bestand (brands manufacturer = 0/50) -
                 der Test dafuer bildet genau das ab."
      erwartet: "16 benannte Tests, alle gruen"
    ausgefuehrt_von: generator

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Schalter traegt: aktiv=false ist byte-gleich zu heute."
    pruefung:
      typ: gate
      schritte: "Suite mit produkt.identitaet.aktiv=false gruen UND ein Import erzeugt
                 byte-gleiche Zeilen wie vor der Aenderung (SELECT * ... ORDER BY id,
                 beide Laeufe als Rohausgabe). Wichtigster Fall (Kante 15) - der Rueckweg."
      erwartet: "byte-gleich, Rohausgaben"
    ausgefuehrt_von: generator

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Keine Regression."
    pruefung:
      befehl: "php artisan test"
      erwartet: ">= 833 passed / 0 fail (Baseline 03.08.); neue Tests kommen dazu"
    beleg: rohausgabe
    ausgefuehrt_von: generator

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Migration umkehrbar (additive Spalten + Indizes + suggestions-Tabelle)."
    pruefung:
      typ: gate
      schritte: "migrate, migrate:rollback, SHOW COLUMNS vorher/nachher auf ticket_testing"
      erwartet: "spaltengleich, Rohausgabe beider Laeufe"
    ausgefuehrt_von: generator   # Hinweis: rollback ist fuer manche Instanzen gesperrt -
                                 # dann Yama, wie bei AUF-IDS-LI-SV K-07

selbstnachweis:
  preflight: "./scripts/auftrag-pruefen.sh docs/auftraege/produktdaten/17-identitaet-schritt4.md"
  gegenprobe: "K-01 - ein Verriegelungstest, der nicht rot werden kann, verriegelt nichts."
```

---

## Rollen

**Planner:** dieses Blatt + Spezifikation `11-…md` (zwei verschiedene Planner-Instanzen).
**Gegenlesen (B8):** Evaluator des Strangs `produktdaten` — prüft Blatt gegen Spezifikation,
besonders: Umstellungstabelle §8 vollständig? Kantenliste §9 gegen den heutigen Code noch wahr?
**Generator:** setzt um — **nicht** die Instanz, die Blatt oder Spezifikation geschrieben hat.
**Evaluator (Abnahme):** frische Instanz, misst alle sechs Kriterien selbst nach.

## Reihenfolge für den Generator

1. Service + Wertobjekte + Config (rein additiv, noch kein Aufrufer).
2. Migration (additiv, `nullable`, Indizes) — gegen `ticket_testing` proben.
3. Tests für Leiter + Normalisierung (16 Kantenfälle) gegen den Service allein.
4. Die 9 Stellen umstellen, **eine je Commit-Scheibe**, Reihenfolge wie §8 — nach jeder
   Scheibe Suite.
5. Verriegelungstest zuletzt — er ist erst erfüllbar, wenn alle 9 umgestellt sind.

## Ledger

```
PLANNER 2026-08-03 · AUF-P1-S4 geschnitten · Spur A · Heimat ticket
  Schritt 3 geschlossen (Messwerte in 11-…md §14, Freigabe Yama 03.08.).
  Blatt bindet die Spezifikation 11-…md §4-§12, dupliziert sie nicht.
  Neuer Messbefund eingebunden: brands manufacturer 0/50 -> Stufe 2 im Bestand tot,
  Kante 14 ist Regelfall; Marken-Typisierung = Paket 2, kein Beifang.
  Ballbesitz: Evaluator (Gegenlesen nach B8), dann Generator.
```
