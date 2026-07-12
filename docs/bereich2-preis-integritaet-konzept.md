# Bereich 2 — Preis-Integrität / Server-Pricing-Schutz — Konzept

**Stand:** 2026-07-12 · **read-only** · **kein Bau/Codeänderung/Archiv/Refactor/Migration/Import/Commit/View-/Controller-/Model-Änderung.**
**Zweck:** Konzept + kleinstes sicheres Paket + Testplan + Rückfallpfad für den P1-Posten „Browser-Einzelpreise nicht ungeprüft übernehmen; GK/Wagnis nicht still 0". **Keine Lösung gebaut.**
**Grundlage:** firsthand verifiziert (`datei:zeile`) in dieser Runde — Datenanker-Analyse der `sections`-Knoten — plus `bereich2-…-verifikation.md`.
**Rahmen:** Ziel-Wahrheit Preis = Server-Pricing (Yama bestätigt); Operanden-Gate; `rueckfall-archiv-regeln.md` (Preis-/Rechenpfad = Variante B).

> **Kern-Erkenntnis:** Server-Pricing ist **nur teilweise** sauber möglich. Eine stabile, server-nachprüfbare Katalog-Referenz existiert **heute nur für Set-Komponenten-Knoten** (`component_id` → `MasterSetComponent`). Produkt-Knoten tragen `product_id`, werden aber **nicht** neu bepreist; Lohn/Notiz/Freitext/Drag-Unterpositionen tragen **gar keine** stabile Referenz (zwei Bauwege verlieren sie sogar). Das kleinste sichere Paket muss diese Realität abbilden — nicht „alle Preise server-verifizieren", sondern „die verankerten absichern, den Rest **markieren**".

---

## 1. Hat ein `sections`-Positionsknoten eine stabile Katalog-Referenz? — **TEILWEISE**

Der komplette JS-State wird 1:1 serialisiert (`sections: State.sections`, `config.blade.php:7954`) → was der Knoten an Keys trägt, landet in der DB.

| Knoten-Typ (`item_type`) | Katalog-ID im Knoten | Server leitet Preis daraus ab? | Beleg |
|---|---|---|---|
| **Set-Komponente** `master_set_component` | `component_id` (+ `master_set_component_id`, `product_id`) | **JA** — einziger echter Reprice-Pfad | `OfferFolderController.php:970,1004,1123`; Bau `config.blade.php:6514-6575` |
| **Einzelartikel** `product` | `product_id`/`productId` | **NEIN** — `hydrateNodeFromOwnFields` bepreist Produkte nicht neu (nur Lieferantenname) | `OfferController` liest nur Preis-Keys; `OfferFolderController.php:1102-1140` |
| **Set-Kopf** `master_set` | `master_set_id` (+ `product_id`) | **NEIN** — Preis ist Summen-Snapshot | `config.blade.php:6458-6498,6663` |
| **Lohn** `labor` | keine (nur `qualification_id` row-level, nullable) | NEIN — frei/manuell | `config.blade.php:6620,12145` |
| **Drag-Unterposition** `sub_product` / `sub_master_set` | **keine — Defaults `null` (ID geht verloren)** | NEIN | `config.blade.php:9946,9976` (vs. `buildBaseItem:6231` Default `null`) |
| **Manuelle Position / Sub-Item / Notiz** `note` | keine | NEIN — frei/manuell | `config.blade.php:12104-12123,8231` |

**Die Preis-Helper des Speicherpfads lesen KEINE ID:** `offerNodeVkPrice/EkPrice/Qty` greifen nur auf `price/unit_price/vk/rate` bzw. `ek/cost/purchase_price` bzw. `qty` zu (`OfferController.php:1754-1785`); `offerLineTotals` rechnet ausschließlich aus diesen Snapshot-Preisen (`:1847-1940`). Die Angebotssumme wird also **immer aus eingefrorenen Knoten-Preisen** gebildet, nie aus dem Katalog re-derived.

**EK-Wahrheit im Katalog:** `master_set_components.id` (stabil, `Migration 2026_01_05_103845:11`) + `purchase_price` (EK) + `unit_price` (VK) (`MasterSetComponent.php:21,25`).

