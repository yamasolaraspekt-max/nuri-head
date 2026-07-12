# Bereich 2 — Angebotserstellung: 3 Wege, eine Wahrheit, Template-/Matching-System — Konzept

**Stand:** 2026-07-12 · **read-only · nur Konzept** · kein Bau/Refactor/Import/Löschen/Überschreiben/Migration/Automatisierung.
**Grundlage (firsthand belegt):** Bereich-2-Inventur/Verifikation/Bewertung; Ist-Stand `offer_templates`/`OfferTemplateController`/`OfferTemplatePickerController`/`processTemplate` (Live-Code 2026-07-12).
**Rahmen:** Ziel-Wahrheit Positionen = `offer_details.sections` (Yama bestätigt); Server-Pricing (P1-a); Operanden-Gate; „eine Wahrheit je Sachverhalt".

> **Leitsatz:** Vorlage, manuell und Tool sind **drei Eingänge in denselben Angebotsentwurf** — nie drei Angebotswelten. Die führende Struktur ist immer `offer_details.sections`; Vorlagen sind gespeicherte `sections` mit Metadaten; Tools erzeugen einen `sections`-**Vorschlag**, den der Mensch prüft und übernimmt.

---

## Teil A — Bestandsaufnahme (firsthand, 2026-07-12, `datei:zeile`/Tabelle)

**Grundsatz: nicht von null. Die 3 Wege bauen auf vorhandenem Code auf.** Kernbefund: Weg 1 (Vorlage) und Weg 2 (manuell) existieren **technisch bereits**; Weg 3 (Tool→Angebot) und ein Matching-System **fehlen**. Es gibt mehrere Alt-Generationen — vieles ist Legacy, einiges eine **lebende Zweitwahrheit**.

### A.1 Angebots-Erstellung — vorhandene Bausteine

| Baustein | Status | Rolle | Beleg |
|---|---|---|---|
| `OfferWizardController` (`index`/`smart`/`createOffer`/`searchProducts`/`groupSet`) | **AKTIV** | Kern-Wizard (Weg 2 manuell) | `routes/web.php:3277-3305`; `OfferWizardController.php:63,94,670,1496` |
| `resources/views/admin/offer/configuration/offer/config.blade.php` | **AKTIV** | **Der** Wizard-Editor (25.064 Z. Inline-JS, Positionsbaum, Set-/Artikel-Einfügen, Preis; Tailwind/jQuery/Sortable/Select2/Quill per CDN = eigene Insel) | `OfferWizardController.php:63`; JS `:6297-7336` |
| `.../wizard-smart.blade.php` (`smart()`) | **AKTIV (unklar Beta)** | zweiter, schlankerer Wizard-Einstieg | `OfferWizardController.php:94` |
| `OfferController` (`saveDocument`→`processOffer`/`processTemplate`) | **AKTIV** | Speicherpfad + „als Vorlage" | `OfferController.php:2048,2068,2101` |
| `OfferFolderController` (`folder-show`, `hydrateSectionsFromMasterSetComponents`) | **AKTIV** | Ordner/Detail + Katalog-Rehydrierung der Positionen | `OfferFolderController.php:787,963,1028` |
| `OfferDetailsController` (`masterSetDetails`, `updates`) | **AKTIV** | Detail-Speicherung; **liest noch Alt-Katalog** `product_master_sets` | `OfferDetailsController.php:178,292-294` |
| `offers`/`offer_folders`/`offer_details(sections)`/`offer_product_lists` | **AKTIV** | Angebots-Datenmodell; `sections`=führend | (Bereich-2-Inventur) |
| `Old/OfferConfigController` + `offer/offer/configuration/wp/index.blade.php` u. a. `Old/Offer*` | **LEGACY-TOT** | alte Wizard-/Set-Generation, **keine Route** | `Old/OfferConfigController.php:30`; grep `Old\Offer*` in routes = 0 |
| `OffersController` (Plural) + `offer.save` + `view/offer_view.blade.php` | **RESERVE-TOT** | alter Speicherweg, nirgends verlinkt | `routes/web.php:3396`; `OffersController.php:73,93` |
| `use App\Http\Controllers\OfferGreetingController` | **DANGLING** | Import auf nicht existente Top-Level-Klasse (nur `Old/…` existiert) | `routes/web.php:148` |

### A.2 Vorlagen (Weg 1) — existiert end-to-end

