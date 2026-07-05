# Ticket NAV-01 — Navigations-Konzept (schlanke Zielnavigation ableiten)

**Stand:** 2026-07-03 · **Planungs-/Konzept-Ticket — KEIN Umbau, kein Code, keine Route/Menü-Änderung, kein Commit.** Dieses Dokument ist der **Auftrag** für die Navi-Konzeptarbeit; es baut noch **keinen** finalen Zielbaum.
**Führend:** `ticket`. **playground:** nur fachliche Struktur-Referenz (Modulkatalog), **nicht** Design-/Umsetzungsvorlage.
**Typ:** Informationsarchitektur / Navi-Planung · **Priorität:** nach Sprint-1-Rechnungskette einplanbar (unabhängig, kein Blocker für S1-03).

---

## 1. Ziel
Aus **zwei Beständen** — System A (ticket, reale Module + gewachsene Navigation + Zusatzzugänge) und Playground (fachliche Themenraum-Struktur als Katalog) — eine **schlanke, geplante Zielnavigation** ableiten: klare Themenräume, **max. 4–5 direkte Unterpunkte je Hauptbereich**, Details in Bereichsseiten/Tabs/Filter, Quick-Menüs nur für echte Schnellaktionen, sensible Bereiche rechtegesichert.

## 2. Scope / Nicht-Scope
**Enthalten (dieses Ticket erzeugt):** ratifizierte IA-Entscheidungen · verbindliche Begriffe (via Glossar) · **einen** Zielnavigations-Entwurf (Themenräume + max. 4–5 Unterpunkte) · Zuordnung „welches heutige Menü/Modul → welcher Zielraum" · Rechte-Zielmodell · Migrations-/Legacy-Plan (was verschieben/zusammenführen/entfernen, in welcher Reihenfolge).
**NICHT enthalten:** Umbau der Sidebar/Routen/Blades · Code · neue Bereichsseiten bauen · Menüpunkte verschieben · Umbenennungen im Code. Das folgt erst nach Freigabe des Konzepts in **separaten** Umsetzungs-Tickets.

## 3. Eingangs-Grundlagen (verbindlich zu berücksichtigen)
- `docs/navigation-ist-befund-inventur.md` — System-A-Sidebar/Module (15/37/93).
- `docs/navigation-vergleich-ticket-vs-playground.md` — voller A↔B-Vergleich (26 Punkte) + Reifegrad-Anhang (Playground aktiv gerendert, aber Prototyp; Buchhaltung hart gesperrt).
- `docs/navigation-vergleich-bewertung-ticket-vs-playground.md` — verdichtete Bewertung + Konsequenzen.
- `docs/software-audit/ia-entscheidungen.md` — 13 IA-Entscheidungen (teils offen: IA-1/2/10).
- `docs/glossar.md` — **ratifiziertes** Begriffsglossar (verbindliche Grundlage für Benennung).
- `docs/navi-schwaechen-gesamt.md` — dokumentierte Navi-Schwächen/Sicherheitsfunde.
- Sprint-1-Kontext (`docs/uebernahme/index-sprint-1-rechnungsprozess.md`): S1-01/02 umgesetzt; Rechnungen fachlich aufgewertet → gehören primär in Finanzen; `deal_invoices` = Legacy (S1-10); S1-09 = UI-Konsolidierung.

