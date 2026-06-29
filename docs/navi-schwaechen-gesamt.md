# Navi-Schwächen — Gesamtübersicht & Paket-Schnitt

**Stand:** 2026-06-29 · **Methodik:** 8 parallele Bereichs-Prüfer, Navi-Punkt für Navi-Punkt nach
`docs/navi-schwaechen-plan.md` (8-Punkte-Raster, code-statisch belegt mit Datei:Zeile, dedupliziert
gegen das bisherige Audit + git log). Rohdaten: `docs/navi-schwaechen-gesamt.csv`.

> Rein analytisch — es wurde nichts am Code geändert.

## Gesamtbild

- **~129 Navi-Punkte** über 8 Sektionen geprüft.
- **~126 offene Funde:** 🔴 ~20 · 🟠 ~37 · 🟡 ~54 · ⚪ ~15.
- **9 Funde als bereits gefixt verifiziert** (Paket 0/1 wirkt — siehe unten).
- **~15 echte Neufunde**, die im bisherigen Audit fehlten — davon **3 kritische anonyme Datenlecks**.

## Heatmap je Sektion (offene Funde)

| Sektion | 🔴 | 🟠 | 🟡 | ⚪ | geprüft |
|---|--:|--:|--:|--:|--:|
| CRM › Anfragen + Leads/Kunden | 6 | 4 | 6 | – | 14 |
| CRM › Kommunikation + Partner | 3 | 3 | 11 | 1 | 14 |
| Vertrieb › Angebote + Aufträge | 2 | 5 | 9 | 4 | 12 |
| Projekte + Support › Tickets | 3 | 5 | 6 | – | 14 |
| Personal (Mitarb./Org./HR) | 2 | 5 | 7 | – | 19 |
| Artikel & Lager | 3 | 5 | 1 | 3 | 21 |
| Finanzen + Admin + System | 1 | 5 | 8 | 3 | 12 |
| Arbeitsbereich + Berichte | 0 | 5 | 6 | 4 | 5 |

## 🆕 Die wichtigsten Neufunde (nicht im alten Audit)

1. **🔴 Website-Leads anonym auslesbar** — `FusionFormSubmissionController` hat keine auth → die ganze
   Fusion-Routengruppe liefert alle Website-Lead-Daten (Name/Mail/Tel/Adresse) als JSON ohne Login.
   `routes/web.php:655-665`.
2. **🔴 IMAP-Passwort Klartext (zweites Modell)** — `EmailConfiguration` speichert das Passwort
   unverschlüsselt und rendert es ins DOM. Der bekannte Befund #16 deckte nur `LeadEmailAccounts`.
3. **🟠 Angebots-Vorlagen inkl. Kalkulation anonym** — `OfferTemplateController@wizardShow` liegt im
   non-auth-Block → Netto-Preise/Kalkulation ohne Login abrufbar.
4. **🔴 Angebots-Kommentare komplett tot** — `OfferCommentController` ist nie als Route registriert;
   die UI ruft 404-Endpunkte. Feature funktioniert gar nicht.
5. **🟠 Rechnungen ohne Rollenschutz** — `InvoiceMiddleware` ist im Kernel registriert, aber nirgends
   angewandt → jeder eingeloggte User sieht/ändert Rechnungen.
6. Weitere: `OfferController@show`/`@generatePdf` fehlen (500), hardcodierter Bitrix-Webhook-Token,
   `is_active`-Toggle im UserController ist ein No-Op, `user-rolls.index` ohne Permission,
   `system-warning.current` anonym, mehrere doppelte/falsch benannte Routen.

## ✅ Durch Paket 0/1 bereits gefixt (verifiziert)

`DailyReportController::isAdmin` (users.id) · `customer/appointments/{id}/reports` auth ·
`AllContactController` auth · RequestOut/PurchaseRequest/GoodsReceipt auth · Favoriten (employeeId) ·
Mein Profil ($employee) · GarbageController · Feedback · Eingeschränkte Benutzer + Wissensdatenbank-
Kategorien (hasPermission) · BEG-Förderungen (durch sauberes Förderungs-Modul ersetzt).

## 📦 Paket-Schnitt (Phase 3 — Reihenfolge nach „Schaden wenn ungefixt")

| Paket | Thema | Funde | Warum zuerst/später |
|---|---|--:|---|
| **P0** | **Sicherheit — anonym & Rechte** | 15 | Anonyme Datenlecks (Website-Leads, IMAP-Passwörter, Angebots-Kalkulation), Rechte-Lücken (make_admin für jeden, Rechnungen, IDOR Aufgaben), Secrets, Dev-Routen. **Höchste Priorität.** |
| **P1** | **Crashes / tote Pfade (500/404)** | 13 | Kernfunktionen tot: Auftrag-Anlegen, Angebots-Kommentare, Anfrage-Kanban, Übergaben, Rabattgruppen, Urlaubsanspruch, Qualifikation löschen. |
| **P2** | **Datenverlust / Workflow** | 4 | Anfrage-Status-Reset, Inventarschwund (Lagerausgabe-Storno), Urlaubstage werden nie verbraucht. |
| **P3** | **Stored XSS** | 2 | Termin-Reports & Ticket-Profile rendern ungefiltertes HTML. |
| **P4** | **CRUD / Routen / CSRF** | 8 | GET-Destroy-Querschnitt (>30 Routen), Restore-Crash, Anlegen-postet-update, leere Edit-Modals, tote Buttons. |
| **P5** | **Konsistenz / UX** | ~7 | DE/EN-Mix, Label-Brüche, ID-Mapping-Inkonsistenzen, fehlende Badge-Producer, Routen-Duplikate. |
| **P6** | **Architektur / Performance** | 2 (Sammel) | Fat Controller, N+1 — bereits in `stabilitaet-fixliste.md` erfasst; bewusst zuletzt. |

**Empfehlung:** P0 → P1 → P2 → P3 → P4 → P5 → P6. P2/P3 könnten je nach Risikoappetit vor P1 gezogen
werden (Datenverlust/XSS schlimmer als ein 500), aber P0 bleibt unstrittig zuerst.

## So arbeiten wir Phase 4 ab
Pro Paket je ein Ausführer-Prompt; **1 Fix = 1 Commit**, jeder einzeln verifiziert (jetzt real mit den
Seeder-Profilen A/B/C testbar), Bericht → Planer-Abnahme. Reihenfolge innerhalb P0: zuerst die **anonym
erreichbaren** Lecks (01–05), dann die **Rechte-Lücken** (06–13), dann **Secrets/Dev-Routen** (14–15).