**Ehrliche Fallstricke:**
- **Reprice greift nur eine Ebene tief** (`section→items→subItems`, `OfferFolderController.php:967-976`) und **nur im Ordner-Pfad (Engine 2)** — der Haupt-Speicherpfad `processOffer` (Engine 1) reprovisioniert **gar nichts** aus dem Katalog.
- **`sub_product`/`sub_master_set` verlieren die Katalog-ID nachweislich** (Default `null`), obwohl Einzelprodukt + Set-Komponente sie mitschreiben → **Datenanker-Lücke**, die Server-Pricing für diese Knoten heute unmöglich macht.
- **ID-Aliase** (`component_id`/`master_set_component_id`, `productId`/`product_id`) laufen parallel; nur `component_id` wird genutzt.

**Fazit Punkt 1:** Server-Pricing ist **für Set-Komponenten sauber möglich**, **für Produkte grundsätzlich möglich** (ID vorhanden, Reprice-Logik fehlt), **für alle anderen Typen nicht** (keine Referenz). → Server-Pricing „für alle Positionen" ist **ohne vorherige Datenanker-Reparatur nicht sauber möglich**.

---

## 2. Positionstypen (belegt)

Zwei Typ-Achsen im Knoten: `item_type` (Herkunft) und `kind` (`article`/`labor`/`note`).

1. **Katalogposition — Einzelartikel** (`item_type=product`, `kind=article`) — trägt `product_id`.
2. **Set-Komponente** (`master_set_component`) — trägt `component_id` (**der einzige heute server-verifizierte Typ**).
3. **Set-Kopf** (`master_set`) — trägt `master_set_id`, Preis = Summen-Snapshot.
4. **Freitext-/manuelle Position** (`kind=note` / manuelle Position) — keine ID.
5. **Manuelle Sonderposition / Lohn** (`labor`) — keine Katalog-ID (nur `qualification_id`).
6. **Importierte/alte Position** — (a) Drag-Unterpositionen `sub_product`/`sub_master_set` (ID verloren); (b) Legacy `offer_product_lists` (separater, toter Pfad gegen alten Katalog `product_master_sets` — nicht Teil dieses Speicherpfads).

---

## 3. Je Positionstyp: Server-Pricing / Marker / Operanden-Gate / Speichern / Yama-Bestätigung

| Typ | Server-Pricing sicher? | Marker „manuell/ungesichert"? | Operanden-Gate? | Darf gespeichert werden? | Yama-Bestätigung? |
|---|---|---|---|---|---|
| **Set-Komponente** (`component_id`) | **JA** — EK aus `MasterSetComponent.purchase_price`, VK aus Regel | nein | nein (Anker vorhanden) | ja, server-verifiziert | Regel-Bestätigung (nicht je Position) |
| **Einzelartikel** (`product_id`) | **JA, sobald Reprice-Logik ergänzt** (ID ist da) | bis dahin: **ja** (ungesichert) | nein | ja | Bestätigen: soll `product_id` als zweiter Reprice-Anker gelten? |
| **Set-Kopf** (`master_set_id`) | indirekt (über seine Komponenten) | Kopf-Snapshot: **ja**, wenn Komponenten nicht einzeln verifiziert | nein | ja | ob Kopf-Preis = Σ verifizierter Komponenten sein muss |
| **Freitext/manuelle Position** | **NEIN** (kein Anker) | **JA — Pflicht-Marker** | ja (Preis = menschliche Entscheidung) | ja, **markiert** | Regel: markierte Positionen erlaubt? |
| **Lohn** (`labor`) | NEIN (kein Katalog-Anker; Regel = `CostingSet`, Punkt 4) | ja, bis CostingSet-Regel greift | ja | ja, markiert | Lohn-Regel-Quelle (später) |
| **Drag-Unterpos.** (`sub_*`, ID verloren) | **NEIN, bis ID-Verlust behoben** | **JA** | ja | ja, **markiert** | Vorab-Fix des ID-Verlusts als eigener Mini-Posten? |
| **Legacy** (`offer_product_lists`) | — (nicht dieser Pfad) | — | — | nicht produktiv weiterführen | separater Stilllegungs-Posten |

