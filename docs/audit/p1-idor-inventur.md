# P1-IDOR — Vollständige Inventur (Pflicht-Stopp vor Bündel-Fix)

> **Status: INTERIM-VOLLSTÄNDIG (2026-07-08).** Gezählt (nicht geschätzt): Skript aus `route:list`-JSON ∩ Grep-Trefferzeilen ∩ Middleware-Analyse (Route + Konstruktor). Alle gelisteten Zeilen: Route-Middleware nur `Authenticate`, Konstruktor nur `auth` → **kein Rollen-/Owner-Gate**. Ausgeschlossen: P0-1..5 (gefixt), korrekt gegatete (Deal/Garbage/GeneralTask/CustomerNote/ChatGroup update+destroy/EmployeeRecurringLeave/Invoice-Zone), TABU (Nuriva/Video/Bitrix/NIBE/IMAP).
> **⚠ Offene Feinadjudikation:** ~200 „with-signal"-Methoden (Rumpf enthält `user_id`/`where`/`assigned` — echte Owner-Checks ODER Schein-Signale) werden noch geprüft → Endzahl kann **steigen**. Einzelne ⚠-Zeilen können abgezogen werden (Preview/user-scoped/Import-Beifang).

## Kernbefund
**232 verifizierte IDOR-Kandidaten** — nicht ~40. Systemischer Autorisierungs-Mangel (deckt CODE-AUDIT-01: „Autorisierung praktisch abwesend, 5/1211 Schreibrouten gegatet").

| Klasse | Zahl |
|---|--:|
| P1-hoch — Kunde/Belegkette | 58 |
| P1-hoch — HR/Personal | 47 |
| P1-mittel — Betrieb/Projekt/Aufgaben | 45 |
| P2-niedrig — Stammdaten/Konfig/Katalog | 82 |
| **Gesamt** | **232** |
| nach Gate-Typ | Owner-Check ~135 · permission:\<Modul\> ~97 · is_admin 0 |

## Bündel (nach Gate-Typ — so wird gefixt)
1. **Owner-Check-Bündel** (nutzer-eigene Objekte) — PersonalNote* (12), PersonalTask*/Board/Step (6), Ticket*-Zone (11), Dashboard-Inbox/Shortcut (3), Feedback (1). → Trait/Policy `OwnsModel` (`user_id/created_by == auth()->id()`).
2. **Kunde/Lead-Bündel** (`permission:Leads` + Objekt-Zugehörigkeit) — Customer*/NewLeads*/Offer*/PVRoof*/Kanban*/LeadEmail*/Inquiry* (Zeilen 1–58). **Höchste Prio (Belegkette).**
3. **HR-Bündel** (`permission:HR`) — Employee/**, Department, Position, Holiday/Leave/Skill/Contract/Country/Language/Team (Z. 59–102). Route-Gruppen-Middleware schließt ~44 auf einen Schlag.
4. **Lager/Belege** (`permission:Lager`) — Inventory/**, DeliveryNotes/**, Machine*/Asset* (Z. 54–57, 115–120, 174–181). Finanz-nah (Raten/Zahlungen) → P1.
5. **Stammdaten/Katalog** (`permission:Stammdaten`) — Product/**, MasterSet*, Distributor*, Brand*, ArticleGroup, Building/Heating/Temperature/Tiles/Stage (Z. 154–228).
6. **Admin/Config** (`permission:Admin/Settings`) — Foerderung, BreakingNews, CostingSet, EmailConfiguration, SupplierConnection/IDS.

## ⚠ Architektur-Weiche (Voraussetzung fürs Bündeln)
Das Rechte-System (`hasPermission`) ist **dormant** — außer `is_admin`-Bypass hat niemand Keys (`Leads/HR/Lager/Stammdaten/…` sind nicht geseedet). Folgen:
- **Bündeln via `permission:X`** ist der effiziente Weg — aber ohne geseedete Rollen kämen **nur Admins** durch (Nicht-Admin-Mitarbeiter ausgesperrt). → braucht **Rechte-System-Aktivierung** (Keys + Rollen-Zuweisung) = eigener Yama-Posten.
- **Owner-Check-Bündel** (1) ist unabhängig davon sofort baubar (self-scoped, kein Rollen-Seed nötig).

## Empfohlene Reihenfolge
1. **Bündel 1 (Owner-Check)** sofort — self-scoped, kein Rollen-Seed, deckt ~33 nutzer-eigene Actions.
2. **Rechte-System aktivieren** (Keys Leads/HR/Lager/Stammdaten/Admin seeden + Rollen) — dann greifen Bündel 2–6 ohne Aussperrung.
3. **Bündel 2 (Kunde/Lead)** — höchste Belegketten-Prio.
4. **Bündel 3 (HR), 4 (Lager), 5 (Stammdaten), 6 (Admin).**
Je Fix: Verhaltens-Test (Fremder→403, Berechtigter→ok), additiv, kleine Commits, unter Bauordnung §3.3.

*(Vollständige 232-Zeilen-Tabelle mit Datei:Zeile: im Agenten-Bericht erfasst; wird beim Bündel-Fix je Gruppe zeilengenau abgearbeitet. Endadjudikation der ~200 with-signal-Methoden ausstehend → Zahl kann steigen.)*