| Baustein | Status | Rolle | Beleg |
|---|---|---|---|
| `OfferTemplatePickerController::useTemplate` | **AKTIV** | **echte Instanziierung** Vorlage→Angebot: erzeugt `Offer`(draft)+`OfferFolder`+`OfferDetail`, kopiert `sections`/Branding **1:1**, `usage_count++` | `OfferTemplatePickerController.php:319-421` |
| `OfferTemplateController::wizardSearch`/`wizardShow` | **AKTIV** | Vorlagen-Suche + Vorbefüllen des Wizards | `OfferTemplateController.php:712-799` |
| `OfferController::processTemplate` (`is_template`) | **AKTIV** | „als Vorlage speichern" — **ohne Scrub/Version/Freigabe** | `OfferController.php:2068-2099` |
| `offer_templates` (Felder) | **AKTIV** | Vorlagen-Speicher; `sections`=**gleiche Struktur** wie `offer_details` | `OfferTemplate.php:9-52` |
| Klassifikation heute | — | **nur 4 FKs**: `department_id`, `article_group_id`(=Gewerk-Näherung), `brand_id`, `distributor_id` + `leistung`/`description`/`usage_count` | Migr. `…115247` |
| Suche heute | **schwach** | reines `LIKE %..%` über name/desc/company/**sections-JSON** + In-PHP-Substring-Scoring 35–100; **kein Index, kein Keyword-Matching** | `OfferTemplateController.php:722-729,850-887` |
| Favoriten | **DOPPELT/inkonsistent** | Pivot `offer_template_favorites` (neu, führend) **vs.** Spalte `is_favorite` (alt, Picker sortiert noch danach) | `OfferTemplateController.php:242-271` vs. `OfferTemplatePickerController.php:57,65` |
| Keyword/Tag/Produktart/Objektart/Region | **FEHLT** (grep negativ) | keine solchen Spalten | — |
| Scrub/Anonymisierung · Version · Freigabe | **FEHLT** | `offerNormalizeSections` = reiner Passthrough; nur weicher `stamp`-Marker, kein Workflow-Status | `OfferController.php:1712`; `OfferTemplateController.php:273-330` |

### A.3 Sets/Artikel/Katalog + Klassifikations-Rohstoff

| Baustein | Status | Rolle | Beleg |
|---|---|---|---|
| `master_sets`/`master_set_components`/`master_set_groups` | **AKTIV, führend** | neuer Set-Katalog; Wizard lädt darüber | `OfferWizardController.php:670-756`; Migr. `2026_01_05_*` |
| **`product_master_sets`** (`ProductMasterSet`) | **⚠️ LEGACY, aber LEBENDE Brücke** | Alt-Katalog (2024); **aktiver** `OfferDetailsController` validiert/liest ihn noch (`exists:product_master_sets`) | `OfferDetailsController.php:178,294,385,415,499` |
| `products`/`article_groups`/`sub_article_groups`/`brands` | **AKTIV** | Artikel-/Preis-Wahrheit; **`article_groups`=Gewerk-Achse** | `OfferWizardController.php:1496-1554` |
| Klassifikations-Felder heute | — | **relational** via `article_groups`(+sub+brand) + WP/PV-Attribute (`products.heatpump_type`/`construction_type`/`refrigerant`; `product_heat_pump_specs.geraetetyp`/`serie`). **Kein** freies `gewerk`/`produktart`/`tag`-Feld; `products.category`=schwacher Default-String | Migr. `add_fields_for_katalog…` |
| `lead_product_lists` | **AKTIV** | **Gewerk-Träger je Lead** (`product_id`→`article_groups`); Angebot↔Gewerk-Brücke; Wizard liest es | `OfferWizardController.php:226-269`; `ArticleGroup.php:18` |
| `CatalogDeviceRepository` | **AKTIV** | Geräte-Wahrheit für Tools (Spec-Tabellen), getrennt vom kaufm. Katalog | `CatalogDeviceRepository.php:23-52` |

### A.4 Tool→Angebot + Matching — **fehlen beide**

| Baustein | Status | Befund | Beleg |
|---|---|---|---|
| Tool→Angebots-`sections`-Adapter | **FEHLT komplett** | einziges `sections`-Service = `CatalogPriceGuard` (Preis-Wächter, kein Adapter) | grep `sections` in `app/Services/**` |
| `HeizkoerperController::uebernehmen` | **AKTIV (Sonderfall)** | einzige Tool-Brücke, aber in `deal_measurement_items` (Measurement-Ebene, **Preise NULL**), **nicht** ins Angebot | `HeizkoerperController.php:122-232`; `routes/web.php:2483` |
| Energie-Controller (WP/PV/Heizlast/Konzept) | — | erzeugen **nichts** Angebotsseitiges (grep leer) | `app/Http/Controllers/Energie/*` |
| **Matching-System** | **EXISTIERT NICHT** | kein Produkt-/Angebots-/Vorlagen-Matching im Code | grep `match/recommend/suggest` = sachfremd |
| `SmartroutingService` | **RESERVE (dormant)** | Anker→Spezifität→Priorität-Muster, 5 Tests grün, **0 Aufrufer**; routet **Formulare**, nicht Angebote | `SmartroutingService.php:27-76`; Test vorhanden |
| `Anforderungsprofil` (+`_werte`, `AnforderungsprofilService`) | **RESERVE, beste Eingabe** | strukturierte Bedarfs-/Objektdaten, versioniert, **verankert an Objekt/Gewerk** (Whitelist); heute nur Heizlast-Input, nicht an Matching | `Anforderungsprofil.php:47-50` |
| `LeadProductChecklistValue.filled_values` | **AKTIV (Formular)** | strukturierte Antworten je Gewerk/Objekt — potenzielle Matching-Eingabe, nicht konsumiert | `LeadProductChecklistValue.php:8-23` |
| `CatalogPriceGuard` (P1-a) | **NEU (uncommitted)** | Preis-Wahrheit für alle Wege | `app/Services/Offer/CatalogPriceGuard.php` |

### A.5 Bewertung — was nutzen, was Vorsicht

- **Als Kern nutzen (nicht neu bauen):** `offer_details.sections` (führend) · `offer_templates`+`processTemplate`+`useTemplate` (Vorlagen-Weg da) · `master_sets`/`products`/`article_groups` (Katalog) · `lead_product_lists` (Gewerk-Anker) · `Anforderungsprofil` (Matching-Eingabe) · `SmartroutingService` (Matching-Muster) · `CatalogDeviceRepository` (Geräte).
- **⚠️ Gefährlich (Doppelwahrheit, nicht anfassen ohne Rückfallpfad):** `product_master_sets` als **lebende** Zweit-Katalog-Brücke (vom aktiven `OfferDetailsController` gelesen) — **nicht löschen**, erst Ablösung mit Variante-B-Plan. `offer_product_lists` (relationale Zweit-Positionswahrheit neben `sections`). Doppeltes Favoriten-System.
- **Legacy/tot (erhalten, später belegt stilllegen — kein Löschen):** `Old/Offer*`, `OffersController`+`offer.save`+`offer_view`, die alten `offer/offer/**`- und `set/old code`-Views, `Old/OfferConfigController`+`wp/index.blade`, dangling `OfferGreetingController`-Import.
- **Fehlt (neu, aber additiv auf Bestehendem):** Template-Metadaten/Keywords/Tags · Scrub/Version/Freigabe · Tool→`sections`-Adapter · Matching.

---

## 1. Zielbild

**Drei Wege → ein Entwurf → eine Wahrheit → optional neue Vorlage → später Matching.**

```
[1 Vorlage]   ┐
[2 Manuell]   ├──►  Angebotsentwurf (offer_details.sections)
[3 Tool/Ausl.]┘        │  Positionen · Sets · Artikel · Lohn · Zeiten · Texte · techn. Annahmen · Varianten · Warnungen
                       ▼
                   Kalkulation (Server-Pricing, P1-a)
                       ▼
                   Prüfung (Mensch)
                       ▼
                   Angebot speichern (offer_details.sections + offer_folders + offers)
                       ▼
                   optional: als Vorlage speichern (offer_templates + Metadaten + Keywords/Tags, kundendaten-frei)
                       ▼
                   später: Bibliothek → Matching (Kunden-/Objekt-/Formulardaten → Vorlagen-/Tool-Vorschlag)
```

**Warum eine Wahrheit:** `offer_details.sections` ist bereits die führende Angebotsstruktur; `offer_templates.sections` nutzt **dieselbe** Struktur (belegt: gleiche JSON-`sections` + gleiche Marker-fähigkeit). Damit ist „drei Wege, eine Wahrheit" kein Neubau, sondern konsequente Nutzung des Vorhandenen. **Kein Weg darf eine zweite Positions-Struktur einführen** (Verbot: `offer_product_lists`-Wiederbelebung, Tool-eigene Angebots-Tabellen, JS-Rechenwahrheit).

---

## 2. Die drei Angebotswege

### Weg 1 — Angebot aus Vorlage
- **Zweck:** typischen Fall schnell starten (Positionen/Sets/Lohn/Texte/Kalkulationslogik vorbelegt).
- **Nutzerablauf:** Vorlage suchen/filtern → auswählen → Kunde/Objekt zuordnen → Mengen/Preise/Texte/Sonderpositionen anpassen → prüfen → speichern.
- **Backend-Quelle:** `offer_templates` (`sections`, `leistung`, `department/article_group/brand/distributor`, `usage_count`) → beim Übernehmen wird `template.sections` zur `offer_details.sections`.
- **Frontend-Ablauf:** Vorlagen-Picker (**vorhanden:** `OfferTemplateController::wizardSearch/wizardShow`, `OfferTemplatePickerController::index`) → Wizard `config.blade.php` mit vorbelegten `sections`.
- **Daten, die entstehen:** neues `offer` + `offer_folder` + `offer_details` mit Positionen aus der Vorlage; `template.usage_count`/`last_used_at` hochzählen.
- **Editierbar:** alles (Mengen, Preise im Rahmen der Preis-Regel, Texte, Zusatzpositionen).
- **→ dasselbe Angebot:** die Vorlage IST schon `sections` → 1:1 in den Entwurf, danach Standard-Speicherpfad (P1-a greift auf Katalog-Positionen).
- **Risiken:** eingefrorene Vorlagen-Preise können vom Katalog abweichen → **Preis-Regel-Entscheidung** (§13). Veraltete Vorlagen. Kundendaten aus Alt-Angebot in Vorlage (§4).

### Weg 2 — Angebot manuell erstellen
- **Zweck:** Fallback, wenn es kein Tool/keine Vorlage gibt.
- **Nutzerablauf:** leeren Entwurf öffnen → Sets/Artikel aus Katalog + Lohn-/Freitext-/Sonderpositionen hinzufügen → kalkulieren → prüfen → speichern.
- **Backend-Quelle:** `master_sets`/`master_set_components`/`products` (Katalog) → Hydration in `sections` (**vorhanden:** `OfferWizardController`, `OfferFolderController::hydrateSectionsFromMasterSetComponents`).
- **Frontend-Ablauf:** Wizard `config.blade.php` (aktiv).
- **Daten:** `offer_details.sections` mit gemischten Knoten (Katalog-Komponenten + manuelle Positionen).
- **Editierbar:** alles.
- **→ dasselbe Angebot:** ist der native Speicherpfad (`processOffer`); Katalog-Knoten → P1-a-verifiziert, Freitext → `preis_quelle=manuell`.
- **Risiken:** Preise heute JS-nah (durch P1-a für Katalog-Knoten abgesichert; Freitext bleibt manuell). Zeitaufwand.

### Weg 3 — Angebot aus Tool / Auslegung
- **Zweck:** Fachtool (WP/PV/Heizkörper/Energie/Material) ermittelt Produktart, Produkte/Sets, Mengen, Lohn/Zeit, techn. Parameter, Varianten, Warnungen/Annahmen, Kalkulationsgrundlage → daraus ein **Angebots-Vorschlag**.
- **Nutzerablauf:** Tool ausfüllen (verankert an Objekt/Gewerk, §7) → berechnen → „als Angebotsentwurf übernehmen" → Wizard mit vorbefüllten `sections` → prüfen/anpassen → speichern.
- **Backend-Quelle:** Auslegungsservices (`Heizlast/*`, `Energie/*`, `Heizkoerper/*`) + `CatalogDeviceRepository` → **neuer Adapter** „Tool-Ergebnis → `sections`" (**fehlt heute** — die Auslegung ist stand-alone, `Auslegung→Angebot ⬜`).
- **Frontend-Ablauf:** Tool-View → „Übernehmen"-Aktion → Wizard `config.blade.php`.
- **Daten:** `sections` mit Knoten `preis_quelle='tool'`/technische Annahmen als Metadaten am Knoten/Abschnitt.
- **Editierbar:** alles; Tool-Werte sind Vorschlag, nicht Zwang.
- **→ dasselbe Angebot:** über den Adapter in `sections`, dann Standard-Speicherpfad + P1-a.
- **Risiken:** Tool-Ergebnis ohne Prüfung; Tool-eigene zweite Rechenwahrheit; heute rechnen aktive Frontends (`profit.blade.php`) Fachlogik im JS statt über die Services.

---

## 3. Einheitlicher Angebotsentwurf (konzeptionell)

Der Entwurf = `offer_details.sections` (JSON-Baum: Abschnitte → Positionen → Unterpositionen). Ein **Positionsknoten** trägt konzeptionell:

| Feld | Bedeutung |
|---|---|
| `kind`/`item_type` | article · master_set · master_set_component · labor · note · … |
| `component_id`/`product_id`/`master_set_id` | Katalog-Anker (für Server-Pricing) |
| `qty`/`price`/`ek`/`unit_price`/`purchase_price` | Menge + Preise (Katalog-verifiziert oder manuell) |
| **`preis_quelle`** | `katalog_verifiziert`/`katalog_korrigiert`/`katalog_fehlt`/`manuell`/**`tool`** (P1-a-Marker, erweiterbar) |
| **`herkunft`** (neu, konzeptionell) | `vorlage` · `manuell` · `tool` · `importiert` · `angepasst` |
| Lohn/Zeit | `labor_rows` (Stunden, Sätze) |
| Texte | Titel/Beschreibung/Hinweis |
| **technische Annahmen** | am Knoten/Abschnitt (z. B. Heizlast, Vorlauf, Bivalenzpunkt) — als Metadaten, nicht als Preis |
| **Varianten** | optionale Alternativpositionen (z. B. WP-Modell A/B) |
| **Warnungen** | Tool-/Plausi-Hinweise (Operanden-Gate) |

**Quelle der Position** wird pro Knoten festgehalten (`herkunft`), damit später sichtbar ist, was aus Vorlage/Tool/manuell kam und was der Nutzer angepasst hat (`angepasst`). **Kalkulation:** genau **eine** Server-Summen-Engine (heute zwei — Bewertung Punkt 3, eigener Posten); Preis-Wahrheit = Server (P1-a).

---

## 4. Vorlage speichern

- **Wann:** nach dem Speichern eines Angebots optional „als Vorlage speichern" (**Teil-vorhanden:** `processTemplate` erzeugt heute schon `OfferTemplate` aus `sections`).
- **Wer darf:** offene Entscheidung (§13) — Vorschlag: erstellen jeder Verkäufer (Status `entwurf`), **freigeben** nur eine berechtigte Rolle (Status `freigegeben`).
- **Was gespeichert wird:** `sections` (Struktur/Positionen/Sets/Lohn/Texte/Kalkulationslogik) + Metadaten (§5) + Keywords/Tags.
- **Was NICHT gespeichert werden darf:** **Kundendaten** (Name, Adresse, Kontakt, konkrete Objektadresse, PLZ nur als grobe Region wenn gewollt), individuelle Rabatte, kundenspezifische Sonderpreise, personenbezogene Notizen.
- **Anonymisierung:** beim Vorlagen-Speichern läuft ein **Scrub-Schritt** — Kundenfelder werden entfernt/geleert; Preise werden je nach Regel (§13) entweder als Richtwert behalten oder auf Katalog-Referenz reduziert (`component_id` bleibt, konkreter Preis wird beim späteren Anwenden neu abgeleitet).
- **Versionierung:** Vorlagen sind **versioniert append-only** — neue Fassung = neue Version, alte Version bleibt erhalten (nie überschreiben; deckt sich mit `rueckfall-archiv-regeln.md`). `version` + `supersedes_id`.
- **Freigabe:** `freigabestatus` (`entwurf`/`in_pruefung`/`freigegeben`/`archiviert`); nur `freigegeben` erscheint im Standard-Picker.
- **Alte Vorlagen erhalten:** `archiviert` statt Löschen; im Matching niedriger gewichtet, aber auffindbar.

---

## 5. Template-Metadaten / Keywords (Matching-Grundlage)

**Heute vorhanden** an `offer_templates`: `name`, `department_id`, `article_group_id`, `brand_id`, `distributor_id`, `leistung`, `description`, `usage_count`, `is_favorite`. **Es fehlen alle Domänen-/Matching-Metadaten.** Vorschlag (additiv, konzeptionell — als Spalten und/oder `meta`-JSON):

| Feld | Typ | Zweck |
|---|---|---|
| `titel` | Text | Anzeigename (≠ interner `name`) |
| `produktart` | Enum/Ref | WP · PV · Speicher · Heizkörper · … |
| `gewerk` | Ref `article_groups` | Zuordnung Gewerk |
| `objektart`/`gebaeudetyp` | Enum | EFH · MFH · Gewerbe · Neubau · Bestand |
| `kundentyp` | Enum | Privat · Gewerbe · WEG |
| `typische_leistung` | Zahl+Einheit | z. B. kW/kWp-Band |
| `mengenbereich` | Range | typische Stückzahlen/Flächen |
| `technische_merkmale` | Tags | Vorlauf, Dachart, Netzanschluss … |
| `einsatzbedingungen` | Tags | Sanierung/Neubau, Flachdach/Schrägdach … |
| `region_plz` | optional | grobe Region (kein konkreter Kunde) |
| `keywords` | Freitext-Liste | nutzer­vergeben, Matching-Hilfe |
| `tags` | kontrollierte Liste | strukturiertes Matching |
| `ausschlusskriterien` | Liste | wann Vorlage NICHT passt |
| `preisbereich` | Range | grobe Angebotssumme |
| `zeit_lohn_profil` | Kennwerte | typische Stunden/Rollen |
| `benoetigte_eingaben` | Feld-Liste | welche Daten der Fall braucht |
| `verwendete_sets_artikel` | Refs | `master_sets`/`products`-IDs (aus `sections` ableitbar) |
| `empfohlene_tools` | Refs | welche Auslegung passt |
| `version`/`freigabestatus`/`supersedes_id` | | Versionierung/Freigabe |

**Regel:** `keywords` (frei) sind die niedrigschwellige Erfassung; `tags`/Enums sind die belastbare Matching-Basis. Beide erfassen, aber Matching primär auf strukturierte Felder stützen (Freitext nur ergänzend).

---

## 6. Matching-Konzept (später)

**Eingaben:** Kundendaten, Objektdaten (`lead_alternative_adds`), Formularantworten (`Anforderungsprofil`), Gewerk (`lead_product_lists`), Produktinteresse, techn. Angaben, PLZ/Region, Budget optional.

**Ausgabe (klassifiziert):**
- **Sicherer Treffer** — Produktart + Gewerk + Objektart passen, keine Ausschlusskriterien verletzt, Pflichteingaben vorhanden → Vorlage direkt vorschlagen.
- **Möglicher Treffer** — Teilübereinstimmung → als Option zeigen, Unterschiede markieren.
- **Braucht Rückfrage** — Schlüsselangabe fehlt (z. B. Dachart) → **empfohlene nächste Frage** ausgeben (Operanden-Gate).
- **Keine passende Vorlage** — dann Tool-Pfad oder manuell vorschlagen.

Zusätzlich: **passende Tools**, **ähnliche frühere Angebote**, **fehlende Pflichtdaten**.

**Baustein:** `SmartroutingService` existiert (Reserve, FS-05, getestet, aber 0 Regeln/0 Aufrufer) — **Kandidat als Matching-Kern**, nicht neu bauen. Matching läuft **serverseitig**, liefert Vorschläge, **entscheidet nichts** (Vorschlag+Bestätigung).

---

## 7. Tool-Integration

- Tool erzeugt: technische Berechnung · Mengen · Produkt-/Set-Vorschläge · Lohn/Zeit · Warnungen/Annahmen · **Angebotsentwurf** (`sections`-Vorschlag).
- **Grenze (zwingend):** Tool erzwingt **kein** endgültiges Angebot. Ergebnis ist ein **Vorschlag**; der Nutzer prüft, passt an, übernimmt.
- **Anbindung:** ein **Adapter je Tool** „Ergebnis → `sections`-Knoten" (Produktart→Set/Artikel via `CatalogDeviceRepository`/`master_sets`; Mengen→qty; Lohn→`labor_rows`; Annahmen/Warnungen→Metadaten; `preis_quelle='tool'`, `herkunft='tool'`). Preise laufen anschließend durch **P1-a** (Katalog-Wahrheit).
- **Verankerung:** Tool-Eingang = Objekt/Gewerk über `Anforderungsprofil` (Ziel-Wahrheit Anker) — behebt zugleich das heutige Stand-alone-Problem.

---

## 8. Backend-Konzept — vorhanden / fehlt / unklar / nicht doppeln

| Baustein | Status | Rolle im Zielbild |
|---|---|---|
| `offer_details.sections` | **vorhanden, führend** | die eine Angebots-/Entwurfs-Wahrheit |
| `offer_folders` / `offers` | **vorhanden** | Varianten + Angebotskopf |
| `offer_templates` (+`sections`,`leistung`,dept/brand/…) | **vorhanden** (Struktur = `sections`) | Vorlagen-Speicher; **Metadaten fehlen** (§5) |
| `OfferTemplateController` (wizardSearch/Show), `OfferTemplatePickerController` | **vorhanden** | Vorlagen-Auswahl; Filter/Matching **auszubauen** |
| `processTemplate` (is_template) | **vorhanden** | „als Vorlage speichern" — **ohne Scrub/Metadaten/Version** |
| `master_sets`/`master_set_components`/`products` | **vorhanden** | Katalog-Quelle aller Wege |
| `CatalogPriceGuard` (P1-a) | **vorhanden (uncommitted)** | Preis-Wahrheit für alle Wege |
| `Anforderungsprofil` (+Adapter) | **vorhanden, unverdrahtet** | Anker + Operanden für Tool/Matching — **verdrahten, nicht neu** |
| Formular-Engine (`FormulaEvaluationService`/`VisibleIf`) | **vorhanden, nur Produkt-Admin** | Eingabe-Erfassung für Tool/Matching |
| Auslegungsservices (`Heizlast/Energie/Heizkoerper/*`) | **vorhanden, stand-alone** | Tool-Rechenkern; **Adapter→`sections` fehlt** |
| `SmartroutingService` | **vorhanden, Reserve (0 Regeln)** | Matching-Kern-Kandidat |
| **Template-Metadaten/Keywords/Tags** | **FEHLT** | §5 |
| **Tool→`sections`-Adapter** | **FEHLT** | §7 (Weg 3) |
| **Vorlagen-Scrub/Anonymisierung** | **FEHLT** | §4 |
| **Vorlagen-Versionierung/Freigabe** | **FEHLT** | §4 |
| **Matching-Regeln + Ausgabe-Klassen** | **FEHLT/UNKLAR** | §6 |
| **Preis-Regel Vorlage (fest vs. Katalog)** | **UNKLAR** | §13 |

**Nicht doppelt bauen:** keine zweite Positions-Struktur (nur `sections`); kein zweiter Katalog; keine Tool-eigene Angebots-Tabelle; kein zweites Matching neben `SmartroutingService`; keine JS-Rechenwahrheit.

---

## 9. Frontend-/UX-Konzept

**Einstieg „Angebot erstellen" → drei Kacheln:** „Aus Vorlage" · „Manuell" · „Über Tool/Auslegung". (Optional: wenn Objekt/Gewerk/Formulardaten vorliegen → oben **Vorschlagsleiste** aus dem Matching mit passenden Vorlagen/Tools.)

**Danach — einheitlicher Entwurfs-Screen** (für alle drei Wege gleich):
- Positions-Baum (Abschnitte/Positionen) mit **Herkunfts-/Preis-Quelle-Badges** (vorlage/tool/manuell; katalog_verifiziert/manuell) — *(Badges sind ein späteres Frontend-Paket; P1-a speichert die Marker bereits, rendert sie aber noch nicht.)*
- **Kalkulations-Panel** (Server-Summe, Marge, MwSt) — read-only Server-Wahrheit.
- **Technische Annahmen** (aus Tool) sichtbar, aufklappbar.
- **Warnungen** prominent (Operanden-Gate: fehlende Daten, Plausi).
- Aktionen: **Speichern** · **optional als Vorlage speichern** (öffnet Metadaten-/Keywords-/Tags-Maske + Scrub-Hinweis) · Variante hinzufügen.

**Ergonomie-Prinzip:** ein Screen, drei Startpunkte; der Nutzer sieht immer denselben Entwurf, egal woher er kam. Keine wegspezifischen Sonder-UIs.

---

## 10. Automatisierung (a/b/c)

- **(a) darf automatisch:** passende Vorlagen vorschlagen · ähnliche Angebote anzeigen · fehlende Pflichtdaten markieren · Tool empfehlen · `usage_count`/`last_used` pflegen.
- **(b) nur Vorschlag+Bestätigung:** Vorlage übernehmen · Tool-Ergebnis ins Angebot übernehmen · Preise/Kalkulation finalisieren · Angebot als Vorlage speichern (inkl. Scrub-Bestätigung).
- **(c) nicht automatisch:** endgültiges Angebot ungeprüft versenden · Preise ohne Regelbasis ändern · **Kundendaten in eine Vorlage speichern** · Vorlage ohne Freigabe in den Standard-Picker heben.

---

## 11. Risiken (Bewertung)

| Risiko | Bewertung | Gegenmaßnahme (konzeptionell) |
|---|---|---|
| **zweite Angebotswahrheit** | hoch | alle Wege → nur `offer_details.sections`; kein Tool-/Vorlagen-Eigenformat |
| **Kundendaten in Vorlagen** | hoch (Datenschutz) | Pflicht-Scrub + Verbot (c); Freigabe-Review |
| **falsches Matching** | mittel | Klassen sicher/möglich/rückfrage; nur Vorschlag; Ausschlusskriterien |
| **falsche Preise** | hoch | Server-Pricing (P1-a); Preis-Regel Vorlage (§13) |
| **zu viele schlechte Vorlagen** | mittel | Freigabestatus; Nutzung/Bewertung als Gewicht; Archivierung |
| **alte Vorlagen** | mittel | Versionierung append-only; `archiviert` niedriger gewichtet |
| **unklare Keywords** | mittel | strukturierte Tags führen, Keywords nur ergänzend |
| **Tool-Ergebnis ohne Prüfung** | hoch | Tool = Vorschlag, Mensch übernimmt (b); Warnungen sichtbar |
| **JS-Rechnung statt Server** | hoch | eine Server-Summen-Engine; JS nur Vorschau (Bewertung Punkt 3) |

---

## 12. Schrittweiser Fahrplan (jeder Schritt: Backend + Frontend + Testpfad)

1. **Angebotswege + führende Struktur bestätigen** (dieses Konzept) — *B:* keine Änderung · *F:* — · *Test:* Review.
2. **Template-Metadaten-Schema definieren** (§5) — *B:* additive Felder/`meta`-JSON (Konzept→Migration später) · *F:* Metadaten-Maske-Entwurf · *Test:* Schema-Review.
3. **„Als Vorlage speichern" minimal + Scrub** — *B:* `processTemplate` um Scrub + Grund-Metadaten erweitern · *F:* Speichern-Dialog mit Scrub-Hinweis · *Test:* Vorlage enthält keine Kundendaten (Unit/Feature).
4. **Keywords/Tags erfassen** — *B:* Felder + Validierung · *F:* Tag-/Keyword-Eingabe · *Test:* Persistenz + Anzeige.
5. **Vorlagen-Auswahl verbessern (Filter statt Volltext)** — *B:* Filter auf Metadaten im Picker · *F:* Filter-UI · *Test:* Filter liefert erwartete Vorlagen.
6. **Tool-Ergebnis → Angebotsentwurf (ein Tool zuerst, z. B. WP)** — *B:* Adapter Tool→`sections` (+ P1-a) · *F:* „als Entwurf übernehmen" · *Test:* Tool-Ergebnis erzeugt prüfbaren `sections`-Entwurf.
7. **Vorlagen-Versionierung/Freigabe** — *B:* `version`/`freigabestatus`/`supersedes_id` · *F:* Freigabe-Ansicht · *Test:* alte Version bleibt, nur freigegebene im Picker.
8. **Matching (später)** — *B:* `SmartroutingService` mit Regeln füttern; Eingaben aus Anforderungsprofil · *F:* Vorschlagsleiste · *Test:* Klassen sicher/möglich/rückfrage korrekt.

*(Reihenfolge folgt „①Konzept→②Workflow→③Verknüpfung→④Automatisierung": Struktur/Vorlage zuerst, Matching zuletzt.)*

---

## 13. Offene Fragen an Yama

1. **Vorlagen-Preise:** fest (Richtwert aus Angebot) **oder** immer aus Katalog neu ableiten (nur `component_id` speichern, Preis beim Anwenden via P1-a)? *(Empfehlung: Katalog neu ableiten — eine Wahrheit.)*
2. **Freigabe:** Wer darf Vorlagen freigeben (jede/r erstellt, Rolle X gibt frei)?
3. **Sichtbarkeit:** Vorlagen **global**, **pro Standort/Filiale** (`department`) oder **pro Gewerk** — oder kombinierbar?
4. **Alte Angebote als Vorlage:** dürfen bestehende Angebote nachträglich zu Vorlagen gemacht werden (mit Scrub) oder nur neue?
5. **Keywords vs. Tags:** wie stark strukturiert (kontrollierte Tag-Liste je Gewerk) vs. freie Keywords?
6. **Metadaten-Ablage:** additive Spalten an `offer_templates` **oder** eine `meta`-JSON **oder** eigene `offer_template_meta`-Tabelle?
7. **Matching-Start:** ab wann (Anzahl Vorlagen) lohnt Matching, und welches Gewerk zuerst (WP oder PV)?
8. **Scrub-Tiefe:** welche Felder gelten als kundendaten-sensibel (PLZ als Region behalten ja/nein)?

---

## 14. Vorschlag erster kleiner Umsetzungsschritt (auf Bestand, NICHT jetzt bauen)

**Schritt: „Als Vorlage speichern" um Scrub + additive Matching-Metadaten erweitern** — baut direkt auf `OfferController::processTemplate` + `offer_templates` auf (beide vorhanden), schafft **keine** neue Welt, und schließt einen realen Datenschutz-Mangel (heute kein Scrub).

- **Warum dieser Schritt zuerst:** Weg 1 (Vorlage) läuft bereits (`useTemplate`/`processTemplate`); die Lücke ist (a) Kundendaten wandern 1:1 in die Vorlage (Risiko), (b) es fehlen die Metadaten, die später Matching tragen. Beides ist additiv am vorhandenen Pfad lösbar — kein Katalog-/Wizard-Umbau.
- **Backend:** in `processTemplate` einen **Scrub-Schritt** vor `OfferTemplate::create` (Kundenfelder aus `sections`/`cover_text_html` entfernen); **additive, nullable** Metadaten (`produktart`, `objektart`, `kundentyp`, `keywords`, `tags` — als Spalten **oder** `meta`-JSON, §5/§13 Q6) aus dem Request übernehmen. `article_group_id` (Gewerk) wird bereits gespeichert.
- **Frontend:** im „als Vorlage speichern"-Dialog eine kleine Metadaten-/Keyword-Maske + Scrub-Hinweis (keine große Wizard-Änderung).
- **Testpfad:** Feature-Test „gespeicherte Vorlage enthält keine Kundendaten" + „Metadaten/Keywords persistiert"; Browser: Angebot → als Vorlage speichern → Vorlage öffnen → Kundenname weg, Keywords/Tags da.
- **Rückfallpfad (Variante B):** `processTemplate`-Original archivieren + Flag; additive Spalten sind nullable (kein Bestandseingriff); Rollback = Flag/Revert.
- **Bewusst NICHT in diesem Schritt:** Matching, Tool→Angebot-Adapter, `product_master_sets`-Ablösung, Versionierung/Freigabe, UI-Badges.

*(Voraussetzung: Yama bestätigt §13 — v. a. Metadaten-Ablage Q6, Scrub-Tiefe Q8, Preis-Regel Q1.)*

---

## Evaluator-Notiz
- **Belegt (firsthand, Teil A):** vollständige Bestandsaufnahme mit `datei:zeile` — Vorlagen-Weg existiert (`useTemplate`/`processTemplate`), `product_master_sets` = lebende Zweitwahrheit, Tool→`sections` + Matching fehlen, keine Keyword-/Produktart-Felder, kein Scrub/Version/Freigabe.
- **Belegt (firsthand):** `offer_templates`-Struktur (= `sections`) + vorhandene Picker/Save-Pfade; fehlende Metadaten/Keywords/Matching; Tool→Angebot fehlt (stand-alone).
- **Konzept, nicht Umsetzung:** Metadaten-Schema, Matching-Klassen, Scrub, Versionierung sind **Vorschläge**; jede Umsetzung = eigener freigegebener Posten mit Backend+Frontend+Testpfad + Rückfallpfad.
- **Nicht gemacht (korrekt):** kein Bau/Refactor/Import/Löschen/Überschreiben/Migration/Automatisierung. P1-a bleibt unkommittet geparkt (separater Vorgang).

---

*Nächster Schritt laut Auftrag: **STOPP.** Yama prüft dieses Konzept + die offenen Fragen (§13). Erst danach — auf Freigabe — Detail-Konzept/Umsetzung eines einzelnen Fahrplan-Schritts.*