## 4. Leitplanken (verbindlich für den Entwurf)
1. **Themenräume statt gewachsener Sammlung** — Sidebar zeigt nur Hauptbereiche.
2. **Max. 4–5 direkte Unterpunkte je Hauptbereich.** Alles darüber → Bereichsseite/Tabs/Filter/Aktionen.
3. **Rechnungen gehören primär nach Finanzen.** Vertrieb öffnet Rechnungen nur **kontextbezogen** aus Auftrag/Angebot/Projekt.
4. **Buchhaltung/DATEV/Kanzlei darf nicht fertiger wirken als umgesetzt** — Prototyp/gesperrte Bereiche klar kennzeichnen; nur real Nutzbares sichtbar.
5. **Quick-Menüs nur für echte Schnellaktionen**, nicht als zweite versteckte Hauptnavigation; **alle Schnellzugriffe rechteprüfen** (heute teils ungated).
6. **Sensible Bereiche** (Finanzen/Rechnungen/Zahlungen/Lohn/Admin/Export) brauchen **klare Rechteprüfung** — Zielmodell klären (Routen-Ebene wie Playground vs. Sidebar-Filter).
7. **Begriffe verbindlich nach `glossar.md`** — Doppelbegriffe auflösen (`deals`/„Auftrag", `new_leads`/„Kunde", „Lead"-Mehrdeutigkeit, Rechnungen/`deal_invoices`).
8. **Eine Navi-Quelle** anstreben (Playground-Muster: eine Definition → Desktop/Rail/Mobile) statt getrennter Sidebar/Quick/Dashboard.

## 5. Vor dem Zielbaum zu klärende Entscheidungen (Yama / ggf. Steuerberater)
| # | Entscheidung | Entscheider |
|---|---|---|
| D1 | IA-Entscheidungen ratifizieren/aktualisieren — insb. **IA-2 (Rechnungen → Finanzen)** und **IA-10 (Canvas-Doppel-Eintrag entfernen)** | Yama |
| D2 | Führende Navigationsebene: **Sidebar** vs. Bereichsseiten vs. globale Suche (bzw. Kombination) | Yama |
| D3 | Rechte-Zielmodell: **Routen-Ebene** (erzwungen, Playground) vs. Sidebar-Filter (heute A) — und Absicherung der Quick-Menüs/Dashboard-Shortcuts | Yama |
| D4 | Welche Playground-Themenräume übernehmen (Kundendienst&Einsatz, Controlling, Energie&Auslegung, Lager+Inventur, Buchhaltung-Struktur) — und in welcher Reihenfolge | Yama |
| D5 | Umgang mit „noch nicht fertigen" Modulen: sichtbar mit Kennzeichnung, oder ausblenden bis fertig | Yama |
| D6 | Ist der Playground-Stand **eingefroren** (Vergleichsquelle) oder aktiv weiterentwickelt (Zeitpunkt fixieren) | Yama |
| D7 | Verbindliche Begriffe final aus `glossar.md` übernehmen (ja/Abweichungen) | Yama |

## 6. Methode / Phasen (der Konzeptarbeit — noch kein Bau)
- **P1 — Entscheidungen ratifizieren:** D1–D7 klären; IA-Entscheidungen und Glossar als verbindlich setzen.
- **P2 — Zielnavigation entwerfen:** Themenräume festlegen (max. 4–5 Unterpunkte), je heutigem Menü/Modul die Zielzuordnung dokumentieren; Detailfunktionen auf Bereichsseiten/Tabs verschieben (nur konzeptuell).
- **P3 — Review-Runde:** gegen Leitplanken prüfen (4–5-Regel, Rechnungen→Finanzen, DATEV-Ehrlichkeit, Rechte), Nutzer-Perspektive (Finden statt Suchen), Sackgassen/Platzhalter raus.
- **P4 — Migrations-/Legacy-Plan:** was verschieben/zusammenführen/entfernen; Reihenfolge; Verzahnung mit S1-09 (Rechnungs-UI) und S1-10 (`deal_invoices`/`Old/`-Cleanup); Rückwärtskompatibilität der Links.
> Erst nach Freigabe des Konzepts folgen **separate Umsetzungs-Tickets** (Sidebar-Refactor, Bereichsseiten, Rechte).

## 7. Grobe Themenraum-Kandidaten (nur Hinweis — NICHT final, kein Zielbaum)
Als **erste grobe** Orientierung (bewusst ohne Unterpunkte, nicht beschlossen), Verdichtung von 15+13 Sektionen auf ~8–11 Themenräume, z. B.: **Arbeitsbereich/Start · CRM & Kontakte · Vertrieb (Angebote/Aufträge) · Projekte & Baustelle · Kundendienst & Einsatz · Lager & Artikel · Finanzen (inkl. Rechnungen) · Personal & HR · Controlling · Stammdaten · System/Admin**. Energie/PV je nach D4 als eigener Raum oder unter Vertrieb/Projekte. **Diese Liste ist Diskussionsinput, keine Entscheidung.**

## 8. Deliverables (Ergebnis des Tickets)
1. Ratifizierte IA-/Begriffs-Entscheidungen (D1–D7 beantwortet).
2. **Ein** Zielnavigations-Konzept (Themenräume + max. 4–5 Unterpunkte je Raum) als Dokument.
3. Zuordnungstabelle „heutiges Menü/Modul → Zielraum → Bereichsseite/Tab" (inkl. „bleibt versteckt/entfällt/Legacy").
4. Rechte-Zielmodell (inkl. Quick-Menü-/Dashboard-Absicherung).
5. Migrations-/Legacy-Plan (Reihenfolge, Verzahnung S1-09/S1-10, Link-Kompatibilität).

## 9. Definition of Done
- D1–D7 entschieden und dokumentiert.
- Zielnavigations-Konzept vollständig, alle Leitplanken (§4) eingehalten (insb. 4–5-Regel, Rechnungen→Finanzen, DATEV-Ehrlichkeit, gesicherte Quick-Aktionen).
- Jedes heutige Menü/Modul ist im Konzept zugeordnet (Zielraum / Bereichsseite / versteckt / Legacy) — nichts „vergessen".
- Migrations-/Legacy-Plan liegt vor, verzahnt mit S1-09/S1-10.
- **Kein** Code/Route/Menü geändert; nur additive Konzeptdokumente.

## 10. Risiken & Guards
| Risiko | Guard |
|---|---|
| Playground-Prototyp als „fertig" übernommen | nur real Nutzbares sichtbar; gesperrte/Prototyp-Bereiche kennzeichnen (D5) |
| Zielnavi wieder überladen | harte 4–5-Regel; Details in Bereichsseiten/Tabs |
| Sensible Bereiche ungeschützt (heute Quick-Menüs ungated) | Rechte-Zielmodell (D3) + Absicherung als Deliverable |
| Vorzeitiger Umbau | Ticket ist reine Konzeptarbeit; Umsetzung nur in Folge-Tickets nach Freigabe |
| Begriffs-Wildwuchs | verbindliches `glossar.md` (D7) |
| Konzept veraltet gegen sich änderndes Playground | Playground-Stand fixieren (D6) |

## 11. Abgrenzung
Dieses Ticket **plant** die Zielnavigation. Es **ändert nichts** an Sidebar, Routen, Blades, Controllern oder Rechten. Umsetzung erfolgt ausschließlich in separaten, nach Konzept-Freigabe erstellten Tickets (u. a. Verzahnung mit **S1-09** UI-Konsolidierung und **S1-10** Legacy/Cleanup).

---
`Dieses Ticket ist ein Konzept-/Planungsauftrag. Es ist kein Umsetzungs- oder Umbau-Auftrag.`
