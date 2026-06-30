# Glossar — verbindliche Begriffe des ticket-Kernprozesses

**Status: VERBINDLICH (ratifiziert von Yama + Planer).** Stand: 2026-06-30 · Branch `private/app-code-backup`.
Dies ist das **Fundament-Dokument** für die Begriffe. Spätere Änderungen (Code, Doku, Gespräche) richten sich danach aus.
Grundlage: `docs/begriffs-konsolidierung-vorschlag.md`. Dies ist **kein** Code-Bau, **kein** Rename, **kein** Schema-Eingriff — es hält die entschiedenen Begriffe fest.

> **Lesart:** Abschnitt 1 + 2 sind **entschieden** (verbindlich). Abschnitt 3 listet die noch **offenen** Begriffe samt der Weiche, an der sie hängen. Abschnitt 4 (Aufräum-Reihenfolge) und Abschnitt 5 (Customer-Model-Falle) sind **To-dos/Befunde** — **nicht jetzt auszuführen**.

---

## 1. Ratifizierte Begriffe (verbindlich)

Diese Zuordnungen gelten ab sofort als **die** Sprache des Kernprozesses.

| Begriff (verbindlich) | Bedeutung | Führende Tabelle / Feld (Single Source) | Klarstellung |
|---|---|---|---|
| **Kunde** | die Person/Firma mit Profil | **`new_leads`** | Die Tabelle `customers` ist **NICHT** der Kunde (sie ist leer/tot — s. Abschnitt 4 + 5). „Kunde" = `new_leads`, trotz des irreführenden Tabellennamens. |
| **Objekt** | die Immobilie/Adresse (Gebäude/Bauobjekt) | **`lead_alternative_adds`** · Objekt-ID = **`alternative_id`** | Das Wort **„alternative" bedeutet im Kernprozess „Objekt"**. Wo immer `alternative_id` steht, ist die **Objekt-ID** gemeint. |
| **Gewerk** | ein Produkt-Vorgang an einem Objekt („PV Müller") | **`lead_product_lists`** — eine Zeile = **Kunde × Produkt × Objekt** | Ein Gewerk ist **kein eigenes Objekt**: „PV Müller" ist eine `lead_product_lists`-Zeile am Objekt des Kunden Müller. Mehrere Gewerke können am selben Objekt hängen. |
| **Angebot** | das Angebotsdokument | **`offers`** (+ `offer_folders`, `offer_details`) | einheitlich. |
| **Auftrag** | der beauftragte Vorgang | **`deals`** | „Deal" im Code = **Auftrag**. |

**Merksätze (verbindlich):**
1. **Kunde = `new_leads`** (nicht `customers`).
2. **Objekt = `lead_alternative_adds`; `alternative_id` = Objekt-ID** („alternative" = „Objekt").
3. **Gewerk = `lead_product_lists`** (Kunde × Produkt × Objekt); „PV Müller" ist ein Gewerk, kein Objekt.
4. **Angebot = `offers`, Auftrag = `deals`.**

---

## 2. Ratifizierte Richtungsentscheidung: KEIN physischer Rename

**Entschieden: Die Tabellen-/Spaltennamen werden NICHT physisch umbenannt.** Begriffsklarheit wird erreicht über
- **(a) dieses Glossar** (die verbindliche Sprache), und
- **(b) sprechende Model-Aliasse / Accessoren auf Code-Ebene** (z. B. ein Accessor/Alias, der `alternative_id` als „Objekt" lesbar macht), **ohne** die physischen Spalten/Tabellen anzufassen.

**Begründung (verbindlich festgehalten):** Ein physischer Rename hätte an einem **produktiven System** einen unverantwortlichen Sprengradius — die Begriffs-Bestandsaufnahme hat ihn beziffert:

| Rename-Kandidat | Sprengradius |
|---|---|
| `new_leads` → Kunde | 62 Migrationen (mit FKs) · 70 Controller / ~1074 Zeilen · 51 Views |
| `lead_alternative_adds` → Objekt | 53 Migrationen · 50 Controller / ~283 Zeilen |
| **`alternative_id` → object_id** | **83 Controller / ~1054 Zeilen · 119 Views / ~849 Zeilen · 53 Models · 62 Migrationen (FKs)** |

Dieselbe Klarheit ist **risikofrei** über die Alias-/Glossar-Schicht erreichbar. Deshalb: **physischer Rename ausgeschlossen**; Alias-/Glossar-Schicht ist der Weg.

---

## 3. Offene Begriffe — noch nicht entschieden (gehören zu einer Weiche)

Diese Begriffe sind **bewusst NICHT** im verbindlichen Teil — sie hängen an einer Architektur-/Geschäftsregel-Entscheidung (`docs/architektur-entscheidungen.md`).

| Begriff | Ist-Zustand | Entscheidung gehört zu | Wer entscheidet |
|---|---|---|---|
| **Rechnung** | zwei Tabellen: `invoices` (11 Z., gefüllt) **vs.** `deal_invoices` (0 Z., schlafend) | **Architektur-Frage 3** | **Yama + Steuerberater** (buchhalterische Hoheit) — **offen** |
| **Projekt** | **zwei Bedeutungen** (s. u.) | **Architektur-Frage 5** | Yama (Geschäftsregel) — **offen** |
| **Status / Phase** | **~11 Status-/Stufen-Felder** in `lead_product_lists` + 5 in `deals`, vermischt | **Architektur-Frage 1** | Yama (konzeptionell) — **offen** |
| **Duplikat** | adress-/kontaktbasiert (`checkCustomer`: street+PLZ + Tel/E-Mail), gegen Kunde **und** Objekt | Geschäftsregel | Yama — **offen** |

### 3.1 „Projekt" — die zwei Bedeutungen (Wahl offen)
- **(i) Gewerk-am-Objekt** — die Kombination Objekt × Produkt (in `lead_product_lists` + `offers` + `deals` + `invoices`). Das, was die Geschäftsdaten trägt.
- **(ii) Bauphasen-Projekt** — die **`projects`**-Tabelle (31 Z.): Projektleiter, Montagestart, Fortschritt. Separat im Planer, **ohne** `deal_id`.

→ Welche Bedeutung „Projekt" verbindlich tragen soll (bzw. ob (ii) zur Auftrags-Phase oder eigenem Vorgang wird), ist **Architektur-Frage 5 — offen**.

### 3.2 „Status/Phase" — Ziel vs. Ist
- **Ziel (aus `workflow-sollkonzept.md`):** drei **getrennte** Dimensionen — **Phase** (wo im Prozess), **Zustand** (wie: aktiv/pausiert/Wiedervorlage/gewonnen/verloren …), **Historie** (was passiert ist). Je Dimension **eine** Quelle.
- **Ist:** ~11 vermischte Stufen-/Status-Felder (Lead) + 5 (Auftrag); `project_status` an mehreren Entitäten mit je anderer Bedeutung.
- **Umsetzung = Architektur-Frage 1 (verbindliche Statusquelle) — offen.**

### 3.3 „Duplikat" — offene Unterscheidung
Heute: gleiche Adresse + Kontakt = Treffer (gegen Kunde und Objekt). **Offen:** Unterscheidung **echtes Duplikat** (versehentlich doppelt) vs. **legitimer 2. Eintrag** (zweites Objekt/Haus). Geschäftsregeln in `docs/erfassung-duplikat-befund.md`.

---

## 4. Aufräum-Reihenfolge der toten Tabellen (To-dos — NICHT jetzt ausführen)

| Tabelle | Zeilen | Entscheidung | Reihenfolge |
|---|--:|---|---|
| **`deal_invoices`** | 0 | **BEHALTEN** — schlafendes, beabsichtigtes Feature (auftragsgebundene Abschlag-/Schlussrechnung). Schicksal hängt an **Architektur-Frage 3**. | nicht anfassen |
| **`customers`** | 0 | **erst Referenzen bereinigen, DANN entfernbar.** Daran hängen: `Customer`-Model + ~10 aktive Controller + ein **Schreibzugriff** (`NewLeadsController:5520`). Das ist die **„Customer-Model-Falle"** (Abschnitt 5). | (1) Referenzen umbiegen/entfernen → (2) Tabelle weg |
| **`customer_alternative_adds`** | 0 | **erst Referenzen bereinigen, dann entfernbar** (`CustomerAlternativeAdd`-Model + Refs in NewLeads/LeadOverview/ArticleGroup + Old). | (1) Referenzen → (2) Tabelle weg |
| **`leads`** | 0 | **voraussichtlich entfernbar**, nachdem bestätigt ist, dass `Lead`-Model + der eine `Old/CustomerController:423`-Zugriff ungenutzt sind (es ist eine **E-Mail-Tabelle**, kein Kunde — Namenskollision auflösen). | bestätigen → entfernen |

> Keine dieser Aktionen wird jetzt ausgeführt — sie sind als künftige, kontrollierte Aufräum-Schritte festgehalten.

---

## 5. Markierter Befund: die „Customer-Model-Falle" (zu untersuchen)

**Befund (kein Aufgaben-Auftrag jetzt, sondern markiert):** Das **`Customer`-Model zeigt (Laravel-Default) auf die leere `customers`-Tabelle** (0 Zeilen) — während der echte Kunde `NewLeads` → `new_leads` (52 Zeilen) ist.

**Folge:** **~10 aktive Controller** (CustomerHeatingCircuit, PVTools, Tools, ChecklistRoom, CustomerPhaseList, PurchaseRequest, Email/Leads …) und mindestens **ein Schreibzugriff** (`NewLeadsController:5520`, `update(['inquiry_screenshot' => …])`) arbeiten dadurch faktisch **auf 0 Datensätzen** — Lese-Queries liefern leer, Schreib-Queries laufen folgenlos ins Leere.

**Warum das ein echter Bug-Herd ist:** Das könnte erklären, **warum manche Funktionen folgenlos ins Leere laufen** (z. B. ein gespeicherter Wert, der nie auftaucht). 

→ **Eigener Befund: „Customer-Model-Falle untersuchen"** — separat zu prüfen, ob/welche Funktionen dadurch still kaputt sind, BEVOR `customers` bereinigt/entfernt wird. **Nicht Teil dieses Glossars; nur markiert.**

---

## Verweise
`begriffs-bestandsaufnahme.md` (Ist-Zustand) · `begriffs-konsolidierung-vorschlag.md` (Vorschlag/Belege) · `architektur-entscheidungen.md` (die offenen Weichen 1/3/5) · `erfassung-duplikat-befund.md` (Duplikat-Geschäftsregeln) · `hierarchie-objekt-projekt-bestandsaufnahme.md` (Kunde→Objekt→Gewerk) · `workflow-sollkonzept.md` (Phase/Zustand/Historie als Ziel).

---

*Verbindliches Fundament-Dokument. Reines Glossar — kein Code, kein Rename, kein Schema-Eingriff. Die ratifizierten Begriffe (Abschnitt 1) und die Rename-Richtung (Abschnitt 2) sind entschieden; die offenen Punkte (Abschnitt 3) bleiben den genannten Weichen vorbehalten.*
