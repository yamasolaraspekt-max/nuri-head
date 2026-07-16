# Planner-Spec — Auftragseingang & Auftragsbestätigung (Welle A2, ticket)

**Datum:** 2026-07-16 · **Rolle:** Planner (kein Produktionscode in diesem Durchgang) · **Heimat-App:** ticket.
**Yamas Konzept-Vorgabe:** „Auftrag ist Erteilung eines Projekts — in den Auftrag sollen Auftragsbestätigung, Eingang und Workflow rein; die Abwicklung läuft parallel."

---

## 0 · Ist-Stand (belegt, drei Quellen)

1. **Der Gewinn-Pfad existiert:** `DealController` erzeugt Aufträge per `Deal::create` (Transaktion, `status => 'deal'`, mit `offer_id`/`offer_folder_id`-Verknüpfung) — DealController.php:3686ff. `deals` trägt `order_number`, `offer_number`, `sign_date`, `price`, `deal_status`, `project_status`.
2. **Die AB existiert heute nur als Wort:** eine Dropdown-Option „Auftragsbestätigung" bei den Angebots-Dokumenttypen (folder-show.blade.php:6180) und eine Datei-Kategorie `confirmed_order` in der Kundenakte (customer_view.blade.php:2762). **Kein erzeugtes Dokument, keine Liste, kein Status.**
3. **Der Inhalt liegt bereit:** Das gewonnene Angebot trägt einen eingefrorenen Positions-Snapshot (`offer_details.angebot_snapshot_sections` + Summen `total_net/tax_rate/total_gross`) — genau die Quelle, aus der die FiBu-Doku die Rechnungspositionen ableitet (docs/accounting/phase-0 §3).

## 1 · Ziel & Entscheidung (festgelegt)

**Auftragseingang** = eine **Lese-Fläche auf `deals`**: alle Aufträge (`status = 'deal'`, ohne Spam/Papierkorb) im wählbaren Zeitraum (7/30/90 Tage), neueste zuerst, mit Summe (`price`), Kunde, Herkunfts-Angebot und **AB-Stand je Auftrag** (keine AB / erzeugt am / gedruckt). Kein Schreibzugriff auf die Kette.

**Auftragsbestätigung** = ein **erzeugtes Druckdokument je Auftrag** aus Deal-Kopf + Angebots-Snapshot, abgelegt **append-only** in einer neuen additiven Tabelle `order_confirmations` (`deal_id`, `ab_no`, Snapshot-JSON der gedruckten Positionen/Summen, `created_by`, `printed_at`). Muster: dunning-Tabellen aus Paket 2. **Die AB-Nummer ist `deals.order_number`** (eine Wahrheit — keine zweite Nummernvergabe; fehlt sie, wird das angezeigt, nicht erfunden — lückenloser Nummernkreis ist FiBu-Stufe iv und bleibt dort).

## 2 · Nahtstellen

**Neu:** `Customer/Deal/AuftragseingangController@index` (Fläche) · `@ab` (Druckansicht) · `@abErzeugen` (POST, ein Insert in `order_confirmations`) · View `admin/deal/auftragseingang.blade.php` + `admin/deal/ab_druck.blade.php` (Druck = Schwarz erlaubt) · Migration `create_order_confirmations_table` · Routen in einer bestehenden auth-Gruppe VOR etwaigen `{deal}`-Wildcards · Navi: „Auftragseingang · geplant" und „Auftragsbestätigungen · geplant" → live (beide zeigen auf die Fläche; AB-Liste = Reiter/Filter derselben Fläche, **eine** Fläche statt zwei halber).
**Bewusst NICHT angefasst:** der `Deal::create`-Gewinn-Pfad, `offer_details`-Snapshot, `invoices`, die Datei-Kategorie `confirmed_order` (bleibt für manuell hochgeladene Alt-ABs gültig und wird auf der Fläche mit angezeigt).
**Erweiterungspunkte (andocken, nicht bauen):** E-Mail-Versand der AB (später eigener Posten mit Vorlagen) · „Auftragsstatus"-Fläche (B-Stufe) liest dieselbe Liste · FiBu-Nummernkreis ersetzt später die order_number-Anzeige-Logik an EINER Stelle.

## 3 · Kantenliste

1. **Auftrag ohne Angebots-Snapshot** (manuell angelegt): AB nur mit Deal-Kopf (Kunde, Leistung, Preis) und sichtbarem Vermerk „ohne Positionsliste — kein Angebots-Snapshot", kein Fehler.
2. **Auftrag ohne `order_number`:** Fläche zeigt „– keine Auftragsnummer –", AB-Erzeugung trotzdem möglich (Nummernfeld leer sichtbar) — nichts erfinden.
3. **Mehrere ABs je Auftrag** (Korrektur): erlaubt, append-only; die Fläche zeigt die jüngste, die Historie bleibt.
4. **40+ Positionen:** Druck mehrseitig, Summenblock am Ende (Extremfall-Regel der UI-Bauordnung).
5. **Kunde ohne Adresse / gelöschter Kunde:** Platzhalter, kein Crash.
6. **Spam-/Papierkorb-Deals** (`deal.junk/delete`-Listen existieren): ausgeschlossen über dieselben Status-Filter wie die Auftragsliste.
7. **Snapshot-Summe ≠ deals.price** (nachträglich geänderter Preis): beide anzeigen, Differenz markieren — nicht stillschweigend eine Seite wählen (Operanden-Gate).

## 4 · Abnahmekriterien (für den Evaluator, je mit Gegen-Beweis)

1. **Zählprobe:** Fläche = exakt `SELECT count(*) FROM deals WHERE status='deal' AND created_at >= X` (Junk/Deleted raus) — Evaluator rechnet SQL selbst.
2. **Ketten-Unversehrtheit:** AB-Erzeugung ändert KEINE Zeile in `deals`/`offer_details`/`invoices` — Gegen-Beweis: Vorher/Nachher-Checksumme der drei Tabellen um einen Erzeugungs-Vorgang.
3. **Cent-Probe:** AB-Summen = Snapshot-Summen des Herkunfts-Angebots (ein handgerechneter Referenzfall).
4. **Kante 1:** Deal ohne Snapshot → AB erzeugbar, Vermerk sichtbar, kein 500er.
5. **Wächter:** Rechte-Gate (Customer) auf Route UND Navi · kein zweiter Ort, der AB-Nummern erzeugt · Styleguide-Konformität (Tokens, kein Schwarz außer Druck).

## 5 · Offene Punkte an Yama (vor dem Generator-Durchgang)

- **(a) AB-Pflichtinhalt:** Reichen Kopf (Firmen-/Kundendaten, Auftragsnummer, Datum), Positionsliste aus dem Snapshot, Summenblock, Zahlungs-/Lieferhinweis als Freitext? Oder brauchst du feste AGB-/Rechtstexte auf der AB (die AGB-Fläche ist C-Stufe)?
- **(b) Briefkopf:** wie beim Mahnbrief schlicht (app.name) — oder soll ich den Briefkopf des Angebots-PDFs (`config.blade.php`) wiederverwenden? Empfehlung: Angebots-Briefkopf, gleiche Handschrift für den Kunden.
