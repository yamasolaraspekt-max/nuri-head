# P1-16 (Auftrags-Anlage) + P1-18 (Offer-PDF) — Reparatur-Bericht

**Stand:** 2026-06-30 · Branch `private/app-code-backup` · Datei: `app/Http/Controllers/Customer/Deal/DealController.php`

## Was war kaputt
13 in `routes/web.php` gebundene DealController-Methoden fehlten → jeder Aufruf 500. Nach Prüfung der **echten Aufrufer** (Views/JS) aufgeteilt in *real genutzt* vs *verwaist*.

## Repariert (real auf dem Nutzer-Pfad) — 4 Commits, je `php -l` geprüft
| Commit | Methode(n) | Funktion |
|---|---|---|
| `0c8318b` | **dealStore** | Auftrag aus `lead_product_lists` ableiten (server-seitig, nicht aus Hidden-Feldern), `status='deal'`, Lead-Stufe→`deal`, `offer_id`/`offer_folder_id` verknüpfen, employee-Fallback. Gate `hasPermission('Customer','update')`. |
| `6c14c42` | **junk / unjunk / destroy / restore** | Junk-Markierung, SoftDelete, Restore. Gate `…('Customer','delete')`. |
| `8632a2b` | **getEmployees / updateReviewers / updateDate** | JSON-Endpunkte (Mitarbeiterliste, Prüfer setzen, Inline-Feld: sign_date/confirmed_at/status/price). |
| `a0a712e` | **profileDeleteDocument** | Dispatcher auf vorhandene Bild-/Anhang-Lösch-Methoden. |

**Rechte durchgehend nach repariertem Muster** (`User::hasPermission` → user_rolls über **users.id**, Flag=**1**, is_admin-Bypass). Kein Schema-Eingriff (alle Pflichtfelder vorhanden).

## Verifikation (real, HTTP + DB)
- **B (volle Rechte)** → „Neues Projekt erstellen" (`deal.store`): **HTTP 302**, Auftrag angelegt (`HE-AB26001`, `status='deal'`, `offer_id` automatisch verknüpft, employee gesetzt).
- **Lead-Stufe**: Test-Produkt `accepted` → **`deal`** nach Anlage (Kanban-Konsistenz). Auftrag erscheint in aktiver Liste (`status IN order/deal`).
- **C (keine Rechte)** → `deal.store` **403**, `deal_junk` **403**.
- Lebenszyklus (B): junk→`Junk`, unjunk→`deal`, destroy→SoftDelete, restore→zurück (alle 302). getEmployees→200 (50 MA).

## Bewusst NICHT angefasst (deine Entscheidung 4 + 5)
**Verwaiste Routen ohne UI-Aufrufer** — nicht „falsch repariert":
- Deal: `store` (deal.save), `info` (customer_deal_info), `price` (customer_deal_price), `jump` (customer_dealljump) — Legacy, kein View/JS ruft sie.
- Offer: `generatePdf` (`offers/generate-pdf`) — kein Aufrufer; der genutzte PDF-Weg ist `printMaterialSheet()` (Material-Druck). Ein echter Angebots-PDF-Export wäre **Feature-Arbeit** (Methode + Trigger-Button), bewusst später als eigene Entscheidung.

## Separater Befund (nicht Teil dieses Auftrags — zur Entscheidung)
Die Deal-Seite (`resources/views/admin/deal/customer_view.blade.php:2468/2474`) prüft Rechte noch mit dem **kaputten Alt-Muster** (`user_rolls.user_id = users.name`, `is_update = 'on'`). Real ist `user_id = users.id` und das Flag `tinyint 1` → `$canUpdateCustomer/$canDeleteCustomer` sind dort **faktisch immer false** (auch für Admin). Folge: die Dropdown-Links **Junk/Löschen sind im View ausgeblendet** (der Anlegen-Button nicht — er ist ungated, daher funktioniert der Haupt-Fluss). Mein Server-Gate ist korrekt; den View-Filter auf das reparierte Muster umzustellen wäre ein kleiner separater Fix (1 Datei) — auf deine Freigabe.

## Beobachtung (Daten, nicht Code)
Die per Seeder angelegten 14 Demo-Aufträge tragen andere `status`-Werte → sie erscheinen nicht in der nach `status IN (order,deal)` gefilterten Auftragsliste. Neu über den echten Fluss angelegte Aufträge schon. Bei Bedarf: Seeder-Status angleichen (eigener kleiner Schritt).
