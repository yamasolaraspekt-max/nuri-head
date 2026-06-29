# Navi-Schwächen-Untersuchung — Plan & Methodik (Planer-Dokument)

**Zweck:** Das System **Navi-Punkt für Navi-Punkt** systematisch auf Schwächen prüfen, alles in
**einem** Format sammeln, daraus **eine Übersicht** schaffen und sie **vor** Arbeitsbeginn in
abarbeitbare **Pakete** schneiden. Danach: ein Paket nach dem anderen, jeder Fix einzeln
verifiziert + committet.

**Abgrenzung zum bisherigen Audit:** Das frühere Navi-Audit (docs/navigation-konzept-audit.md,
25 Agenten) hat *Reifegrad* je Punkt bewertet. Diese Untersuchung ist **umsetzungs-orientiert**:
konkrete Defekte mit Repro + Fix-Skizze, jetzt mit den Seeder-Profilen auch **real testbar**
(einloggen, Seite laden, Buttons klicken), nicht nur Code-Lesen.

---

## Phasenmodell

| Phase | Was | Wer | Output |
|--|--|--|--|
| **0 Inventar** | Komplette aktuelle Navi-Struktur extrahieren (Quelle: `groups.json` + Sidebar-Blade): jede Sektion → Hauptpunkt → Untermenü mit route-name, URL, Controller@Methode, View, count_key, benötigtem Recht (user_roll-Item). | 1 Agent | Master-Index aller ~120 Punkte (Tabelle/JSON) |
| **1 Prüfung** | Jeder Bereichs-Agent geht **seine** Navi-Punkte einzeln durch, nach dem einheitlichen Raster unten. Real testen wo möglich (Profile A/B/C), sonst code-statisch. | ~24 Agenten parallel (1 je Bereich, in Wellen von 10–12) | Fund-Zeilen im Sammelformat |
| **2 Konsolidierung** | Alle Funde in **eine** Master-CSV/MD; Dedup gegen Bekanntes; Heatmap je Bereich. | 1 Agent | `docs/navi-schwaechen-gesamt.{md,csv}` |
| **3 Paket-Schnitt** | Übersicht → Pakete nach Ausnutzbarkeit/Schwere/Abhängigkeit. **Hier stoppen, dem Nutzer vorlegen.** | **Planer** | Paket-Arbeitsliste |
| **4 Abarbeitung** | Paket für Paket, EIN Fix = ein Commit, einzeln verifiziert (jetzt real), Bericht → Abnahme. | Ausführer + Planer | gefixter Code |

---

## Einheitliches Prüfraster (pro Navi-Punkt — alle Agenten messen gleich)

1. **Erreichbarkeit** — Route + Controller-Methode + View existieren? Lädt 200 (nicht 404/500)?
   MAIN-Header ohne eigene URL als solchen markieren (kein Defekt, aber notieren).
2. **Auth/Recht** — In auth-Gruppe? Korrektes `user_roll`-Item abgefragt? Profil **C** gesperrt,
   **B**/Admin sichtbar? (real testen, wenn Seeder steht).
3. **CRUD/Aktionen** — Anlegen/Bearbeiten/Löschen funktionieren? Formular postet an die **richtige**
   Route (store vs update)? Tote Buttons (kein JS-Handler)? GET für Destroy/Restore (CSRF)?
4. **Workflow/Daten** — Kein Status-Reset/Datenverlust? Rückbuchungen/Dekremente korrekt?
   Drag/Status-Update-Endpunkte vorhanden? Doppel-Submit?
5. **Validierung** — Eingaben serverseitig validiert (nicht `$request->all()` blind)?
6. **Sicherheit** — Keine Klartext-Secrets im View/DOM, keine öffentlich erreichbare Daten-Route.
7. **Konsistenz/UX** — DE/EN-Mix, fehlender Empty-State, Label↔Inhalt-Bruch, fehlende Pagination,
   falscher Breadcrumb.
8. **Beleg** — Jeder Fund mit `Datei:Zeile` und – wenn real getestet – einem Repro-Schritt.

## Sammelformat (CSV-Spalten — verbindlich, sonst läuft nichts sauber zusammen)

`navi_pfad ; route_name ; controller@methode ; view ; schwere ; typ ; befund ; datei:zeile ; repro ; fix_skizze ; aufwand ; dedup_status`

- **schwere:** 🔴 kritisch · 🟠 hoch · 🟡 mittel · ⚪ niedrig
- **typ:** Erreichbarkeit · Auth/Recht · CRUD/Aktion · Route-falsch · Workflow/Datenverlust ·
  Validierung · Sicherheit · Konsistenz/UX · Architektur
- **aufwand:** S · M · L
- **dedup_status:** neu · bekannt · bereits-gefixt (Abgleich gegen die Quellen unten)

## Dedup-Quellen (nicht doppelt melden)

- `docs/software-audit/befunde-bestaetigt.csv` (131 bestätigte schwere Funde)
- `docs/stabilitaet-fixliste.md`, `docs/stabilitaet-routing-workflow.md`
- `docs/stabilitaet-p1-arbeitsliste.md` (Paket 0/1 teils erledigt — git log gegenprüfen)
- `docs/navigation-konzept-audit.md` (Reifegrad-Funde)

## Paket-Reihenfolge-Prinzip (für Phase 3)

P0 Sicherheit (anonym/Recht) → P1 Crashes (500/404/tote Pfade) → P2 Datenverlust/Workflow →
P3 CRUD-/Route-Fixes → P4 Konsistenz/UX → P5 Architektur. Jedes Paket = abgeschlossene,
einzeln verifizierbare Einheit; Reihenfolge nach „Schaden wenn ungefixt".

## Bereichs-Aufteilung (1 Agent je Bereich, ~24)

Arbeitsbereich · Berichte · CRM›Anfragen · CRM›Leads/Kunden · CRM›Kommunikation · CRM›Partner ·
Vertrieb›Angebote · Vertrieb›Aufträge · Projekte›Termine · Projekte›Notizen · Projekte›Wartung ·
Projekte›Einzelseiten · Support›Tickets · Personal›Mitarbeiter · Personal›Organisation ·
Personal›HR-Daten · Personal›Einzelseiten · Artikel›Artikel · Artikel›Artikel-Daten · Lager ·
Finanzen · Admin›Benutzer · Admin›System · System/Sonstiges.

> Diese Untersuchung ist rein analytisch (read-only). Es wird **nichts** am Code geändert.
> Fixes erst ab Phase 4, paketweise, nach Freigabe.
