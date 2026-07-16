# Welle A1 „Geld einsammeln" — Planner-Inventur + Konzept + Pakete

**Datum:** 2026-07-16 · Rolle: Planner (Startblock erfüllt) · Alle Aussagen mit Beleg (R1/R4).

---

## 1 · Inventur — was schon da ist (belegt)

**Die FiBu-Engine liegt komplett im Haus, hat aber NULL Aufrufer:**

| Baustein | Beleg | Kann |
|---|---|---|
| `BelegflussService` | app/Services/Accounting, 15 kB | festgeschriebene Rechnung → Buchungssatz (Kopf + mit Positionen), Status `entwurf` |
| `BuchungsEngine` | ebenda | `festschreiben()`, `storno()` (append-only, GoBD-Muster) |
| `DatevExtfExportService` | ebenda | `exportBuchungsstapel()`, `pruefeKonformitaet()` |
| `EingangsBelegflussService` | ebenda | `bucheEingangsrechnung(array)` |
| `AuswertungsService` | ebenda | `summenSalden()`, `ustVoranmeldung()`, `bwa()` |
| FiBu-Tabellen | 3 Migrationen 2026-07-05 (foundation/documents/journal) | Schema Stufe (i) |
| **Aufrufer** | `grep -rln "Services\\Accounting" app routes` → **0 Treffer** | Engine ohne Zündschlüssel — genau die A1-Lücke |

**Die invoices-Schiene trägt schon alles für Offene Posten** (Invoice.php): `STATUS = draft/sent/paid/overdue/cancelled` (:18), `ISSUED_STATUSES` (:21), zentrale `due_date`-Ableitung (Zahlungsziel, :24ff), `paid_amount/paid_at` (Migration 2026-05-08), **`open_amount`-Attribut** (:151). UI existiert: `Invoice/InvoiceController` + Canvas, Routen `admin.invoices.*` hinter `InvoiceMiddleware` (web.php:4518ff).

**hausverwaltung liefert die Vorlagen:** `Accounting/OffenePostenController` (index/sync/assign), `Finance/DunningController` (index/execute/print), `dunning_*`-Tabellen + `DunningRun/DunningItem`-Models.

**Bindend bleibt der Phase-0-Befund** (docs/accounting/phase-0-fibu-transplant-befund.md): FiBu dockt nur an festgeschriebene Rechnungen an · Stufenplan (i)–(viii) mit Gates · **SKR03/04 = Steuerberater-Entscheid** · DATEV erst nach Konformitäts-Prüfer + Testpaket grün · alles gegen `ticket_testing` vor Live.

## 2 · Konzept-Folge: OP braucht KEINE FiBu

Offene Posten = `total_amount − paid_amount` je nicht-stornierter, nicht-Entwurfs-Rechnung — das steht komplett in `invoices`. Deshalb ist Paket 1 eine **reine Lese-Fläche ohne Schema-Änderung und ohne Buchungs-Abhängigkeit**: sofort live-fähig, verletzt keine Dauerdirektive (liest nur), und die FiBu-Stufen (iii)–(vi) können später unabhängig andocken.

## 3 · Paketschnitt A1 (jedes einzeln abnehmbar)

| Paket | Inhalt | Abhängigkeit | Status |
|---|---|---|---|
| **1 · Offene Posten** | Lese-Fläche: Fälligkeitsraster (nicht fällig / 1–30 / 31–60 / 61–90 / >90 / ohne Zahlungsziel), Summen, Kundenlink, überfälligste zuerst | keine | **GEBAUT 2026-07-16** — Controller `Invoice/OffenePostenController`, View `admin/invoices/offene_posten`, Route `admin.invoices.offene-posten` (vor der {invoice}-Wildcard), Navi live |
| **2 · Mahnwesen** | Mahnläufe + Mahnstufen nach hausverwaltung-Muster (`dunning_runs/items` additiv), Briefe aus Textvorlagen | Paket 1 · **Operanden von Yama:** Fristen je Stufe, Gebühren, Zinsen (Fach-/Rechtsentscheid → Vorschlag + Bestätigung, nie automatisch) | offen |
| **3 · DATEV-Export-Fläche** | UI auf `DatevExtfExportService` + `pruefeKonformitaet()` sichtbar; Testexport gegen `ticket_testing` | Buchungen müssen existieren → Stufen (ii)/(iii): Kontenrahmen-Seed (**SKR-Entscheid Steuerberater!**) + BelegflussService-Anbindung an Festschreibung | offen |
| **4 · Eingangsrechnungen** | Erfassungs-UI auf `EingangsBelegflussService` (Beleg-Erkennung als Ausbaustufe dahinter) | Stufe (ii) Konten | offen |
| **5 · E-Rechnung Empfang** | XRechnung/ZUGFeRD einlesen → Paket-4-Erfassung vorbefüllen | Paket 4 | offen |

## 4 · Offene Operanden (werden erfragt, nicht erfunden)

1. **SKR03 oder SKR04** → Steuerberater (blockiert Paket 3/4-Buchung, NICHT Paket 1/2).
2. **Mahn-Regeln** (Stufenanzahl, Fristen, Gebühren, Verzugszinsen) → Yama/Steuerberater.
3. **Zahlungserfassung heute:** `paid_amount` wird manuell gepflegt — reicht das für den OP-Start (Bank-Anbindung ist B-Stufe)? → Yama.

## 5 · Ziel-Beweis Paket 1

PHP-Lint grün (Controller, View, Sidebar, routes/web.php) · Route VOR der `{invoice}`-Wildcard eingefügt (sonst würde „offene-posten" als ID gefangen) · Rechte: Route hinter `InvoiceMiddleware`, Navi-Sektion Buchhaltung gated `Finance` · UI nach Styleguide (Tokens, Pills hell+Tinte, kein Schwarz/Fremdblau) · Leerzustand + Extremfall (langer Kundenname, ellipsis + title) bedacht · Entwürfe/Storni ausgeschlossen, Rundungsgrenze 0,009 €.