**Leitregel (Operanden-Gate):** Ein Knoten **ohne** server-nachprüfbaren Anker wird **gespeichert, aber als `preis_quelle=manuell/ungesichert` markiert** — **nie** stillschweigend als „geprüft" behandelt. Kein erfundener/übernommener Preis ohne Herkunft.

---

## 4. GK / Wagnis

- **Fehlende Datenfelder:** `master_sets.global_gemeinkosten` und `global_wagnis` **existieren in keiner Migration** (bestätigt), werden aber in `MasterSetController.php:226-229,455-458` gelesen → immer `?? 0` → **VK = `purchase_price × (1 + margin/100)`**, GK/Wagnis still weg.
- **Fachlich richtige Quelle:** `CostingSet` trägt bereits die passenden Sätze (`material_overhead_percent`, `risk_percent`, `profit_percent`, …, `CostingSet.php`) — das ist der **richtige Regel-Träger**, nicht neue Global-Felder am MasterSet.
- **Kleinster additiver Schritt:** **NICHT** stilles 0 zulassen. Die kleinste sichere Maßnahme ist der **Operanden-Gate-Marker**: wenn GK/Wagnis undefiniert ist, wird das **sichtbar markiert** („GK/Wagnis nicht gesetzt") statt mit 0 gerechnet. Ob die Werte dann aus `CostingSet` gezogen oder als additive nullable Felder gepflegt werden, ist die **nächstgrößere** Entscheidung.
- **Was NOCH nicht gebaut werden darf:** die Verdrahtung `CostingSet`-Materialfelder → MasterSet-VK (das ist der Kalkulations-Umbau aus Bewertung Punkt 3, größer als P1). Auch **keine** neue Migration in diesem Paket.

---

## 5. Rückfall (nur beschrieben — **nicht** ausgeführt)

Als **geplanter** Bau-Schritt (erst nach separater Bau-Freigabe):
- **Variante B (Preis-/Rechenpfad):** vor Änderung die betroffenen Methoden (`OfferController::processOffer`/`offerLineTotals`/`calculateOfferSections`) **archivieren** nach `_archiv/YYYY-MM-DD/p1-preis-integritaet/` + `MANIFEST.md` (Warum · Originalpfad · Ersatz · geprüfte Nutzung · Tests · Rückweg · Freigeber). **In dieser Runde NICHT anlegen.**
- **Rückfallpfad konkret:** (1) additiver **Guard hinter Flag** — bei Flag=aus verhält sich der Speicherpfad exakt wie heute; (2) alter Rechenweg bleibt als Fallback im Code erhalten; (3) path-scoped Commit → Rollback = Flag aus **oder** Commit-Revert, ohne Datenverlust (Guard schreibt additiv `preis_quelle`/`preis_geprueft`-Marker, ändert keine Bestandszeilen destruktiv).

---

## 6. Kleinstes sicheres Paket (Vorschlag)

**P1-a (Kern — kleinster Schnitt):** Server-seitiger Preis-Guard im **einen** Haupt-Speicherpfad `OfferController::processOffer` (save-document):
- Für Knoten mit **`component_id`**: EK server aus `MasterSetComponent.purchase_price` ableiten; weicht der Payload-EK ab → **Katalogwert gewinnt** (Payload ignoriert).
- Für Knoten **ohne** verifizierbaren Anker (labor/note/manuell/`sub_*`): Preis behalten, aber **`preis_quelle=manuell/ungesichert`** markieren.
- **Kein** Umbau von Engine 2, **keine** neue Migration, **keine** UI-Umschreibung.

**P1-b (Mini-Begleitschnitt, optional separat):** GK/Wagnis-**Operanden-Gate-Marker** — undefinierte GK/Wagnis werden markiert statt still 0.

**Bewusst NOCH NICHT gebaut:** Produkt-Reprice (`product_id`) · `sub_*`-ID-Verlust-Fix · CostingSet→VK-Verdrahtung · Engine-1/2-Zusammenführung · UI-Umstellung (JS→Server) · `offer_product_lists`-Stilllegung. Jedes ist ein eigener späterer Posten.

**Offene Grundsatz-Entscheidung (Sicherheits-Engineering):** **Enforcement-first** (P1-a wie oben: Katalogwert erzwingen) **oder Detection-first** (zunächst nur Abweichung **protokollieren/markieren**, Preis noch nicht überschreiben — null Risiko für legitime Angebote, aber Loch bleibt offen)? Empfehlung: **Enforcement für `component_id`-Knoten** (der zuverlässig verankerte Teil), **Detection/Marker für den Rest** — schließt das Loch dort, wo es sicher geht, ohne legitime Freitext-Angebote zu brechen.

---

## 7. Testplan (für den späteren Bau — hier nur geplant)

- **Unit — Guard:** manipulierter Payload-EK an einem `component_id`-Knoten → Guard liefert `MasterSetComponent.purchase_price`, nicht den Payload-Wert.
- **Feature — Enforcement:** `POST /offers/save-document` mit verfälschtem Komponenten-Preis → persistierte `total_net` basiert auf Katalog-EK.
- **Feature — Marker:** Freitext-/Lohn-/`sub_*`-Position → gespeichert **mit** `preis_quelle=manuell`; keine stille „geprüft"-Kennung.
- **Feature — GK/Wagnis:** MasterSet ohne GK/Wagnis → **Marker gesetzt**, nicht still 0 gerechnet.
- **Regression:** legitimes Angebot (Katalog-Preise unverändert) → Totals identisch zu vorher; **Flag=aus** → Verhalten exakt wie heute.
- **Sicherheits-Regression:** `component_id`-Knoten mit fehlender/gelöschter Komponente → definierter Fehler/Marker, kein Crash, kein stiller 0-Preis.

## 8. Wie prüfst du es im Browser

1. Angebot im Wizard bauen, **Set mit Komponenten** einfügen, speichern → gespeicherter Komponenten-EK = Katalog-EK.
2. **Absicherungs-Nachweis:** per DevTools einen manipulierten Komponenten-Preis mitsenden → im gespeicherten Angebot steht der **Katalog-Preis**, nicht der manipulierte.
3. **Freitext-Position** anlegen → sichtbare Markierung „manuell/ungesichert".
4. **GK/Wagnis** nicht gesetzt → sichtbare Markierung „nicht gesetzt" statt stiller 0.

## 9. Stop-Kriterium
Dieses Konzept-Dokument liegt vor → **STOPP.** Kein Code, kein Archiv, kein Commit. Yama prüft und gibt **separat** (a) das kleinste Paket + (b) die offenen Entscheidungen frei, **bevor** gebaut wird.

---

## Entscheidungstabelle

| Thema | Befund | Risiko | Kleinstes sicheres Paket | Braucht Yama-Entscheidung | Darf gebaut werden |
|---|---|---|---|---|---|
| **Datenanker Knoten** | Nur `component_id` server-verifizierbar; `product_id` vorhanden aber ungenutzt; Rest ohne ID | Server-Pricing „für alle" unmöglich ohne Anker-Reparatur | — (Grundlage) | Ja: `product_id` als 2. Anker aufnehmen? | **NEIN** (Konzept) |
| **Set-Komponente** | `component_id` → MasterSetComponent | manipulierbar, heute ungenutzt | **P1-a Enforcement** | Enforcement- vs Detection-first | **NEIN**, bis Freigabe |
| **Einzelartikel** | `product_id` da, kein Reprice | manipulierbar | später (Reprice-Logik) | ja | **NEIN** |
| **Freitext/Lohn/`sub_*`** | keine/verlorene ID | still als „geprüft" behandelbar | **Marker `manuell/ungesichert`** | Regel: markierte Positionen ok? | **NEIN** |
| **`sub_*`-ID-Verlust** | Defaults `null` überschreiben Katalog-ID | Anker geht verloren | Vorab-Mini-Fix (eigener Posten) | vorziehen oder später? | **NEIN** |
| **GK/Wagnis** | Spalten fehlen → still 0 | VK zu niedrig, ohne Warnung | **P1-b Operanden-Gate-Marker** | CostingSet als Quelle vs. neue Felder | **NEIN** |
| **Engine 1/2, UI, Legacy** | zwei Engines, JS-Preise, `offer_product_lists` | zweite Wahrheit | **außerhalb P1** (spätere Posten) | Reihenfolge | **NEIN** |

---

## Evaluator-Notiz
- **Belegt (firsthand, `datei:zeile`):** Knoten-ID-Realität je Typ (Hydration + JS-Bauwege + Preis-Helper); `component_id` als einziger Reprice-Anker; `sub_*`-ID-Verlust; fehlende GK/Wagnis-Spalten; CostingSet als Regel-Träger.
- **Ehrlich offen:** genaue Abdeckung des Produkt-Reprice (ID da, Logik fehlt — nicht zu Ende designt, bewusst außerhalb P1); ob `master_set`-Kopf-Preis gegen Σ Komponenten geprüft werden soll.
- **Keine Lösung gebaut:** P1-a/P1-b sind Vorschläge; Testplan/Rückfall sind geplant, **nicht** ausgeführt. Kein Archiv angelegt.
- **Nicht gemacht (korrekt):** kein Bau/Codeänderung/Archiv/Refactor/Migration/Import/Commit/View-/Controller-/Model-Änderung. `rueckfall-archiv-regeln.md` beachtet.

---

## Finalisierung nach Yama-Entscheidung (2026-07-12)

**Entscheidungen (gesperrt):**
1. **Enforcement-first für `component_id`.** Zeigt `component_id` eindeutig auf `MasterSetComponent`, darf Browser-EK/VK **nicht** führen → Server erzwingt Katalogwert. Alles ohne sicheren Anker: **nur markieren**, nicht erzwingen.
2. **`product_id` NICHT im ersten Paket.** Braucht eigene Preisregel (EK/VK/Rabatt/Lieferant/Marge) → erst inventarisieren/konzipieren.
3. **`sub_*`-ID-Verlust = eigener Mini-Fix danach**, nicht in P1-a mischen.
4. **GK/Wagnis:** `CostingSet` als Regel-Träger **konzeptionell bestätigt**; im ersten Bau **nur Operanden-Gate/Marker**, keine CostingSet-Integration.

**Gesperrter Scope des ersten Baus (nach Freigabe):**
- **P1-a (Sicherheits-Kern):** im Haupt-Speicherpfad `OfferController::processOffer` (save-document) für Knoten mit **`component_id`**: EK aus `MasterSetComponent.purchase_price` **und** VK aus `MasterSetComponent.unit_price` server-seitig setzen; abweichender Payload-Wert wird **verworfen** (Katalog gewinnt) und der Knoten additiv als `preis_quelle=katalog_verifiziert` (bzw. `katalog_korrigiert`) markiert. Knoten **ohne** verifizierbaren Anker → `preis_quelle=manuell/ungesichert` (behalten, nicht erzwingen). `component_id` gesetzt, aber Komponente fehlt/gelöscht → **Speichern läuft durch mit `preis_quelle='katalog_fehlt'` (kein harter Abbruch)**, nie stiller 0-Preis. *(Yama 2026-07-12: kein Abbruch, um Bestand/alte Daten nicht zu brechen; harte Ablehnung erst später nach Datenbereinigung.)*
- **KEIN UI in P1-a (Yama 2026-07-12):** keine View-/JS-Änderung. `preis_quelle` wird **nur im gespeicherten `sections`-JSON** abgelegt und per **Test/DB** geprüft — **nicht gerendert**. Die serverseitige Preis-Korrektur ist im bestehenden (unveränderten) Angebots-Detail als korrigierter Preis sichtbar; eine **sichtbare Marker-Kennzeichnung** ist ein **späteres eigenes Frontend-Paket**.
- **Additiv/rückfallbar:** nur JSON-Knoten-Marker + Guard hinter Flag; keine Bestandsänderung, keine Migration, keine UI-Änderung, Engine 2 unberührt.
- **Ausdrücklich NICHT im ersten Bau:** **P1-b (GK/Wagnis-Marker)**, Produkt-Reprice (`product_id`), `sub_*`-ID-Fix, CostingSet→VK-Verdrahtung, Engine-1/2-Zusammenführung, `offer_product_lists`-Stilllegung.

*Nächster Schritt: **STOPP.** Der Scope ist gesperrt; der Bau von P1-a (+ P1-b) startet **erst** auf Yamas ausdrückliches „Bau frei" — dann als getrennte Generator-Runde mit Variante-B-Archiv + Testplan, Evaluator-Prüfung vor Commit.*
